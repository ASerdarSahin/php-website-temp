<!-- filepath: /php/admin_panel.php -->
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
include('connection.php');

$sql = "SELECT r.id, r.user_id, t.id AS table_id, r.date, r.time, r.status
        FROM reservations r
        JOIN tables t ON r.table_id = t.id
        WHERE r.status = 'active'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Reservations</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h2>All Active Reservations</h2>
    <table>
        <tr>
            <th>Reservation ID</th>
            <th>User ID</th>
            <th>Table ID</th>
            <th>Date</th>
            <th>Time</th>
            <th>Action</th>
        </tr>
        <?php while ($reservation = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $reservation['id']; ?></td>
                <td><?php echo $reservation['user_id']; ?></td>
                <td><?php echo $reservation['table_id']; ?></td>
                <td><?php echo $reservation['date']; ?></td>
                <td><?php echo $reservation['time']; ?></td>
                <td>
                    <form action="cancel_reservation.php" method="POST" onsubmit="return confirm('Are you sure you want to cancel this reservation?');">
                        <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                        <input type="hidden" name="admin" value="1">
                        <button type="submit">Cancel</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
    <p><a href="../index.php">Back to Home</a></p>
</body>
</html>