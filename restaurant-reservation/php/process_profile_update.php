<!-- filepath: /php/process_profile_update.php -->
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
include('connection.php');

$user_id = $_SESSION['user_id'];
$username = $_POST['username'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$password = $_POST['password'];

try {
    // Start transaction
    $conn->begin_transaction();

    // Update user info
    if (!empty($password)) {
        // Update with new password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET username = ?, email = ?, phone = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssi', $username, $email, $phone, $hashed_password, $user_id);
    } else {
        // Update without changing password
        $sql = "UPDATE users SET username = ?, email = ?, phone = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssi', $username, $email, $phone, $user_id);
    }
    $stmt->execute();

    $conn->commit();
    header('Location: profile.php?message=Profile updated successfully');
    exit();
} catch (Exception $e) {
    $conn->rollback();
    header('Location: profile_edit.php?error=' . urlencode($e->getMessage()));
    exit();
}
?>