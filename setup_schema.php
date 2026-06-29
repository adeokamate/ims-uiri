<?php
/**
 * UIRI IMS - Database Schema Setup Script
 * Run this script once to apply all schema enhancements
 * Access via: http://localhost/uiri-ims/setup_schema.php
 */

require_once 'includes/config.php';

if ($_GET['confirm'] !== 'yes') {
    echo '<div style="font-family:sans-serif;padding:40px;max-width:600px;margin:50px auto;">
        <h2>UIRI IMS - Database Schema Setup</h2>
        <p>This script will apply all necessary schema enhancements to your database.</p>
        <p><strong>Changes will be made:</strong></p>
        <ul>
            <li>Add columns to users table (failed_login_attempts, password_reset_token, remember_token, etc.)</li>
            <li>Add columns to departments table (section_manager_id, department_manager_id)</li>
            <li>Add columns to notifications table (branch_id, link)</li>
            <li>Create database indexes for performance</li>
        </ul>
        <p><a href="?confirm=yes" style="background:#28a745;color:white;padding:10px 20px;border-radius:4px;text-decoration:none;">Proceed with Setup</a></p>
    </div>';
    exit;
}

$pdo = db();
$errors = [];
$success = [];

try {
    // 1. Add columns to users table
    $statements = [
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS failed_login_attempts INT DEFAULT 0 AFTER is_active",
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS last_login_attempt DATETIME DEFAULT NULL AFTER last_login",
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS password_reset_token VARCHAR(255) DEFAULT NULL AFTER last_login_attempt",
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS token_expiry DATETIME DEFAULT NULL AFTER password_reset_token",
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS remember_token VARCHAR(255) DEFAULT NULL AFTER token_expiry",
        
        // 2. Add columns to departments table
        "ALTER TABLE departments ADD COLUMN IF NOT EXISTS section_manager_id INT DEFAULT NULL AFTER description",
        "ALTER TABLE departments ADD COLUMN IF NOT EXISTS department_manager_id INT DEFAULT NULL AFTER section_manager_id",
        
        // 3. Modify notifications table to allow NULL user_id
        "ALTER TABLE notifications MODIFY user_id INT DEFAULT NULL",
        "ALTER TABLE notifications ADD COLUMN IF NOT EXISTS branch_id INT DEFAULT NULL AFTER user_id",
        "ALTER TABLE notifications ADD COLUMN IF NOT EXISTS link VARCHAR(255) DEFAULT NULL AFTER message",
        
        // 4. Add indexes
        "CREATE INDEX IF NOT EXISTS idx_users_email ON users(email)",
        "CREATE INDEX IF NOT EXISTS idx_users_username ON users(username)",
        "CREATE INDEX IF NOT EXISTS idx_users_branch ON users(branch_id)",
        "CREATE INDEX IF NOT EXISTS idx_users_role ON users(role_id)",
        "CREATE INDEX IF NOT EXISTS idx_inventory_items_branch ON inventory_items(branch_id)",
        "CREATE INDEX IF NOT EXISTS idx_inventory_items_category ON inventory_items(category_id)",
        "CREATE INDEX IF NOT EXISTS idx_inventory_items_supplier ON inventory_items(supplier_id)",
        "CREATE INDEX IF NOT EXISTS idx_inventory_items_code ON inventory_items(item_code)",
        "CREATE INDEX IF NOT EXISTS idx_stock_transactions_item ON stock_transactions(item_id)",
        "CREATE INDEX IF NOT EXISTS idx_stock_transactions_branch ON stock_transactions(branch_id)",
        "CREATE INDEX IF NOT EXISTS idx_stock_transactions_date ON stock_transactions(transaction_date)",
        "CREATE INDEX IF NOT EXISTS idx_inventory_requests_user ON inventory_requests(user_id)",
        "CREATE INDEX IF NOT EXISTS idx_inventory_requests_status ON inventory_requests(status)",
        "CREATE INDEX IF NOT EXISTS idx_inventory_requests_branch ON inventory_requests(branch_id)",
        "CREATE INDEX IF NOT EXISTS idx_transfers_from_branch ON transfers(from_branch_id)",
        "CREATE INDEX IF NOT EXISTS idx_transfers_to_branch ON transfers(to_branch_id)",
        "CREATE INDEX IF NOT EXISTS idx_transfers_status ON transfers(status)",
        "CREATE INDEX IF NOT EXISTS idx_notifications_user ON notifications(user_id)",
        "CREATE INDEX IF NOT EXISTS idx_notifications_branch ON notifications(branch_id)",
        "CREATE INDEX IF NOT EXISTS idx_notifications_type ON notifications(type)",
        "CREATE INDEX IF NOT EXISTS idx_audit_log_user ON audit_log(user_id)",
        "CREATE INDEX IF NOT EXISTS idx_audit_log_action ON audit_log(action)",
        "CREATE INDEX IF NOT EXISTS idx_audit_log_created ON audit_log(created_at)",
    ];
    
    foreach ($statements as $sql) {
        try {
            $pdo->exec($sql);
            $success[] = "✓ " . substr($sql, 0, 60) . "...";
        } catch (Exception $e) {
            $errors[] = substr($sql, 0, 60) . "... (" . $e->getMessage() . ")";
        }
    }
    
    // 5. Ensure all 5 roles exist
    $roles = [
        'Administrator' => 'Full system access across all branches',
        'Campus Manager' => 'Manage operations for assigned campus',
        'Store Manager' => 'Manage inventory and stock for assigned branch',
        'Section Manager' => 'Manage section inventory and requests',
        'Staff' => 'View inventory and request items'
    ];
    
    foreach ($roles as $name => $desc) {
        $check = $pdo->prepare("SELECT id FROM roles WHERE name = ?");
        $check->execute([$name]);
        if (!$check->fetch()) {
            $pdo->prepare("INSERT INTO roles (name, description) VALUES (?, ?)")->execute([$name, $desc]);
            $success[] = "✓ Added role: $name";
        }
    }
    
    echo '<div style="font-family:sans-serif;padding:40px;max-width:600px;margin:50px auto;">
        <h2 style="color:#28a745;">✓ Setup Complete!</h2>';
    
    if (!empty($success)) {
        echo '<h3>Success:</h3><ul>';
        foreach ($success as $s) {
            echo '<li style="color:#28a745;">' . htmlspecialchars($s) . '</li>';
        }
        echo '</ul>';
    }
    
    if (!empty($errors)) {
        echo '<h3>Notes/Errors:</h3><ul>';
        foreach ($errors as $e) {
            echo '<li style="color:#ffc107;">' . htmlspecialchars($e) . '</li>';
        }
        echo '</ul>';
    }
    
    echo '<p><a href="index.php" style="background:#007bff;color:white;padding:10px 20px;border-radius:4px;text-decoration:none;">Go to Login</a></p>
    </div>';
    
} catch (Exception $e) {
    echo '<div style="font-family:sans-serif;padding:40px;background:#fff0f0;border-left:4px solid #e53e3e;margin:20px;">
        <h3 style="color:#e53e3e;">Setup Failed</h3>
        <p>' . htmlspecialchars($e->getMessage()) . '</p>
    </div>';
}
?>
