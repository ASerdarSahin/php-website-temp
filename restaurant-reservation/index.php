<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Restaurant Reservation System</title>
    <!-- Link to External CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Welcome to the Restaurant Reservation System</h1>
        <?php include('php/navbar.php'); ?>
    </header>
    <main>
        <?php if(isset($_SESSION['user_id'])): ?>
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
        <?php else: ?>
            <h2>Welcome to Our Restaurant!</h2>
            <p>You can make reservations by logging in or registering an account.</p>
            <p>If you've already made a reservation, you can <a href="php/check_reservation.php">check your reservation</a> using your confirmation number.</p>
        <?php endif; ?>
    </main>
    <?php include('php/footer.php'); ?>

    <!-- Conditionally Load JavaScript Only for Logged-in Users -->
    <?php if(isset($_SESSION['user_id'])): ?>
        <script src="js/script.js"></script>
    <?php endif; ?>
</body>
</html>