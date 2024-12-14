<?php
   
   define('DB_USER', "user");
   define('DB_PASSWORD', "123");
   define('DB_DATABASE', "projectrestaurant");
   define('DB_SERVER', "localhost:3308");

$conn=mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
mysqli_set_charset($conn,"UTF8");

?>