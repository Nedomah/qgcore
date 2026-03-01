<?php
require __DIR__ . '/../connections/conman.php';
require __DIR__ . '/../devtools/tools.php';

header('Content-Type: application/json');

$in_active = isset($_GET['in_active']) && $_GET['in_active'] === '1' ? 1 : 0;

// your PDO call here
$pdo = ConMan::getCore();

$stmt = $pdo->prepare("CALL stp_connections_select(?)");
$stmt->bindValue(1, $in_active, PDO::PARAM_BOOL);
$stmt->execute();

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($rows) && $stmt->nextRowset()) {
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC); // second set — usually has the data
}

// dd_1($rows);

// Tabulator expects plain array of objects
echo json_encode($rows);
?>