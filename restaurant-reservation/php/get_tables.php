<?php
header('Content-Type: application/json');
include('connection.php');

$sql = "SELECT id, capacity FROM tables WHERE status = 'available'";
$result = $conn->query($sql);

$tables = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $tables[] = $row;
    }
}

echo json_encode($tables);
$conn->close();
?>