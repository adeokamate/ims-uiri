<?php
require_once __DIR__ . '/config.php';
requireLogin();
requireRole('Administrator');
verifyCsrf();

$branchId = (int)($_POST['branch_id'] ?? 0);
$redirect = $_POST['redirect'] ?? BASE_URL . 'pages/dashboard.php';

// Validate branch exists
$stmt = db()->prepare("SELECT id FROM branches WHERE id = ?");
$stmt->execute([$branchId]);
if ($stmt->fetch()) {
    $_SESSION['user']['branch_id'] = $branchId;
    // Update branch info in session
    $b = db()->prepare("SELECT name FROM branches WHERE id = ?");
    $b->execute([$branchId]);
    $branch = $b->fetch();
    $_SESSION['user']['branch_name'] = $branch['name'];
    auditLog('SWITCH_BRANCH', 'branches', $branchId, "Switched to branch ID $branchId");
    setFlash('success', 'Branch switched to ' . $branch['name']);
}

header('Location: ' . $redirect);
exit;
