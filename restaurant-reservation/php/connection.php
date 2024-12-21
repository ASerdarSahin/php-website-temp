<?php
   
   define('DB_USER', "user");
   define('DB_PASSWORD', "123");
   define('DB_DATABASE', "projectrestaurant");
   define('DB_SERVER', "localhost:3308");

// Add error handling for connection failure
$conn = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "UTF8");

?>