<?php
session_start();  // Start the session

// Check if the user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
include('connection.php'); // Include database connection

// Fetch users from the database
$users = $conn->query("SELECT id, username, email, phone, role, message FROM users");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- Link to external CSS stylesheet -->
</head>
<body>
    <?php include('navbar.php'); ?> <!-- Include the navigation bar -->
    <main class="main-content">
        <h2>User Management</h2>
        <?php if (isset($_GET['message'])): ?> <!-- Display success message if set -->
            <p class="success"><?php echo htmlspecialchars($_GET['message']); ?></p>
        <?php endif; ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Message</th>
                <th>Action</th>
            </tr>
            <?php while ($user = $users->fetch_assoc()): ?> <!-- Loop through each user -->
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['phone']); ?></td>
                    <td><?php echo $user['role']; ?></td>
                    <td><?php echo htmlspecialchars($user['message']); ?></td>
                    <td>
                        <div style="display: flex; gap: 10px; justify-content: <?php echo ($user['role'] !== 'customer') ? 'center' : 'flex-start'; ?>">
                            <button onclick="window.location.href='edit_user.php?id=<?php echo $user['id']; ?>'" class="edit-button">Edit Role</button>
                            <?php if ($user['role'] === 'customer'): ?> <!-- Show Edit Message button only for customer users -->
                                <button onclick="window.location.href='edit_user_message.php?id=<?php echo $user['id']; ?>'" class="edit-button">Edit Message</button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?> <!-- End of users loop -->
        </table>

        <div class="button-container" style="text-align: center; margin-top: 20px;">
            <button onclick="window.location.href='admin_panel.php'" class="edit-button">Back to Admin Panel</button>
        </div>
    </main>
    <?php include('footer.php'); ?> <!-- Include the footer -->
</body>
</html>