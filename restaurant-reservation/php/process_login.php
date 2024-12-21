<?php

session_start();
require_once 'connection.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Prepare statement
        $sql = "SELECT id, password, role FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Failed to prepare statement.');
        }
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user exists
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                header("Location: ../index.php");
                exit();
            } else {
                throw new Exception('Invalid username or password.');
            }
        } else {
            throw new Exception('Invalid username or password.');
        }
    } catch (Exception $e) {
        // Handle exceptions
        $_SESSION['error'] = $e->getMessage();
        header("Location: login.php");
        exit();
    } finally { // Close statement and connection
        if (isset($stmt)) {
            $stmt->close();
        }
        $conn->close();
    }
}
?>