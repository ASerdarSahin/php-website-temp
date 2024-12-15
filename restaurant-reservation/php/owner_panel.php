<!-- filepath: /php/owner_panel.php -->
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    header('Location: login.php');
    exit();
}
include('connection.php');

// Fetch upcoming reservations
$sql = "SELECT r.id, r.user_id, t.id AS table_id, r.date, r.time, r.status, r.confirmation_number, u.username
        FROM reservations r
        JOIN tables t ON r.table_id = t.id
        JOIN users u ON r.user_id = u.id
        WHERE r.date >= CURDATE()
        ORDER BY r.date, r.time";
$result = $conn->query($sql);

// Fetch current promotion message
$promoSql = "SELECT promotion_message FROM restaurants WHERE id = 1";
$promoResult = $conn->query($promoSql);
$promoData = $promoResult->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_promotion'])) {
    $promotion_message = $_POST['promotion_message'];
    $updatePromoSql = "UPDATE restaurants SET promotion_message = ? WHERE id = 1";
    $updatePromoStmt = $conn->prepare($updatePromoSql);
    $updatePromoStmt->bind_param('s', $promotion_message);
    $updatePromoStmt->execute();

    // Redirect to prevent form resubmission
    header('Location: owner_panel.php?message=Promotion message updated successfully');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Owner Panel</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>
    <h2>Owner Panel</h2>

    <!-- Upcoming Reservations -->
    <h3>Upcoming Reservations</h3>
    <table>
        <tr>
            <th>Reservation ID</th>
            <th>User</th>
            <th>Table ID</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Confirmation Number</th>
            <th>Action</th>
        </tr>
        <?php while ($reservation = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $reservation['id']; ?></td>
                <td><?php echo htmlspecialchars($reservation['username']); ?></td>
                <td><?php echo $reservation['table_id']; ?></td>
                <td><?php echo $reservation['date']; ?></td>
                <td><?php echo $reservation['time']; ?></td>
                <td><?php echo $reservation['status']; ?></td>
                <td><?php echo htmlspecialchars($reservation['confirmation_number']); ?></td>
                <td>
                    <?php if ($reservation['status'] === 'active'): ?>
                        <form action="cancel_reservation.php" method="POST" onsubmit="return confirm('Are you sure you want to cancel this reservation?');">
                            <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                            <input type="hidden" name="admin" value="1">
                            <button type="submit">Cancel</button>
                        </form>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
    <h3>Set Global Promotion Message</h3>
    <form method="POST">
        <textarea name="promotion_message" rows="5" cols="50"><?php echo htmlspecialchars($promoData['promotion_message']); ?></textarea><br>
        <button type="submit" name="update_promotion">Update Message</button>
    </form>

    <p><a href="../index.php">Back to Home</a></p>
</body>
</html>