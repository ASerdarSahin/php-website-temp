<?php
// filepath: /php/profile_edit.php
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
    <!-- Include Navbar -->
    <?php include('navbar.php'); ?>

    <!-- Main Content -->
    <main class="main-content">
        <h2>Edit Profile</h2>
        <?php if (isset($_GET['message'])): ?>
            <p class="success"><?php echo htmlspecialchars($_GET['message']); ?></p>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <p class="error"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>
        <form action="process_profile_update.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="phone">Phone:</label>
            <input type="tel" name="phone" id="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>

            <label for="password">New Password (leave blank to keep current):</label>
            <input type="password" name="password" id="password" placeholder="New Password (leave blank to keep current)">

            <button type="submit">Update Profile</button>
        </form>

        <div class="button-container" style="text-align: center; margin-top: 20px;">
            <button onclick="window.location.href='profile.php'" class="edit-button">Back to Profile</button>
        </div>
    </main>

    <!-- Include Footer -->
    <?php include('footer.php'); ?>
</body>
</html>