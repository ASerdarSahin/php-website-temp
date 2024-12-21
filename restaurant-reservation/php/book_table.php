<?php
session_start();
header('Content-Type: application/json');

// user login check
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

include('connection.php');

// Retrieve POST data
$timeslot_id = $_POST['timeslot_id'];
$user_id = $_SESSION['user_id'];

try {
    // Start transaction
    $conn->begin_transaction();

    // Check if the time slot is still available
    $checkSql = "SELECT * FROM time_slots WHERE id = ? AND status = 'available'";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param('i', $timeslot_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Time slot is no longer available');
    }

    // Get time slot details
    $time_slot = $result->fetch_assoc();
    $table_id = $time_slot['table_id'];
    $slot_datetime = $time_slot['slot_datetime'];
    $date = date('Y-m-d', strtotime($slot_datetime));
    $time = date('H:i:s', strtotime($slot_datetime));

    // Set restaurant_id (assuming single restaurant with id = 1 !!!)
    $restaurant_id = 1;

    // Generate a unique confirmation number
    do {
        $confirmation_number = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
        // Check for uniqueness
        $confCheckSql = "SELECT id FROM reservations WHERE confirmation_number = ?";
        $confCheckStmt = $conn->prepare($confCheckSql);
        $confCheckStmt->bind_param('s', $confirmation_number);
        $confCheckStmt->execute();
        $confCheckResult = $confCheckStmt->get_result();
    } while ($confCheckResult->num_rows > 0);

    // Insert reservation with confirmation number
    $sql = "INSERT INTO reservations (user_id, restaurant_id, table_id, date, time, confirmation_number) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iiisss', $user_id, $restaurant_id, $table_id, $date, $time, $confirmation_number);
    $stmt->execute();

    // Update time slot status
    $updateSql = "UPDATE time_slots SET status = 'reserved' WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param('i', $timeslot_id);
    $updateStmt->execute();

    // Check if there are any remaining available time slots for the table
    $checkSlotsSql = "SELECT COUNT(*) AS available_slots FROM time_slots WHERE table_id = ? AND status = 'available'";
    $checkSlotsStmt = $conn->prepare($checkSlotsSql);
    $checkSlotsStmt->bind_param('i', $table_id);
    $checkSlotsStmt->execute();
    $slotsResult = $checkSlotsStmt->get_result();
    $slotsData = $slotsResult->fetch_assoc();

    if ($slotsData['available_slots'] > 0) {
        // There are still available time slots; ensure table status is 'available'
        $updateTableSql = "UPDATE tables SET status = 'available' WHERE id = ?";
    } else {
        // No available time slots left; set table status to 'unavailable'
        $updateTableSql = "UPDATE tables SET status = 'unavailable' WHERE id = ?";
    }
    $updateTableStmt = $conn->prepare($updateTableSql);
    $updateTableStmt->bind_param('i', $table_id);
    $updateTableStmt->execute();

    // Commit transaction
    $conn->commit();

    // Send a single JSON response with confirmation number
    echo json_encode([
        'success' => true,
        'message' => 'Reservation successful!',
        'confirmation_number' => $confirmation_number
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally { // Close connection
    $conn->close(); 
}
?>