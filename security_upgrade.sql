-- ============================================================
--  UIRI IMS - SECURITY UPGRADE
--  Adds email verification, rate limiting, and security features
-- ============================================================

-- Add email verification columns to users table
ALTER TABLE users ADD COLUMN IF NOT EXISTS email_verified TINYINT(1) DEFAULT 0 AFTER is_active;
ALTER TABLE users ADD COLUMN IF NOT EXISTS email_verification_token VARCHAR(255) DEFAULT NULL AFTER email_verified;
ALTER TABLE users ADD COLUMN IF NOT EXISTS email_verification_expiry DATETIME DEFAULT NULL AFTER email_verification_token;
ALTER TABLE users ADD COLUMN IF NOT EXISTS signup_ip_address VARCHAR(45) DEFAULT NULL AFTER email_verification_expiry;

-- Create rate limiting table for brute force protection
CREATE TABLE IF NOT EXISTS rate_limits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    identifier VARCHAR(255) NOT NULL,
    action VARCHAR(50) DEFAULT 'login',
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_identifier_action (identifier, action),
    INDEX idx_created (created_at)
);

-- Create session security log table (optional, for enhanced audit)
CREATE TABLE IF NOT EXISTS session_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    session_id VARCHAR(255),
    ip_address VARCHAR(45),
    user_agent VARCHAR(500),
    login_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    logout_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_session (user_id, session_id),
    INDEX idx_created (login_at)
);

-- Update existing records to mark them as verified (since they were pre-created by admin)
UPDATE users SET email_verified = 1, signup_ip_address = '127.0.0.1' WHERE email_verified = 0 AND created_at < DATE_SUB(NOW(), INTERVAL 1 DAY);

-- If needed later, update default emails to be verified:
-- UPDATE users SET email_verified = 1 WHERE username IN ('admin', 'jssemanda', 'gakello');
