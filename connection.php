<?php
// Database configuration
$host = "localhost";     
$db_name = "student_complaint_system"; 
$db_user = "root";        
$db_pass = "";            

// Create connection
$conn = new mysqli($host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set character set to handle special characters
$conn->set_charset("utf8mb4");
?>
