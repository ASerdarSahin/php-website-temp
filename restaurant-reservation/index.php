<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: php/login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Reservation System</title>
    <!-- Link to External CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Welcome to the Restaurant Reservation System</h1>
        <nav>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="index.php">Home</a>
                <a href="php/profile.php">My Profile</a>
                <?php if($_SESSION['role'] === 'admin'): ?>
                    <a href="php/admin_panel.php">Admin Panel</a>
                <?php endif; ?>
                <a href="php/logout.php">Logout</a>
            <?php else: ?>
                <a href="php/login.php">Login</a>
                <a href="php/register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
    <p>Start making your reservations now!</p>
    <form id="reservationForm">
        <label for="table">Select Table:</label>
        <select id="table" name="table_id" required>
            <option value="">Select a table</option>
            <!-- Options populated via JavaScript -->
        </select><br>

        <label for="timeslot">Select Time Slot:</label>
        <select id="timeslot" name="timeslot_id" required disabled>
            <option value="">First select a table</option>
            <!-- Options populated via JavaScript -->
        </select><br>

        <button type="submit" disabled>Reserve</button>
    </form>
    </main>
    <footer>
        <p>&copy; 2024 Restaurant Reservation System</p>
    </footer>
    <!-- Link to External JavaScript -->
    <script src="js/script.js"></script>
</body>
</html>