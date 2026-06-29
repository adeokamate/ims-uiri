<?php
$pdo = new PDO('mysql:host=localhost;dbname=uiri_ims;charset=utf8mb4','root','', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
foreach (['sections', 'departments'] as $t) {
    echo "--- $t ---\n";
    $rows = $pdo->query("SHOW COLUMNS FROM $t")->fetchAll();
    foreach ($rows as $row) {
        echo $row['Field'] . ' ' . $row['Type'] . ' ' . ($row['Null'] === 'NO' ? 'NOT NULL' : 'NULL') . ' ' . ($row['Key'] ? $row['Key'] : '') . "\n";
    }
    echo "\n";
}
