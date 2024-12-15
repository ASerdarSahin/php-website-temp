<!-- filepath: /php/profile_edit.php -->
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
include('connection.php');

// Fetch current user data
$user_id = $_SESSION['user_id'];
$sql = "SELECT username, email, phone FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>
    <h2>Edit Profile</h2>
    <form action="process_profile_update.php" method="POST">
        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
        <input type="password" name="password" placeholder="New Password (leave blank to keep current)">
        <button type="submit">Update Profile</button>
    </form>
    <p><a href="profile.php">Back to Profile</a></p>
    <?php include('footer.php'); ?>
</body>
</html>