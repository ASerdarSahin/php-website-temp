<?php

session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $username     = $_POST['username'];
        $email        = $_POST['email'];
        $phone        = $_POST['phone'];
        $secret_key   = $_POST['secret_key'];
        $new_password = $_POST['new_password'];
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Fetch user data
        $sql = "SELECT id, secret_key FROM users WHERE username = ? AND email = ? AND phone = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Failed to prepare statement.');
        }
        $stmt->bind_param("sss", $username, $email, $phone);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) { // User found
            $user = $result->fetch_assoc();

            // Verify secret key
            if (password_verify($secret_key, $user['secret_key'])) {
                // Update password
                $updateSql = "UPDATE users SET password = ? WHERE id = ?";
                $updateStmt = $conn->prepare($updateSql);
                if (!$updateStmt) {
                    throw new Exception('Failed to prepare update statement.');
                }
                $updateStmt->bind_param("si", $hashed_new_password, $user['id']);
                if ($updateStmt->execute()) { // Password updated
                    $_SESSION['message'] = "Password reset successful. Please login with your new password.";
                    header("Location: login.php");
                    exit();
                } else {
                    throw new Exception('Error updating password.');
                }
            } else {
                throw new Exception('Invalid secret key.');
            }
        } else {
            throw new Exception('User not found or incorrect information provided.');
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: forgot_password.php");
        exit();
    } finally { // Close statement and connection
        if (isset($stmt)) {
            $stmt->close();
        }
        if (isset($updateStmt)) {
            $updateStmt->close();
        }
        $conn->close();
    }
}
?>