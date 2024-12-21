<?php
session_start();

// check user login
if(isset($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- Include Navbar -->
    <?php include('navbar.php'); ?>

    <!-- Main Content -->
    <main class="main-content">

        <div class="form-container">
            <h2>Reset Password</h2>
            
            <?php
            // Display error message
            if (isset($_SESSION['error'])) {
                echo '<p class="error">' . htmlspecialchars($_SESSION['error']) . '</p>';
                unset($_SESSION['error']);
            }

            // Display success message
            if (isset($_SESSION['message'])) {
                echo '<p class="success">' . htmlspecialchars($_SESSION['message']) . '</p>';
                unset($_SESSION['message']);
            }
            ?>

            <form action="process_forgot_password.php" method="POST"> <!-- Form submission to process_forgot_password.php -->
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="tel" name="phone" placeholder="Phone Number" required>
                <input type="text" name="secret_key" placeholder="Secret Key" required>
                <input type="password" name="new_password" placeholder="New Password" required>
                <button type="submit">Reset Password</button>
            </form>
            
        </div>
        <div class="button-container" style="text-align: center; margin-top: 20px;">
            <button onclick="window.location.href='../index.php'" class="edit-button">Back to Home</button>
        </div>
    </main>
    <!-- Include Footer -->
    <?php include('footer.php'); ?>
</body>
</html>