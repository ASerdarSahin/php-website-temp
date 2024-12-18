<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
include('connection.php');

// Fetch reservation statistics
$stats = $conn->query("
    SELECT date, COUNT(*) as total_reservations
    FROM reservations
    GROUP BY date
    ORDER BY date ASC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Statistics</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>
    <main class="main-content">
        <h2>Reservation Statistics</h2>
        <table>
            <tr>
                <th>Date</th>
                <th>Total Reservations</th>
            </tr>
            <?php while ($stat = $stats->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $stat['date']; ?></td>
                    <td><?php echo $stat['total_reservations']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
        <div class="button-container" style="text-align: center; margin-top: 20px;">
            <button onclick="window.location.href='admin_panel.php'" class="edit-button">Back to Admin Panel</button>
        </div>    
    </main>
    <?php include('footer.php'); ?>
</body>
</html>