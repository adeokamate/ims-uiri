# UIRI IMS - Security Enhancement Report

## Overview
This document outlines all security improvements implemented for the UIRI Inventory Management System.

## Security Enhancements Implemented

### 1. Email Verification for Registration
**What Changed:**
- New users must verify their email before they can log in
- Email verification tokens expire after 24 hours
- Verification page at `verify-email.php` handles token validation

**Database Changes:**
- Added `email_verified` (TINYINT, default 0)
- Added `email_verification_token` (VARCHAR 255)
- Added `email_verification_expiry` (DATETIME)
- Added `signup_ip_address` (VARCHAR 45) - tracks signup IP for audit

**How It Works:**
1. User registers and receives email with verification link
2. Link contains secure token valid for 24 hours
3. User clicks link to verify email
4. Only then can user log in
5. Fallback: Links displayed on registration confirmation if email send fails

---

### 2. Rate Limiting & Brute Force Protection
**What Changed:**
- IP-based rate limiting on both login and registration
- Prevents automated attacks and account enumeration
- New `rate_limits` table tracks attempts

**Limits Configured:**
- **Login:** Max 10 failed attempts per IP in 15 minutes (900 seconds)
- **Registration:** Max 3 registration attempts per IP per hour (3600 seconds)
- **Rate Limit Window:** Records auto-cleanup after 1 hour

**Features:**
- Tracks by IP address (with proxy support for X-Forwarded-For)
- Failed login attempts counted per user account
- Account locks after 5 failed password attempts (30-minute lockout)
- Clear distinction between IP rate limits and per-account lockouts

**Database Structure:**
```sql
CREATE TABLE rate_limits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    identifier VARCHAR(255) NOT NULL,
    action VARCHAR(50) DEFAULT 'login',
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_identifier_action (identifier, action),
    INDEX idx_created (created_at)
);
```

---

### 3. Account Lockout Protection
**Features:**
- Per-user account lockout after 5 failed password attempts
- 30-minute lockout period (automatic after time passes)
- Admin can manually unlock via database query
- Prevents brute force attacks on specific accounts
- Failed login attempts tracked in `users.failed_login_attempts`

**Helper Functions:**
- `isAccountLocked(int $userId)` - check if account is locked
- `unlockAccount(int $userId)` - reset failed attempts

---

### 4. Session Security Hardening
**What Changed:**
- Secure session configuration in `includes/config.php`
- HTTP-only cookies (prevents JavaScript access)
- Session ID regeneration every 10 minutes
- Strict session mode (reject uninitialized session IDs)
- CSRF protection via session tokens

**Configuration:**
```php
ini_set('session.use_strict_mode', 1);           // Reject invalid session IDs
ini_set('session.use_only_cookies', 1);          // No URL-based sessions
ini_set('session.cookie_httponly', 1);           // Prevent JS access
ini_set('session.cookie_samesite', 'Lax');       // CSRF protection
ini_set('session.gc_maxlifetime', 3600);         // 1 hour lifetime
ini_set('session.cookie_lifetime', 3600);
```

**Session Regeneration:**
- New session ID generated every 10 minutes
- Prevents session fixation attacks
- Transparent to users (automatic)

---

### 5. Security Headers
**Headers Added:**
- `X-Content-Type-Options: nosniff` - Prevent MIME-type sniffing
- `X-Frame-Options: SAMEORIGIN` - Prevent clickjacking
- `X-XSS-Protection: 1; mode=block` - XSS protection
- `Referrer-Policy: strict-origin-when-cross-origin` - Referrer control
- `Permissions-Policy` - Disable unused features (geolocation, camera, etc.)
- `Content-Security-Policy` - Controls resource loading (script-src, style-src, etc.)

**Applied To:**
- Login page (`index.php`)
- Registration page (`register.php`)
- Email verification page (`verify-email.php`)
- All authenticated pages (via `includes/header.php`)

---

### 6. Email Validation & Protection
**What Changed:**
- Enhanced email validation using `FILTER_VALIDATE_EMAIL`
- Disposable email detection (prevents spam registrations)
- Email strength validation function

**Blocked Domains:**
- `tempmail.com`, `guerrillamail.com`, `mailinator.com`
- `temp-mail.org`, `throwaway.email`, `maildrop.cc`

