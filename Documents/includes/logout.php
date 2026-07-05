<?php
require_once __DIR__ . '/config.php';
if (isLoggedIn()) {
    auditLog('LOGOUT', 'users', $_SESSION['user_id'], 'User logged out');
}
session_destroy();
header('Location: ' . BASE_URL . 'index.php?msg=logged_out');
exit;
