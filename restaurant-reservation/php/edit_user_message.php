<?php
// filepath: /c:/xampp/htdocs/restaurant-reservation/php/edit_user_message.php
session_start();


if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'owner'])) {
    header('Location: login.php');
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
    $sql = "SELECT username, message FROM users WHERE id = ?";
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
            if (!isset($_POST['message'])) {
                throw new Exception('Message is required.');
            }
            $message = trim($_POST['message']);

            // Optional: Further sanitize the message if necessary
            $sanitized_message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

            // Update user message within a transaction
            $conn->begin_transaction();

            $updateSql = "UPDATE users SET message = ? WHERE id = ?";
            $updateStmt = $conn->prepare($updateSql);
            if (!$updateStmt) {
                throw new Exception('Failed to prepare update statement.');
            }
            $updateStmt->bind_param('si', $sanitized_message, $user_id);

            if (!$updateStmt->execute()) {
                throw new Exception('Failed to update user message.');
            }

            $conn->commit();

            // Success message
            header('Location: admin_panel.php?message=User message updated successfully');
            exit();
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            // Redirect with error message
            header('Location: edit_user_message.php?id=' . urlencode($user_id) . '&error=' . urlencode($e->getMessage()));
            exit();
        } finally {
            if (isset($updateStmt)) {
                $updateStmt->close();
            }
        }
    }
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: admin_panel.php?error=' . urlencode($e->getMessage()));
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
        <form method="POST">
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