<?php
session_start(); // Start the session to manage user state
session_destroy(); // Destroy all data registered to the session
header("Location: login.php"); // Redirect the user to the login page
exit(); // Terminate the script execution
?>