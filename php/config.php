<?php
// Database connection settings
$servername = "localhost";   // Server where MySQL is hosted
$username = "root";          // Default MySQL username for XAMPP
$password = "";              // Default MySQL password for XAMPP (blank)
$dbname = "crud_system";     // Database name used in your application

// Create a new MySQLi connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection for errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set character encoding to UTF-8 (recommended)
$conn->set_charset("utf8");
?>
