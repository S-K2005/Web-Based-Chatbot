<?php
$servername = "localhost";
$username   = "root";   // XAMPP default user
$password   = "";       // XAMPP default has no password
$database   = "chatbots"; // apna database naam

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// echo "✅ Database connected successfully!";
?>
