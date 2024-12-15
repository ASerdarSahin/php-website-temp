<?php
session_start();
header('Content-Type: application/json');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

include('connection.php');

// Validate and retrieve the table ID
if (!isset($_GET['table_id']) || !is_numeric($_GET['table_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid table ID']);
    exit();
}

$table_id = intval($_GET['table_id']);

// Fetch available time slots for the selected table
$stmt = $conn->prepare("SELECT id, slot_datetime FROM time_slots WHERE table_id = ? AND status = 'available' AND slot_datetime >= NOW()");
$stmt->bind_param('i', $table_id);
$stmt->execute();
$result = $stmt->get_result();

$time_slots = [];
while ($row = $result->fetch_assoc()) {
    $time_slots[] = $row;
}

echo json_encode($time_slots);

$stmt->close();
$conn->close();
?>