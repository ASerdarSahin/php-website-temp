<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
include('connection.php');

$user_id = $_SESSION['user_id'];
$sql = "SELECT r.id, t.id AS table_id, r.date, r.time, r.status
        FROM reservations r
        JOIN tables t ON r.table_id = t.id
        WHERE r.user_id = ? AND r.status = 'active'";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Reservations</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h2>My Reservations</h2>
    <table>
        <tr>
            <th>Reservation ID</th>
            <th>Table ID</th>
            <th>Date</th>
            <th>Time</th>
            <th>Action</th>
        </tr>
        <?php while ($reservation = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $reservation['id']; ?></td>
                <td><?php echo $reservation['table_id']; ?></td>
                <td><?php echo $reservation['date']; ?></td>
                <td><?php echo $reservation['time']; ?></td>
                <td>
                    <form action="cancel_reservation.php" method="POST" onsubmit="return confirm('Are you sure you want to cancel this reservation?');">
                        <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                        <button type="submit">Cancel</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
    <p><a href="../index.php">Back to Home</a></p>
</body>
</html>