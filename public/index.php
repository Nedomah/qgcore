<?php

require __DIR__ . '/../connections/conman.php';
require __DIR__ . '/../devtools/tools.php';

$pdo = ConMan::get();

// Plain query
$stmt = $pdo->prepare('SELECT * FROM Connections');
$stmt->execute();
$data = $stmt->fetchAll();

echo json_encode($data)

?>