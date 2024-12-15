<?php
session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username     = $_POST['username'];
    $email        = $_POST['email'];
    $phone        = $_POST['phone'];
    $secret_key   = $_POST['secret_key'];
    $new_password = $_POST['new_password'];
    $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Fetch user data
    $sql = "SELECT id, secret_key FROM users WHERE username = ? AND email = ? AND phone = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify the secret key
        if (password_verify($secret_key, $user['secret_key'])) {
            // Update password
            $updateSql = "UPDATE users SET password = ? WHERE id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("si", $hashed_new_password, $user['id']);
            if ($updateStmt->execute()) {
                $_SESSION['message'] = "Password reset successful. Please login with your new password.";
                header("Location: login.php");
                exit();
            } else {
                $_SESSION['error'] = "Error updating password.";
                header("Location: forgot_password.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Invalid secret key.";
            header("Location: forgot_password.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "User not found or incorrect information provided.";
        header("Location: forgot_password.php");
        exit();
    }
}
?>