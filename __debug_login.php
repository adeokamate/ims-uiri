<?php
require 'includes/config.php';
$pdo = db();
$cols = $pdo->query("SHOW COLUMNS FROM users LIKE 'email_verified'")->fetchAll();
echo 'EMAIL_VERIFIED_COLUMN=' . (count($cols) ? 'present' : 'missing') . PHP_EOL;
$stmt = $pdo->prepare("SELECT id, username, email, password, email_verified, is_active FROM users WHERE username = ? LIMIT 1");
$stmt->execute(['admin']);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    echo "ADMIN_ROW=missing\n";
} else {
    echo json_encode($user, JSON_UNESCAPED_SLASHES) . PHP_EOL;
    echo 'PASSWORD_VERIFY=' . (password_verify('Admin@1234', $user['password']) ? 'yes' : 'no') . PHP_EOL;
    echo 'LEGACY_VERIFY=' . (password_verify('password', $user['password']) ? 'yes' : 'no') . PHP_EOL;
}
