<?php
// Start session
session_start();

// Database configuration
$host = 'localhost';
$dbname = 'lost_and_found_system';
$user = 'root'; // Change this to your MySQL username
$password = ''; // Change this to your MySQL password

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
