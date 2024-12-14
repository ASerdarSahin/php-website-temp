<?php
header('Content-Type: application/json');
include('connection.php');

$table_id = $_GET['table_id'];

$stmt = $conn->prepare("SELECT id, slot_datetime FROM time_slots WHERE table_id = ? AND status = 'available' AND slot_datetime >= NOW()");
$stmt->bind_param('i', $table_id);
$stmt->execute();
$result = $stmt->get_result();

$time_slots = [];
while ($row = $result->fetch_assoc()) {
    $time_slots[] = $row;
}

echo json_encode($time_slots);

$conn->close();
?>