<?php
require_once __DIR__ . '/includes/config.php';

// Redirect root requests to the landing page, while keeping login as /login.php.
if (!isset($_SERVER['REQUEST_URI']) || $_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '/uiri-ims/' || $_SERVER['REQUEST_URI'] === '/uiri-ims') {
    header('Location: ' . BASE_URL . 'pages/landing.html');
    exit;
}

// If a visitor goes to /login.php, show the login form.
if (basename($_SERVER['PHP_SELF']) === 'login.php') {
    require __DIR__ . '/login.php';
    exit;
}

// Otherwise serve the previous login page from index.php.
require __DIR__ . '/login.php';
exit;
