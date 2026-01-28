<?php
$servername = "localhost";
$dbname = "clocky";
$username = "patrik";
$password = "patrik30";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
