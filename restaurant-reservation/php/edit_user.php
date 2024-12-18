<?php
// filepath: /c:/xampp/htdocs/restaurant-reservation/php/edit_user.php
session_start();


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Access denied. Only administrators can edit user roles.";
    header('Location: ../index.php');
    exit();
}

include('connection.php');

try {
    // Validate and sanitize the user ID parameter
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        throw new Exception('Invalid user ID.');
    }
    $user_id = intval($_GET['id']);

    // Fetch user data
    $sql = "SELECT username, email, phone, role FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Failed to prepare statement.');
    }
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('User not found.');
    }

    $user = $result->fetch_assoc();

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Validate and sanitize input
            if (!isset($_POST['role']) || empty($_POST['role'])) {
                throw new Exception('Role is required.');
            }
            $new_role = trim(filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING));

            // Validate role value
            $valid_roles = ['customer', 'admin', 'owner'];
            if (!in_array($new_role, $valid_roles)) {
                throw new Exception('Invalid role selected.');
            }

            // Update user role within a transaction
            $conn->begin_transaction();

            $updateSql = "UPDATE users SET role = ? WHERE id = ?";
            $updateStmt = $conn->prepare($updateSql);
            if (!$updateStmt) {
                throw new Exception('Failed to prepare update statement.');
            }
            $updateStmt->bind_param('si', $new_role, $user_id);

            if (!$updateStmt->execute()) {
                throw new Exception('Failed to update user role.');
            }

            $conn->commit();

            // Success message
            header('Location: user_management.php?message=User updated successfully');
            exit();
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            // Redirect with error message
            header('Location: edit_user.php?id=' . urlencode($user_id) . '&error=' . urlencode($e->getMessage()));
            exit();
        } finally {
            if (isset($updateStmt)) {
                $updateStmt->close();
            }
        }
    }
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: user_management.php?error=' . urlencode($e->getMessage()));
    exit();
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
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
    <!-- Include Navbar -->
    <?php include('navbar.php'); ?>

    <!-- Main Content -->
    <main class="main-content">
        <h2>Edit User Role</h2>
        <?php if (isset($_GET['error'])): ?>
            <p class="error"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>
        <form method="POST">
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Current Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>

            <label for="role">New Role:</label>
            <select name="role" id="role" required>
                <option value="" disabled>Select a role</option>
                <option value="customer" <?php if ($user['role'] === 'customer') echo 'selected'; ?>>Customer</option>
                <option value="admin" <?php if ($user['role'] === 'admin') echo 'selected'; ?>>Admin</option>
                <option value="owner" <?php if ($user['role'] === 'owner') echo 'selected'; ?>>Owner</option>
            </select>

            <button type="submit">Update Role</button>
        </form>
        <div class="button-container" style="text-align: center; margin-top: 20px;">
            <button onclick="window.location.href='user_management.php'" class="edit-button">Back to User Management</button>
        </div>
    </main>

    <!-- Include Footer -->
    <?php include('footer.php'); ?>
</body>
</html>