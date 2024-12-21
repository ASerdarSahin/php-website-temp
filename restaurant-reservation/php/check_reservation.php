<?php
session_start(); // Initialize session

// user login check
if (isset($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit();
}
include('connection.php');

// Initialize variables
$confirmation_number = '';
$reservation = null;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $confirmation_number = $_POST['confirmation_number'];

    // Fetch reservation data
    $sql = "SELECT r.id, r.date, r.time, t.id AS table_id, t.capacity, r.status
            FROM reservations r
            JOIN tables t ON r.table_id = t.id
            WHERE r.confirmation_number = ?";
    
    // Prepare and execute the SQL statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $confirmation_number);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if reservation exists
    if ($result->num_rows === 1) {
        $reservation = $result->fetch_assoc();
    } else {
        $error = 'Reservation not found. Please check your confirmation number.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Check Reservation</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- Include Navbar -->
    <?php include('navbar.php'); ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="form-container">
            <h2>Check Your Reservation</h2>
            <form method="POST"> <!-- Form to check reservation -->
                <label for="confirmation_number">Confirmation Number:</label>
                <input type="text" name="confirmation_number" id="confirmation_number" placeholder="Confirmation Number" value="<?php echo htmlspecialchars($confirmation_number); ?>" required>
                <button type="submit">Check Reservation</button>
            </form>

            <?php if ($error): ?> <!-- Display error message if set -->
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php elseif ($reservation): ?> <!-- Display reservation details if found -->
                <h3>Reservation Details</h3>
                <p><strong>Reservation ID:</strong> <?php echo $reservation['id']; ?></p>
                <p><strong>Date:</strong> <?php echo $reservation['date']; ?></p>
                <p><strong>Time:</strong> <?php echo $reservation['time']; ?></p>
                <p><strong>Table ID:</strong> <?php echo $reservation['table_id']; ?></p>
                <p><strong>Table Capacity:</strong> <?php echo $reservation['capacity']; ?> seats</p>
                <p><strong>Status:</strong> <?php echo ucfirst($reservation['status']); ?></p>
            <?php endif; ?>

            <div class="button-container" style="text-align: center; margin-top: 20px;">
            <button onclick="window.location.href='../index.php'" class="edit-button">Back to Home</button>
            </div>
        </div>

        
    </main>

    <!-- Include Footer -->
    <?php include('footer.php'); ?>
</body>
</html>