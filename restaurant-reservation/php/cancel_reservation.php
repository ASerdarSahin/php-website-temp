<?php
session_start();
include('connection.php');

// user login check
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get data from POST request
$reservation_id = $_POST['reservation_id'];
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];
$is_admin = isset($_POST['admin']) && $_SESSION['role'] === 'admin';

try {
    // Start transaction
    $conn->begin_transaction();

    // Fetch reservation details
    $sql = "SELECT r.user_id, r.table_id, r.date, r.time
            FROM reservations r
            WHERE r.id = ? AND r.status = 'active'";

    // Prepare and execute statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $reservation_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if reservation exists and is active
    if ($result->num_rows === 0) {
        throw new Exception('Reservation not found or already canceled.');
    }

    $reservation = $result->fetch_assoc();

    // Check if user is authorized to cancel
    if (!$is_admin && $reservation['user_id'] != $user_id && $user_role !== 'owner') {
        throw new Exception('Unauthorized action.');
    }

    // Update reservation status to 'canceled'
    $updateSql = "UPDATE reservations SET status = 'canceled' WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param('i', $reservation_id);
    $updateStmt->execute();

    // Update time slot status to 'available'
    $slot_datetime = $reservation['date'] . ' ' . $reservation['time'];
    $getTimeSlotSql = "SELECT id FROM time_slots WHERE table_id = ? AND slot_datetime = ?";
    $getTimeSlotStmt = $conn->prepare($getTimeSlotSql);
    $getTimeSlotStmt->bind_param('is', $reservation['table_id'], $slot_datetime);
    $getTimeSlotStmt->execute();
    $slotResult = $getTimeSlotStmt->get_result();

    if ($slotResult->num_rows > 0) {
        $time_slot = $slotResult->fetch_assoc();
        $updateSlotSql = "UPDATE time_slots SET status = 'available' WHERE id = ?";
        $updateSlotStmt = $conn->prepare($updateSlotSql);
        $updateSlotStmt->bind_param('i', $time_slot['id']);
        $updateSlotStmt->execute();
    }

    // Check if table status needs to be updated based on available time slots
    $checkSlotsSql = "SELECT COUNT(*) AS available_slots FROM time_slots WHERE table_id = ? AND status = 'available'";
    $checkSlotsStmt = $conn->prepare($checkSlotsSql);
    $checkSlotsStmt->bind_param('i', $reservation['table_id']);
    $checkSlotsStmt->execute();
    $slotsResult = $checkSlotsStmt->get_result();
    $slotsData = $slotsResult->fetch_assoc();

    if ($slotsData['available_slots'] > 0) {
        // There are available time slots, set table status to 'available'
        $updateTableSql = "UPDATE tables SET status = 'available' WHERE id = ?";
        $updateTableStmt = $conn->prepare($updateTableSql);
        $updateTableStmt->bind_param('i', $reservation['table_id']);
        $updateTableStmt->execute();
    } else {
        // No available time slots, set table status to 'unavailable'
        $updateTableSql = "UPDATE tables SET status = 'unavailable' WHERE id = ?";
        $updateTableStmt = $conn->prepare($updateTableSql);
        $updateTableStmt->bind_param('i', $reservation['table_id']);
        $updateTableStmt->execute();
    }

    $conn->commit();

    // Redirect with success message
    if ($is_admin) {
        header('Location: admin_panel.php?message=Reservation canceled successfully');
    } else {
        header('Location: profile.php?message=Reservation canceled successfully');
    }
    exit();

} catch (Exception $e) { 
    $conn->rollback();
    $error_message = urlencode($e->getMessage());
    if ($is_admin) {
        header("Location: admin_panel.php?error=$error_message");
    } else {
        header("Location: profile.php?error=$error_message");
    }
    exit();
} finally { // Close statement and connection
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($updateStmt)) {
        $updateStmt->close();
    }
    $conn->close();
}
?>