<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();
requireRole('Administrator');
$roleId = (int)($_GET['role_id'] ?? 0);
$pdo = db();
$stmt = $pdo->prepare("SELECT permission_id FROM role_permissions WHERE role_id = ?");
$stmt->execute([$roleId]);
$rows = $stmt->fetchAll();
$ids = array_map(function($r){return (int)$r['permission_id'];}, $rows);
header('Content-Type: application/json');
echo json_encode($ids);
