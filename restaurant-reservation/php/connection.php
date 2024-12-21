<?php

   // Defined the database connection constants for local development
   define('DB_USER', "user");
   define('DB_PASSWORD', "123");
   define('DB_DATABASE', "projectrestaurant");
   define('DB_SERVER', "localhost:3308");

// Add error handling for connection failure
$conn = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set the character set for the database connection to UTF-8
mysqli_set_charset($conn, "UTF8");

?>