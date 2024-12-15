<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
include('connection.php');

$user_id = $_SESSION['user_id'];
$sql = "SELECT r.id, t.id AS table_id, r.date, r.time, r.status, r.confirmation_number
        FROM reservations r
        JOIN tables t ON r.table_id = t.id
        WHERE r.user_id = ?
        ORDER BY r.date DESC, r.time DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch user's own data to get the message
$sql_user = "SELECT message FROM users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param('i', $user_id);
$stmt_user->execute();
$user_data = $stmt_user->get_result()->fetch_assoc();

// Fetch global promotion message
$promoSql = "SELECT promotion_message FROM restaurants WHERE id = 1";
$promoResult = $conn->query($promoSql);
$promoData = $promoResult->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Reservations</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>
    <?php if (isset($_GET['message'])): ?>
        <p class="success"><?php echo htmlspecialchars($_GET['message']); ?></p>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <p class="error"><?php echo htmlspecialchars($_GET['error']); ?></p>
    <?php endif; ?>
    <?php if (!empty($promoData['promotion_message'])): ?>
        <div class="promotion-message">
            <h3>Promotion Message</h3>
            <p><?php echo nl2br(htmlspecialchars($promoData['promotion_message'])); ?></p>
        </div>
    <?php endif; ?>

    <h2>My Reservations</h2>
    <table>
        <tr>
            <th>Reservation ID</th>
            <th>Table ID</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Confirmation Number</th>
            <th>Action</th>
        </tr>

        <?php if (!empty($user_data['message'])): ?>
            <div class="promotion-message">
                <h3>Promotion Message</h3>
                <p><?php echo nl2br(htmlspecialchars($user_data['message'])); ?></p>
            </div>
        <?php endif; ?>

        <?php while ($reservation = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $reservation['id']; ?></td>
                <td><?php echo $reservation['table_id']; ?></td>
                <td><?php echo $reservation['date']; ?></td>
                <td><?php echo $reservation['time']; ?></td>
                <td><?php echo ucfirst($reservation['status']); ?></td>
                <td><?php echo htmlspecialchars($reservation['confirmation_number']); ?></td>
                <td>
                    <?php if ($reservation['status'] === 'active'): ?>
                        <form action="cancel_reservation.php" method="POST" onsubmit="return confirm('Are you sure you want to cancel this reservation?');">
                            <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                            <button type="submit">Cancel</button>
                        </form>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
    <!-- Add link to edit profile -->
    <p><a href="profile_edit.php">Edit Profile</a></p>
    <p><a href="../index.php">Back to Home</a></p>
    <?php include('footer.php'); ?>
</body>
</html>