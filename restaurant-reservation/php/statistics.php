<?php
session_start();

// Logged in and admin role check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
include('connection.php');

// Fetch reservation statistics grouped by date and ascending
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
            <?php while ($stat = $stats->fetch_assoc()): ?> <!-- Loop through each statistic -->
                <tr>
                    <td><?php echo $stat['date']; ?></td>
                    <td><?php echo $stat['total_reservations']; ?></td>
                </tr>
            <?php endwhile; ?> <!-- End of statistics loop -->
        </table>
        <div class="button-container" style="text-align: center; margin-top: 20px;">
            <button onclick="window.location.href='admin_panel.php'" class="edit-button">Back to Admin Panel</button>
        </div>    
    </main>
    <?php include('footer.php'); ?>
</body>
</html>