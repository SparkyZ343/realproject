<?php
// Database connection file for pg_accomodation
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pg_accommodation"; // Your database name

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
