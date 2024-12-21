<?php
session_start();

// Check if the user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Access denied. Only administrators can edit user roles.";
    header('Location: ../index.php');
    exit();
}

include('connection.php');

// Fetch user data and update message
try {
    // Validate and sanitize the user ID parameter
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        throw new Exception('Invalid user ID.');
    }
    $user_id = intval($_GET['id']);

    // Fetch user data
    $sql = "SELECT username, message FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Failed to prepare statement.');
    }
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows === 0) {
        throw new Exception('User not found.');
    }

    // Fetch user data
    $user = $result->fetch_assoc();

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Validate and sanitize input
            if (!isset($_POST['message'])) {
                throw new Exception('Message is required.');
            }
            $message = trim($_POST['message']);

            // Further sanitize the message if necessary
            $sanitized_message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

            // Update user message within a transaction
            $conn->begin_transaction();

            $updateSql = "UPDATE users SET message = ? WHERE id = ?";
            $updateStmt = $conn->prepare($updateSql);
            if (!$updateStmt) {
                throw new Exception('Failed to prepare update statement.');
            }
            $updateStmt->bind_param('si', $sanitized_message, $user_id);

            // Execute the update statement
            if (!$updateStmt->execute()) {
                throw new Exception('Failed to update user message.');
            }

            $conn->commit();

            // Success message
            header('Location: user_management.php?message=User message updated successfully');
            exit();
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            // Redirect with error message
            header('Location: edit_user_message.php?id=' . urlencode($user_id) . '&error=' . urlencode($e->getMessage()));
            exit();
        } finally { // Close prepared statements
            if (isset($updateStmt)) {
                $updateStmt->close();
            }
        }
    }
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: user_management.php?error=' . urlencode($e->getMessage()));
    exit();
} finally { // Close prepared statements and database connection
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
    <title>Edit User Message</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- Include Navbar -->
    <?php include('navbar.php'); ?>

    <!-- Main Content -->
    <main class="main-content">
        <h2>Edit Message for <?php echo htmlspecialchars($user['username']); ?></h2>
        <?php if (isset($_GET['error'])): ?>
            <p class="error"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>
        <form method="POST"> <!-- Form to update user message -->
            <label for="message">Message:</label>
            <textarea name="message" id="message" rows="5" cols="50" required><?php echo htmlspecialchars($user['message']); ?></textarea>
            <button type="submit">Update Message</button>
        </form>
        <div class="button-container" style="text-align: center; margin-top: 20px;">
            <button onclick="window.location.href='user_management.php'" class="edit-button">Back to User Management</button>
        </div>
    </main>

    <!-- Include Footer -->
    <?php include('footer.php'); ?>
</body>
</html>