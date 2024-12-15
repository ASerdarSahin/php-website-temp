<?php
session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $phone = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING));
    $secret_key = trim(filter_input(INPUT_POST, 'secret_key', FILTER_SANITIZE_STRING));
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $hashed_secret_key = password_hash($secret_key, PASSWORD_DEFAULT);

    $checkSql = "SELECT id FROM users WHERE username = ? OR email = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param('ss', $username, $email);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows > 0) {
    throw new Exception('Username or email already exists.');
    }

    $sql = "INSERT INTO users (username, email, phone, password, secret_key) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $username, $email, $phone, $password, $hashed_secret_key);

    if ($stmt->execute()) {
        header("Location: login.php");
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>