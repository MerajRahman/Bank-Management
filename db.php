<?php
$host = 'localhost';
$user = 'root'; // Default XAMPP username
$pass = '';     // Default XAMPP password is empty
$dbname = 'bank'; // The database you created in phpMyAdmin

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>