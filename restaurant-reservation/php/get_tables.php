<?php
session_start();
header('Content-Type: application/json');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

include('connection.php');

// Fetch available tables from the database
$sql = "SELECT id, capacity FROM tables WHERE status = 'available'";
$result = $conn->query($sql);

$tables = [];
while ($row = $result->fetch_assoc()) {
    $tables[] = $row;
}

echo json_encode($tables);

$conn->close();
?>