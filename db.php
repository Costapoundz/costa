<?php
// Database configuration
$host = "localhost";      // Database host
$dbname = "database"; // Replace with your database name
$username = "root";        // Replace with your database username
$password = "";            // Replace with your database password



// $dbname = "graflame"; 
// $username = "grasavings";        
// $password = "gr@S@v1ng$"; 



try {
    $pdo= new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
 catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>