**Function:**
```php
validateEmailStrength(string $email): bool
// Validates email format and checks against disposable domain list
```

---

### 7. IP Address Tracking & Detection
**What Changed:**
- Proper IP detection considering proxies
- Tracks IP on signup (`signup_ip_address` column)
- Support for X-Forwarded-For headers
- Used for rate limiting and audit logging

**Helper Function:**
```php
getUserIpAddress(): string
// Returns proper IP considering proxy headers
```

---

### 8. Password Security
**Existing Features (Enhanced):**
- Password hashing with `PASSWORD_BCRYPT` algorithm
- Auto-generation of strong passwords (12 characters with mixed case, numbers, symbols)
- Password reset via tokens with expiry (1 hour)
- Password strength validation

**New Requirements:**
- Passwords validated on registration/reset
- Minimum 8 characters
- Must contain uppercase, lowercase, numbers, and symbols

---

### 9. Audit Logging
**What Changed:**
- Enhanced audit logging for security events
- New audit events:
  - `EMAIL_VERIFIED` - When user verifies email
  - `REGISTER_USER` - User registration with status
  - Rate limit attempts tracked in rate_limits table

**Audit Trail:**
```sql
SELECT * FROM audit_log 
WHERE action IN ('LOGIN', 'REGISTER_USER', 'EMAIL_VERIFIED', 'LOGIN_ATTEMPT')
ORDER BY created_at DESC;
```

---

## Database Migrations Required

**Run the following SQL to update your database:**

```sql
-- Add email verification columns to users table
ALTER TABLE users ADD COLUMN IF NOT EXISTS email_verified TINYINT(1) DEFAULT 0;
ALTER TABLE users ADD COLUMN IF NOT EXISTS email_verification_token VARCHAR(255) DEFAULT NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS email_verification_expiry DATETIME DEFAULT NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS signup_ip_address VARCHAR(45) DEFAULT NULL;

-- Create rate limiting table
CREATE TABLE IF NOT EXISTS rate_limits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    identifier VARCHAR(255) NOT NULL,
    action VARCHAR(50) DEFAULT 'login',
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_identifier_action (identifier, action),
    INDEX idx_created (created_at)
);

-- Mark existing admin users as verified (since they're pre-created)
UPDATE users SET email_verified = 1, signup_ip_address = '127.0.0.1' 
WHERE email_verified = 0 AND created_at < DATE_SUB(NOW(), INTERVAL 1 DAY);
```

A migration file `security_upgrade.sql` has been created in the project root.

---

## New Files Created

1. **`security_upgrade.sql`** - Database migration for security features
2. **`verify-email.php`** - Email verification page for new registrations

## Modified Files

1. **`includes/config.php`** 
   - Added secure session configuration
   - Added session regeneration logic

2. **`includes/functions.php`**
   - Added `isRateLimited()` - Check rate limit status
   - Added `recordRateLimitAttempt()` - Record attempt
   - Added `generateEmailVerificationToken()` - Generate secure tokens
   - Added `isAccountLocked()` - Check account lockout
   - Added `unlockAccount()` - Reset failed attempts
   - Added `getUserIpAddress()` - Proper IP detection
   - Added `validateEmailStrength()` - Enhanced email validation

3. **`includes/header.php`**
   - Added security headers to all authenticated pages

4. **`index.php`** (Login page)
   - Added security headers
   - Added rate limiting on login attempts
   - Added email verification check
   - Added IP tracking

5. **`register.php`** (Registration page)
   - Added security headers
   - Added rate limiting on registration
   - Added email verification token generation
   - Added enhanced email validation
   - Added IP address tracking

6. **`pages/settings.php`**
   - Added SMTP configuration form fields
   - Can now persist SMTP settings to database

---

## Configuration Requirements

### SMTP Setup (for email delivery)
In `Settings > System Settings`, configure:
- **SMTP Host:** e.g., `smtp.mailtrap.io`, `smtp.gmail.com`, `smtp.office365.com`
- **SMTP Port:** Usually 587 (TLS) or 465 (SSL)
- **SMTP Username:** Your email account
- **SMTP Password:** App password or email password
- **From Email:** e.g., `noreply@yourdomain.com`
- **From Name:** e.g., `UIRI Inventory System`

