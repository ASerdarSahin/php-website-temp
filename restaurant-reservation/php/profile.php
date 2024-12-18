<?php
// filepath: /php/profile.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
include('connection.php');

$user_id = $_SESSION['user_id'];

// Get the current page number from GET parameter, default to 1
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$records_per_page = 20;
$offset = ($page - 1) * $records_per_page;

// Fetch total number of reservations for pagination
$total_reservations_sql = "
    SELECT COUNT(*) AS total
    FROM reservations
    WHERE user_id = ?
";
$stmt_total = $conn->prepare($total_reservations_sql);
$stmt_total->bind_param('i', $user_id);
$stmt_total->execute();
$total_reservations_result = $stmt_total->get_result();
$total_reservations_row = $total_reservations_result->fetch_assoc();
$total_reservations = $total_reservations_row['total'];
$total_pages = ceil($total_reservations / $records_per_page);

// Fetch reservations with pagination and ordering
$sql = "
    SELECT r.id, t.id AS table_id, r.date, r.time, r.status, r.confirmation_number
    FROM reservations r
    JOIN tables t ON r.table_id = t.id
    WHERE r.user_id = ?
    ORDER BY 
        CASE 
            WHEN r.status = 'active' THEN 1
            ELSE 2
        END ASC,
        r.date DESC, r.time DESC
    LIMIT ? OFFSET ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('iii', $user_id, $records_per_page, $offset);
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
    <main class="main-content">
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

        <?php if (!empty($user_data['message'])): ?>
            <div class="promotion-message">
                <h3>Your Message</h3>
                <p><?php echo nl2br(htmlspecialchars($user_data['message'])); ?></p>
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

        <!-- Pagination Links -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="profile.php?page=<?php echo $page - 1; ?>">Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php if ($i == $page): ?>
                    <strong><?php echo $i; ?></strong>
                <?php else: ?>
                    <a href="profile.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="profile.php?page=<?php echo $page + 1; ?>">Next</a>
            <?php endif; ?>
        </div>

        <div class="button-container" style="text-align: center; margin-top: 20px;">
            <button onclick="window.location.href='profile_edit.php'" class="edit-button">Edit Profile</button>
        </div>
    </main>
    <?php include('footer.php'); ?>
</body>
</html>