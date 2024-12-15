<!-- filepath: php/navbar.php -->
<nav>
    <?php if(isset($_SESSION['user_id'])): ?>
        <a href="/restaurant-reservation/index.php">Home</a>
        <a href="/restaurant-reservation/php/profile.php">My Profile</a>
        <?php if($_SESSION['role'] === 'admin'): ?>
            <a href="/restaurant-reservation/php/admin_panel.php">Admin Panel</a>
        <?php endif; ?>
        <?php if($_SESSION['role'] === 'owner'): ?>
            <a href="/restaurant-reservation/php/owner_panel.php">Owner Panel</a>
        <?php endif; ?>
        <a href="/restaurant-reservation/php/logout.php">Logout</a>
    <?php else: ?>
        <a href="/restaurant-reservation/index.php">Home</a>
        <a href="/restaurant-reservation/php/login.php">Login</a>
        <a href="/restaurant-reservation/php/register.php">Register</a>
        <a href="/restaurant-reservation/php/check_reservation.php">Check Reservation</a>
    <?php endif; ?>
</nav>