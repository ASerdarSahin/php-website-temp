<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Restaurant Reservation System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Welcome to the Restaurant Reservation System</h1>
        <?php include('php/navbar.php'); ?>
    </header>

    <!-- Hero Image Section -->
    <div class="hero-image">
        <img src="images/jason-leung-poI7DelFiVA-unsplash.jpg" alt="Restaurant ambiance"> 
    </div>

    <main class="main-content">
        <div class="columns">
            <!-- Left Column: Restaurant Description -->
            <div class="description-section">
                <h2>Experience Fine Dining</h2>
                <p>Welcome to our elegant restaurant where culinary excellence meets warm hospitality. Our expert chefs craft exquisite dishes using the finest ingredients, creating an unforgettable dining experience.</p>
                
                <h3>Our Cuisine</h3>
                <p>Indulge in our carefully curated menu featuring both traditional favorites and innovative culinary creations. Each dish is prepared with passion and precision, ensuring a memorable dining experience.</p>
                
                <h3>Atmosphere</h3>
                <p>Enjoy your meal in our sophisticated yet comfortable dining room, perfect for both intimate dinners and special celebrations. Our attentive staff ensures impeccable service throughout your visit.</p>
                Photo by <a href="https://unsplash.com/@ninjason?utm_content=creditCopyText&utm_medium=referral&utm_source=unsplash">Jason Leung</a> on <a href="https://unsplash.com/photos/photo-of-pub-set-in-room-during-daytime-poI7DelFiVA?utm_content=creditCopyText&utm_medium=referral&utm_source=unsplash">Unsplash</a>
            </div>

            <!-- Right Column: Reservation Section -->
            <div class="reservation-section">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="reservation-box">
                        <h2>Make a Reservation</h2>
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
                    </div>
                <?php else: ?>
                    <div class="welcome-box">
                        <h2>Welcome to Our Restaurant!</h2>
                        <p>To make a reservation, please log in or create an account.</p>
                        <div class="action-buttons">
                            <a href="php/login.php" class="btn btn-primary">Login</a>
                            <a href="php/register.php" class="btn btn-secondary">Register</a>
                        </div>
                        <p class="check-reservation">
                            Already have a reservation?
                            <div class="button-container" style="text-align: center; margin-top: 20px;">
                            <button onclick="window.location.href='php/check_reservation.php'" class="edit-button">Check status here</button>
                            </div> 
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include('php/footer.php'); ?>

    <?php if(isset($_SESSION['user_id'])): ?>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="js/script.js"></script>
    <?php endif; ?>
</body>
</html>