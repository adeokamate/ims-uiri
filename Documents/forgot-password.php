<?php
require_once __DIR__ . '/includes/config.php';

if (isLoggedIn()) {
    header('Location: ' . BASE_URL . 'pages/dashboard.php');
    exit;
}

$error = '';
$success = '';
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    
    $email = trim($_POST['email'] ?? '');
    
    if (!$email) {
        $error = 'Please enter your email address.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Generate reset token (64 character random string)
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store token in database
            $pdo->prepare("UPDATE users SET password_reset_token = ?, token_expiry = ? WHERE id = ?")->execute([
                $token,
                $expiry,
                $user['id']
            ]);
            
            $resetLink = BASE_URL . 'reset-password.php?token=' . urlencode($token);
            
            // Log the password reset request
            auditLog('PASSWORD_RESET_REQUEST', 'users', $user['id'], "Password reset requested for email: $email");
            
            $success = 'A password reset link has been generated. You can use the link below to reset your password (valid for 1 hour):<br><br>
                        <code style="background:#f5f5f5;padding:10px;display:block;margin:10px 0;border-radius:4px;word-break:break-all;">' . htmlspecialchars($resetLink) . '</code><br>
                        <p><strong>Note:</strong> In a production environment, this link would be sent to your email address.</p>';
        } else {
            // Security: Don't reveal if email exists
            $success = 'If an account exists with that email, a password reset link will be sent shortly.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — UIRI IMS</title>
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
            <p>Enter your email address and we'll send you a link to reset your password.</p>
        </div>
    </div>

    <div class="login-panel-right">
        <div class="login-form-wrap">
            <div class="login-form-header">
                <h3>Forgot Password?</h3>
                <p>Enter your email address</p>
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
                <?= $success ?>
            </div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrap">
                        <svg class="input-icon" viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 6l10 7 10-7"/></svg>
                        <input type="email" id="email" name="email" placeholder="Enter your email address" 
                               value="<?= clean($_POST['email'] ?? '') ?>" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    Send Reset Link
                </button>

                <div class="login-help">
                    <p><a href="index.php">Back to login</a> | <a href="register.php">Create an account</a></p>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
