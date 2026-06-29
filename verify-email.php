<?php
require_once __DIR__ . '/includes/config.php';

// Set security headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=()");

$error = '';
$success = '';
$pdo = db();

$token = trim($_GET['token'] ?? '');

if ($token) {
    $stmt = $pdo->prepare("
        SELECT id, email, full_name, email_verification_expiry FROM users
        WHERE email_verification_token = ? AND email_verified = 0
    ");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if (!$user) {
        $error = 'Invalid or expired verification link.';
    } elseif (strtotime($user['email_verification_expiry']) < time()) {
        $error = 'Verification link has expired. Please register again.';
    } else {
        // Mark email as verified
        $stmt = $pdo->prepare("
            UPDATE users 
            SET email_verified = 1, email_verification_token = NULL, email_verification_expiry = NULL
            WHERE id = ?
        ");
        $stmt->execute([$user['id']]);
        
        auditLog('EMAIL_VERIFIED', 'users', $user['id'], 'Email verified: ' . $user['email']);
        
        $success = 'Email verified successfully! You can now set your password using the link sent to your email.';
    }
} else {
    $error = 'No verification token provided.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email — UIRI IMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body class="login-body">
<div class="login-page">
    <div class="login-panel-left">
        <div class="login-brand">
            <div class="login-logo">
                <img src="<?= BASE_URL ?>assets/img/uiri-logo.webp" alt="UIRI Logo">
            </div>
            <div>
                <h1>UIRI IMS</h1>
                <p>Uganda Industrial Research Institute</p>
            </div>
        </div>

        <div class="login-tagline">
            <h2>Email Verification</h2>
            <p>Confirm your email address to activate your account.</p>
        </div>
    </div>

    <div class="login-panel-right">
        <div class="login-form-wrap">
            <div class="login-form-header">
                <h3>Verify Your Email</h3>
                <p>Email verification status</p>
            </div>

            <?php if ($error): ?>
            <div class="alert alert-error">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <?= clean($error) ?>
            </div>
            <div class="mt-3">
                <p><a href="register.php" class="btn btn-primary" style="display:inline-block;">Register Again</a></p>
                <p><a href="index.php">Back to Login</a></p>
            </div>
            <?php elseif ($success): ?>
            <div class="alert alert-success">
                <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                <?= clean($success) ?>
            </div>
            <div class="mt-3">
                <p>Next, you'll receive an email with a link to set your password.</p>
                <p><a href="index.php" class="btn btn-primary" style="display:inline-block;">Continue to Login</a></p>
            </div>
            <?php else: ?>
            <div class="alert alert-info">
                <p>Verifying your email address...</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
