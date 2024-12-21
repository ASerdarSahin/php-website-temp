<?php
session_start();

// user login and role check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Owner Panel</title>
    <!-- Links to Font Awesome for icons and external CSS sheet -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Grid layout for admin cards */
        .admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        /* Styling for each admin card */    
        .admin-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-decoration: none;
            color: inherit;
        }

        .admin-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        /* Icon styling inside admin cards */          
        .admin-card i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }
        /* Header styling inside admin cards */          
        .admin-card h3 {
            margin: 0.5rem 0;
            color: var(--primary-color);
        }
        /* Paragraph styling inside admin cards */
        .admin-card p {
            font-size: 0.9rem;
            color: #666;
            margin: 0.5rem 0;
        }
    </style>
</head>
<body>
    <?php include('navbar.php'); ?>
    <main class="main-content">
        <h2>Owner Panel</h2>
        <div class="admin-grid">
            <!-- Reservation Management Card -->
            <a href="reservation_management.php" class="admin-card">
                <i class="fas fa-calendar-check"></i> <!-- Calendar icon -->
                <h3>Reservation Management</h3>
                <p>View and manage all restaurant reservations</p>
            </a>
            
            <!-- Promotion Messages Card -->
            <a href="promotion_message.php" class="admin-card">
                <i class="fas fa-bullhorn"></i><!-- Bullhorn (megafon) icon -->
                <h3>Promotion Messages</h3>
                <p>Manage promotional messages</p>
            </a>
        </div>
    </main>
    <?php include('footer.php'); ?>
</body>
</html>