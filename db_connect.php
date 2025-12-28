<?php
$servername = "localhost";   // usually "localhost"
$username = "root";          // your MySQL username
$password = "1234";              // your MySQL password (blank in WAMP by default)
$dbname = "idcard_db";  // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
