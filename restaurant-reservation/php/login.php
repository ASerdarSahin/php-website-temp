<?php
session_start();

// check user login
if (isset($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- Include Navbar -->
    <?php include('navbar.php'); ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="form-container">
            <h2>Login</h2>
            <?php
            // Display success message
            if (isset($_SESSION['message'])) {
                echo '<p class="success">' . htmlspecialchars($_SESSION['message']) . '</p>';
                unset($_SESSION['message']); // Clear the message after displaying
            }

            // Display error message
            if (isset($_SESSION['error'])) {
                echo '<p class="error">' . htmlspecialchars($_SESSION['error']) . '</p>';
                unset($_SESSION['error']); // Clear the error after displaying
            }
            ?>
            <form action="process_login.php" method="POST"> <!-- Form submission to process_login.php -->
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" placeholder="Username" required>

                <label for="password">Password:</label>
                <input type="password" name="password" id="password" placeholder="Password" required>

                <button type="submit">Login</button>
            </form>
            <div class="button-container" style="display: flex; justify-content: center; gap: 20px; margin-top: 20px;">
                <button onclick="window.location.href='register.php'" class="edit-button">Register here</button>
                <button onclick="window.location.href='forgot_password.php'" class="edit-button">Forgot password?</button>
            </div>
        </div>
    </main>

    <!-- Include Footer -->
    <?php include('footer.php'); ?>
</body>
</html>