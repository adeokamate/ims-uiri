<?php
require_once __DIR__ . '/includes/config.php';

if (isLoggedIn()) {
    header('Location: ' . BASE_URL . 'pages/dashboard.php');
    exit;
}

$error = '';
$success = '';
$pdo = db();
$token = $_GET['token'] ?? '';
$user = null;

// Validate token
if ($token) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE password_reset_token = ? AND token_expiry > NOW() AND is_active = 1");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if (!$user) {
        $error = 'Invalid or expired password reset token. Please request a new one.';
    }
} else {
    $error = 'No password reset token provided.';
}

// Handle password reset form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user) {
    verifyCsrf();
    
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (!$password || !$confirmPassword) {
        $error = 'Please enter both password fields.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } elseif (!validatePassword($password, $error)) {
        // Error already set by validatePassword
    } else {
        // Update password and clear reset token
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $pdo->prepare("UPDATE users SET password = ?, password_reset_token = NULL, token_expiry = NULL, failed_login_attempts = 0 WHERE id = ?")
            ->execute([$hash, $user['id']]);
        
        auditLog('PASSWORD_RESET', 'users', $user['id'], 'User reset their password');
        
        $success = 'Your password has been reset successfully. You can now sign in with your new password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — UIRI IMS</title>
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
            <h2>Reset Your Password</h2>
            <p>Create a new password for your account.</p>
        </div>
    </div>

    <div class="login-panel-right">
        <div class="login-form-wrap">
            <div class="login-form-header">
                <h3>Create New Password</h3>
                <p>Enter your new password below</p>
            </div>

            <?php if ($error): ?>
            <div class="alert alert-error">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <?= clean($error) ?>
            </div>
            <?php endif; ?>

            <?php if ($success): ?>
            <div class="alert alert-success">
                <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                <?= clean($success) ?>
                <p style="margin-top:10px;"><a href="index.php" class="btn btn-sm btn-primary">Go to Login</a></p>
            </div>
            <?php elseif ($user): ?>
            <form method="POST" novalidate>
                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                
                <div class="form-group">
                    <label for="password">New Password</label>
                    <div class="input-wrap">
                        <svg class="input-icon" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                        <input type="password" id="password" name="password" placeholder="Enter new password" required>
                        <button type="button" class="toggle-password" onclick="togglePwd(this)" aria-label="Show password">
                            <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="input-wrap">
                        <svg class="input-icon" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm password" required>
                        <button type="button" class="toggle-password" onclick="togglePwd(this)" aria-label="Show password">
                            <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>

                <p style="font-size:13px;color:#666;margin:15px 0;">Password requirements:
                    <ul style="margin:5px 0;padding-left:20px;">
                        <li>At least 8 characters</li>
                        <li>At least one uppercase letter (A-Z)</li>
                        <li>At least one lowercase letter (a-z)</li>
                        <li>At least one number (0-9)</li>
                    </ul>
                </p>

                <button type="submit" class="btn btn-primary btn-block">
                    Reset Password
                </button>

                <div class="login-help">
                    <p><a href="index.php">Back to login</a></p>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function togglePwd(btn) {
    const input = btn.previousElementSibling;
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>
