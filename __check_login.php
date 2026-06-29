<?php
require 'includes/config.php';
$pdo = db();
$stmt = $pdo->prepare("SELECT id, username, password, email_verified, is_active FROM users WHERE username = ? LIMIT 1");
$stmt->execute(['admin']);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    echo "ADMIN_ROW=missing\n";
} else {
    echo "ADMIN_ROW=found\n";
    echo "PASSWORD_VERIFY=" . (password_verify('Admin@1234', $user['password']) ? 'yes' : 'no') . "\n";
    echo "LEGACY_VERIFY=" . (password_verify('password', $user['password']) ? 'yes' : 'no') . "\n";
    echo "EMAIL_VERIFIED=" . ($user['email_verified'] ?? 'null') . "\n";
    echo "IS_ACTIVE=" . ($user['is_active'] ?? 'null') . "\n";
}
