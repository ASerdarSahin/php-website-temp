<?php
// filepath: /php/edit_user_message.php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'owner'])) {
    header('Location: login.php');
    exit();
}
include('connection.php');

$user_id = $_GET['id'];

// Fetch user data
$sql = "SELECT username, message FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $_POST['message'];

    $updateSql = "UPDATE users SET message = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param('si', $message, $user_id);
    $updateStmt->execute();

    header('Location: admin_panel.php?message=User message updated successfully');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User Message</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>
    <h2>Edit Message for <?php echo htmlspecialchars($user['username']); ?></h2>
    <form method="POST">
        <textarea name="message" rows="5" cols="50"><?php echo htmlspecialchars($user['message']); ?></textarea>
        <button type="submit">Update Message</button>
    </form>
    <p><a href="admin_panel.php">Back to Admin Panel</a></p>
</body>
</html>