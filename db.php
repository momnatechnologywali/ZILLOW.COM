<?php
// db.php
// Database connection file
 
$host = 'localhost'; // Assuming local host; adjust if needed for the provided DB
$dbname = 'dbi7a36epgloaf';
$username = 'uhpdlnsnj1voi';
$password = 'rowrmxvbu3z5';
 
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
