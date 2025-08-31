<?php
// Connection details
$servername = "mysql";
$username = "root";
$password = "root";
$dbname = "resepsionis";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>