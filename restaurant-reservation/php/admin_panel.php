<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

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

        .admin-card i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        .admin-card h3 {
            margin: 0.5rem 0;
            color: var(--primary-color);
        }

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
        <h2>Admin Panel</h2>
        <div class="admin-grid">
            <a href="user_management.php" class="admin-card">
                <i class="fas fa-users"></i>
                <h3>User Management</h3>
                <p>Manage user accounts, roles, and messages</p>
            </a>
            
            <a href="reservation_management.php" class="admin-card">
                <i class="fas fa-calendar-check"></i>
                <h3>Reservation Management</h3>
                <p>View and manage all restaurant reservations</p>
            </a>
            
            <a href="statistics.php" class="admin-card">
                <i class="fas fa-chart-bar"></i>
                <h3>Statistics</h3>
                <p>View reservation statistics</p>
            </a>
            
            <a href="promotion_message.php" class="admin-card">
                <i class="fas fa-bullhorn"></i>
                <h3>Promotion Messages</h3>
                <p>Manage promotional messages</p>
            </a>
        </div>
    </main>
    <?php include('footer.php'); ?>
</body>
</html>