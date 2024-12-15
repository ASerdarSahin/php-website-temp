<!-- hangi user hangi useri editleyebiliyor test et -->
<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'owner')) {
    header('Location: login.php');
    exit();
}
include('connection.php');

$user_id = $_GET['id'];

// Fetch user data
$sql = "SELECT username, email, phone, role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_role = $_POST['role'];

    $updateSql = "UPDATE users SET role = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param('si', $new_role, $user_id);
    $updateStmt->execute();

    header('Location: admin_panel.php?message=User updated successfully');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User Role</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>
    <h2>Edit User Role</h2>
    <form method="POST">
        <p>Username: <?php echo htmlspecialchars($user['username']); ?></p>
        <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
        <p>Current Role: <?php echo $user['role']; ?></p>
        <label for="role">New Role:</label>
        <select name="role" id="role">
            <option value="customer" <?php if ($user['role'] === 'customer') echo 'selected'; ?>>Customer</option>
            <option value="admin" <?php if ($user['role'] === 'admin') echo 'selected'; ?>>Admin</option>
            <option value="owner" <?php if ($user['role'] === 'owner') echo 'selected'; ?>>Owner</option>
        </select>
        <button type="submit">Update Role</button>
    </form>
    <p><a href="admin_panel.php">Back to Admin Panel</a></p>
    <?php include('footer.php'); ?>
</body>
</html>