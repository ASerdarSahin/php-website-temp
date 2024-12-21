<?php
// Start the session
session_start();

// Include the database connection
include('connection.php');

try {
    // Prepare the SQL statement to fetch restaurant information
    // Assuming there's only one restaurant entry with id = 1 !!!
    $sql = "SELECT name, address, phone FROM restaurants WHERE id = 1";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Failed to prepare the statement.');
    }
    
    // Execute the statement
    $stmt->execute();
    
    // Get the result
    $result = $stmt->get_result();
    
    // Check if restaurant information exists
    if ($result->num_rows === 0) {
        throw new Exception('Restaurant information not found.');
    }
    
    // Fetch the restaurant data
    $restaurant = $result->fetch_assoc();
    
} catch (Exception $e) {
    // Log the error for debugging
    error_log("Error in contact_us.php: " . $e->getMessage());
    
    // Set a session error message and redirect to the 500 error page
    $_SESSION['error'] = "An unexpected error occurred. Please try again later.";
    header("Location: 500.php");
    exit();
} finally {
    // Close the statement and the connection
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us - Restaurant Reservation System</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>

    </style>
</head>
<body>
    <!-- Include Navbar -->
    <?php include('navbar.php'); ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="contact-info">
            <h2>Contact Us</h2>
            <p><strong>Restaurant Name:</strong> <?php echo htmlspecialchars($restaurant['name']); ?></p>
            <p><strong>Address:</strong> <?php echo nl2br(htmlspecialchars($restaurant['address'])); ?></p>
            <p><strong>Phone:</strong> <a href="tel:<?php echo htmlspecialchars($restaurant['phone']); ?>"><?php echo htmlspecialchars($restaurant['phone']); ?></a></p>
           
        </div>
        <div class="button-container" style="text-align: center; margin-top: 20px;">
            <button onclick="window.location.href='../index.php'" class="edit-button">Back to Home</button>
        </div>
    </main>

    <!-- Include Footer -->
    <?php include('footer.php'); ?>
</body>
</html>