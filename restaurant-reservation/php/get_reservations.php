<?php
header('Content-Type: application/json');

// Database connection
include('connection.php');

$user_id = $_GET['user_id']; // Pass this as a GET parameter
$role = $_GET['role']; // Either 'customer' or 'admin'

if ($role === 'customer') {
    // Query to fetch reservations for a specific customer
    $sql = "SELECT r.reservation_id, r.reservation_time, t.table_name, t.capacity, r.status
            FROM reservations r
            JOIN tables t ON r.table_id = t.table_id
            WHERE r.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
} else if ($role === 'admin') {
    // Query to fetch all reservations (for admins)
    $sql = "SELECT r.reservation_id, r.reservation_time, t.table_name, t.capacity, r.status, u.username
            FROM reservations r
            JOIN tables t ON r.table_id = t.table_id
            JOIN users u ON r.user_id = u.id";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

$reservations = [];
// Fetch the results into an array
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reservations[] = $row;
    }
}

echo json_encode($reservations); // Return the reservations as JSON

// Close the statement and connection
$stmt->close();
$conn->close();
?>
