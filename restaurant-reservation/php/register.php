<?php
session_start();

// Redirect to profile page if user is logged in
if (isset($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- Include Navbar -->
    <?php include('navbar.php'); ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="form-container">
            <h2>Register</h2>
                <?php
                // Display error message if it exists in the session
                if (isset($_SESSION['error'])) {
                    echo '<p class="error">' . htmlspecialchars($_SESSION['error']) . '</p>';
                    unset($_SESSION['error']);
                }
                ?>
            <form action="process_register.php" method="POST"> <!-- Form submission to process_register.php -->
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" placeholder="Username" required>

                <label for="email">Email:</label>
                <input type="email" name="email" id="email" placeholder="Email" required>

                <label for="phone">Phone Number:</label>
                <input type="tel" name="phone" id="phone" placeholder="Phone Number" required>

                <label for="password">Password:</label>
                <input type="password" name="password" id="password" placeholder="Password" required>

                <label for="secret_key">Secret Key (used for password recovery):</label>
                <input type="text" name="secret_key" id="secret_key" placeholder="Secret Key" required>

                <button type="submit">Register</button>
            </form>
            <p>Already have an account? 
                    <div class="button-container" style="text-align: center; margin-top: 20px;">
                        <button onclick="window.location.href='login.php'" class="edit-button">Login here</button>
                    </div> 
            </p>
        </div>
    </main>

    <!-- Include Footer -->
    <?php include('footer.php'); ?>
</body>
</html>