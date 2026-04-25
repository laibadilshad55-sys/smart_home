<?php
include 'db.php';
header('Content-Type: application/json');

$result = $conn->query("SELECT COUNT(*) as c FROM devices WHERE status IN ('ON','OPEN')");

if ($result) {
    $row = $result->fetch_assoc();
    echo json_encode(['active' => (int)$row['c']]);
} else {
    echo json_encode(['error' => 'Query failed']);
}
?>