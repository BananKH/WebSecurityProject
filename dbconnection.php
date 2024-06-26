<?php
$servername = "localhost";
$username = "webuser";
$password = "1234";
$dbname = "securityproject";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Try to perform a simple query
$sql = "SHOW TABLES";
$result = $conn->query($sql);

$conn->close();
?>