### Recommended SMTP Services:
- **Mailtrap** (testing): https://mailtrap.io
- **Gmail/Google Workspace**: Use App Passwords
- **Office 365**: Use your organizational email
- **SendGrid/Mailgun**: Professional email services

### Local Testing:
For local development without SMTP:
1. Leave SMTP Host empty
2. System will use PHP's `mail()` function
3. Emails will appear in server mailbox or local mail handler
4. Verification/reset links will be displayed as fallback

---

## Testing Checklist

- [ ] Run `security_upgrade.sql` migration
- [ ] Test user registration:
  - [ ] Verify email verification link is sent/displayed
  - [ ] Verify email verification works
  - [ ] Verify account locked until email verified
- [ ] Test rate limiting:
  - [ ] Try login 10+ times to trigger IP rate limit
  - [ ] Try registration 3+ times to trigger registration limit
- [ ] Test account lockout:
  - [ ] Fail password 5 times to lock account
  - [ ] Verify lockout message appears
  - [ ] Wait 30 minutes or admin reset to unlock
- [ ] Test session security:
  - [ ] Session ID changes when logged in
  - [ ] Check HTTP-only cookie flag in browser
- [ ] Test SMTP (if configured):
  - [ ] Register new user and verify email received
  - [ ] Try password reset and verify link received
- [ ] Test security headers:
  - [ ] Check response headers with curl or browser dev tools
  - [ ] Verify no MIME type sniffing possible
  - [ ] Verify clickjacking protection active

---

## Admin Actions

### Reset Failed Login Attempts
```sql
UPDATE users SET failed_login_attempts = 0, last_login_attempt = NULL 
WHERE username = 'jsmith';
```

### Unlock Locked Account
```php
unlockAccount(123); // where 123 is user ID
```

### Mark Email as Verified
```sql
UPDATE users SET email_verified = 1 
WHERE id = 123;
```

### View Rate Limit Activity
```sql
SELECT identifier, action, ip_address, COUNT(*) as attempts, 
       MAX(created_at) as last_attempt
FROM rate_limits
GROUP BY identifier, action
ORDER BY last_attempt DESC
LIMIT 20;
```

### View Login Attempts
```sql
SELECT * FROM login_history 
WHERE success = 0 
ORDER BY created_at DESC 
LIMIT 50;
```

---

## Security Best Practices Implemented

1. ✅ **Defense in Depth** - Multiple layers of protection
2. ✅ **Rate Limiting** - Both IP and per-account limits
3. ✅ **Email Verification** - Prevent invalid email registrations
4. ✅ **Session Security** - Secure cookies and frequent regeneration
5. ✅ **CSRF Protection** - Token-based CSRF prevention
6. ✅ **Secure Headers** - HTTP security headers for defense
7. ✅ **Input Validation** - Email and domain validation
8. ✅ **Audit Logging** - Track all security events
9. ✅ **Account Lockout** - Protect against brute force
10. ✅ **Password Hashing** - BCRYPT with proper salting

---

## Remaining TODOs

1. **Composer Installation** 
   - Run `composer install` to install PHPMailer
   - Will enable reliable SMTP email delivery

2. **Production HTTPS Setup**
   - Set `session.cookie_secure = true` in config.php when HTTPS enabled
   - Update CSP header to enforce HTTPS

3. **Two-Factor Authentication** (Optional)
   - TOTP support via authenticator apps
   - Email-based 2FA

4. **IP Whitelisting** (Optional)
   - For sensitive admin accounts
   - Prevent access from unknown IPs

5. **Security Monitoring** (Optional)
   - Real-time alerting for suspicious activity
   - Dashboard for security metrics

---

## Support & Troubleshooting

**Q: Users can't register - getting rate limited immediately**
A: Check if IP detection is working correctly. Look at `rate_limits` table and verify IPs.

**Q: Email verification not working**
A: Ensure SMTP is configured in Settings or emails will only show links.

**Q: Users locked out - can't log in**
A: Use admin command above to reset failed attempts.

**Q: Session expires too quickly**
A: Adjust `session.gc_maxlifetime` in `includes/config.php` (in seconds)

---

**Last Updated:** 2026-06-23
**Security Version:** 2.0
