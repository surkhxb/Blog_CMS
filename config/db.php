<?php
// Database configuration
$host = 'localhost';
$dbname = 'blog_cms'; // Your database name
$username = 'root';   // Default MySQL username in XAMPP
$password = '';       // Default MySQL password in XAMPP (if none, leave empty)

try {
    // Create a PDO instance (PHP Data Object) to interact with MySQL
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set error mode to exception
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
 