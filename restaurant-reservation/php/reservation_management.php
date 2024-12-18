<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'owner'])) {
    header('Location: login.php');
    exit();
}
include('connection.php');

// Determine return path and page title based on user role
$return_path = $_SESSION['role'] === 'admin' ? 'admin_panel.php' : 'owner_panel.php';
$page_title = $_SESSION['role'] === 'admin' ? 'Admin' : 'Owner' . ' - Reservation Management';

// Get the current page number from GET parameter, default to 1
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$records_per_page = 20;
$offset = ($page - 1) * $records_per_page;

// Fetch the total number of reservations to calculate total pages
$total_reservations_result = $conn->query("SELECT COUNT(*) AS total FROM reservations");
$total_reservations_row = $total_reservations_result->fetch_assoc();
$total_reservations = $total_reservations_row['total'];
$total_pages = ceil($total_reservations / $records_per_page);

// Fetch reservations with LIMIT and OFFSET, ordered by status
$reservations_sql = "
    SELECT r.id, r.user_id, t.id AS table_id, r.date, r.time, r.status, r.confirmation_number
    FROM reservations r
    JOIN tables t ON r.table_id = t.id
    ORDER BY 
        CASE 
            WHEN r.status = 'active' THEN 1
            ELSE 2
        END ASC,
        r.date DESC, r.time DESC
    LIMIT $records_per_page OFFSET $offset
";
$reservations = $conn->query($reservations_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>
    <main class="main-content">
        <h2>Reservation Management</h2>
        
        <?php if (isset($_GET['message'])): ?>
            <p class="success"><?php echo htmlspecialchars($_GET['message']); ?></p>
        <?php endif; ?>

        <table>
            <tr>
                <th>Reservation ID</th>
                <th>User ID</th>
                <th>Table ID</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Confirmation Number</th>
                <th>Action</th>
            </tr>
            <?php while ($reservation = $reservations->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $reservation['id']; ?></td>
                    <td><?php echo $reservation['user_id']; ?></td>
                    <td><?php echo $reservation['table_id']; ?></td>
                    <td><?php echo $reservation['date']; ?></td>
                    <td><?php echo $reservation['time']; ?></td>
                    <td><?php echo $reservation['status']; ?></td>
                    <td><?php echo htmlspecialchars($reservation['confirmation_number']); ?></td>
                    <td>
                        <?php if ($reservation['status'] === 'active'): ?>
                            <form action="cancel_reservation.php" method="POST" onsubmit="return confirm('Are you sure you want to cancel this reservation?');">
                                <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                <input type="hidden" name="<?php echo $_SESSION['role']; ?>" value="1">
                                <button type="submit">Cancel</button>
                            </form>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <!-- Pagination Links -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="reservation_management.php?page=<?php echo $page - 1; ?>">Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php if ($i == $page): ?>
                    <strong><?php echo $i; ?></strong>
                <?php else: ?>
                    <a href="reservation_management.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="reservation_management.php?page=<?php echo $page + 1; ?>">Next</a>
            <?php endif; ?>
        </div>
        
        <!-- Back Button -->
        <div class="button-container" style="text-align: center; margin-top: 20px;">
            <button onclick="window.location.href='<?php echo $return_path; ?>'" class="back-button">
                Back to <?php echo ucfirst($_SESSION['role']); ?> Panel
            </button>
        </div>
    </main>
    <?php include('footer.php'); ?>
</body>
</html>