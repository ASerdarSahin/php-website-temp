<?php
// filepath: /php/check_reservation.php
include('connection.php');

$confirmation_number = '';
$reservation = null;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $confirmation_number = $_POST['confirmation_number'];

    $sql = "SELECT r.id, r.date, r.time, t.id AS table_id, t.capacity, r.status
            FROM reservations r
            JOIN tables t ON r.table_id = t.id
            WHERE r.confirmation_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $confirmation_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $reservation = $result->fetch_assoc();
    } else {
        $error = 'Reservation not found. Please check your confirmation number.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Check Reservation</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>
    <h2>Check Your Reservation</h2>
    <form method="POST">
        <input type="text" name="confirmation_number" placeholder="Confirmation Number" value="<?php echo htmlspecialchars($confirmation_number); ?>" required>
        <button type="submit">Check Reservation</button>
    </form>

    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php elseif ($reservation): ?>
        <h3>Reservation Details</h3>
        <p><strong>Reservation ID:</strong> <?php echo $reservation['id']; ?></p>
        <p><strong>Date:</strong> <?php echo $reservation['date']; ?></p>
        <p><strong>Time:</strong> <?php echo $reservation['time']; ?></p>
        <p><strong>Table ID:</strong> <?php echo $reservation['table_id']; ?></p>
        <p><strong>Table Capacity:</strong> <?php echo $reservation['capacity']; ?> seats</p>
        <p><strong>Status:</strong> <?php echo ucfirst($reservation['status']); ?></p>
    <?php endif; ?>

    <p><a href="../index.php">Back to Home</a></p>
</body>
</html>