<?php
$hostname = "localhost:3307";
$username = "root";
$password = "";
$databasename = "ecommerce";

$connection = mysqli_connect($hostname, $username, $password, $databasename,) or die ("connection");

// if (!$connection) {
//     // This shows you the actual error from MySQL
//     die("Connection failed: " . mysqli_connect_error());
// } else {
//     echo "✅ Connected successfully!";
// }
?>
