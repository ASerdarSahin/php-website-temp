<?php
session_start();

// Logged in and admin/owner role check
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'owner'])) {
    header('Location: login.php');
    exit();
}
include('connection.php');

// Determine return path and page title based on user role
$return_path = $_SESSION['role'] === 'admin' ? 'admin_panel.php' : 'owner_panel.php';
$page_title = $_SESSION['role'] === 'admin' ? 'Admin' : 'Owner' . ' - Promotion Messages';

// Fetch current promotion message
$promoSql = "SELECT promotion_message FROM restaurants WHERE id = 1";
$promoResult = $conn->query($promoSql);
$promoData = $promoResult->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $promotion_message = $_POST['promotion_message']; // Get the promotion message from POST data
    $updatePromoSql = "UPDATE restaurants SET promotion_message = ? WHERE id = 1";
    $updatePromoStmt = $conn->prepare($updatePromoSql);
    $updatePromoStmt->bind_param('s', $promotion_message); // Bind the promotion message parameter
    $updatePromoStmt->execute();

    // Redirect back to the promotion message page with a success message
    header("Location: promotion_message.php?role={$_SESSION['role']}&message=Promotion message updated successfully");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?></title> <!-- Set the page title dynamically -->
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>
    <main class="main-content">
        <h2>Set Global Promotion Message</h2>
        <?php if (isset($_GET['message'])): ?> <!-- Display success message if set -->
            <p class="success"><?php echo htmlspecialchars($_GET['message']); ?></p>
        <?php endif; ?>
        <form method="POST"> <!-- Form to update promotion message -->
            <textarea name="promotion_message" rows="5" cols="50"><?php echo htmlspecialchars($promoData['promotion_message']); ?></textarea><br>
            <button type="submit">Update Message</button>
        </form>

         <!-- Back Button -->
        <div class="button-container" style="text-align: center; margin-top: 20px;">
            <button onclick="window.location.href='<?php echo $return_path; ?>'" class="back-button">
                Back to <?php echo ucfirst($_SESSION['role']); ?> Panel <!-- button label based on role -->
            </button>
        </div>
    </main>
    <?php include('footer.php'); ?>
</body>
</html>