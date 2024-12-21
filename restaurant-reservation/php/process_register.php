<?php

session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Sanitize and validate input
        $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $phone = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING));
        $secret_key = trim(filter_input(INPUT_POST, 'secret_key', FILTER_SANITIZE_STRING));
        $password_plain = $_POST['password'];

        // Ensure all fields are filled
        if (!$username || !$email || !$phone || !$secret_key || !$password_plain) {
            throw new Exception('All fields are required.');
        }

        // Hash passwords
        $password = password_hash($password_plain, PASSWORD_DEFAULT);
        $hashed_secret_key = password_hash($secret_key, PASSWORD_DEFAULT);

        // Check for existing username or email
        $checkSql = "SELECT id FROM users WHERE username = ? OR email = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param('ss', $username, $email);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            throw new Exception('Username or email already exists.');
        }

        // Insert new user
        $sql = "INSERT INTO users (username, email, phone, password, secret_key) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            // Handle statement preparation error
            throw new Exception('Failed to prepare statement.');
        }
        $stmt->bind_param("sssss", $username, $email, $phone, $password, $hashed_secret_key);

        if ($stmt->execute()) {
            // Registration successful
            $_SESSION['message'] = 'Registration successful. You can now log in.';
            header("Location: login.php");
            exit();
        } else {
            // Check for duplicate entry error
            if ($conn->errno == 1062) { // Duplicate entry error code
                throw new Exception('Email or username already exists.');
            } else {
                // Other database error
                throw new Exception('An error occurred during registration.');
            }
        }
    } catch (Exception $e) {
        // Catch any exceptions and display error message
        $_SESSION['error'] = $e->getMessage();
        header("Location: register.php");
        exit();
    } finally {
        // Close statements and connection
        if (isset($stmt)) {
            $stmt->close();
        }
        if (isset($checkStmt)) {
            $checkStmt->close();
        }
        $conn->close();
    }
}
?>