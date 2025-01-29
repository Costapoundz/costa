<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['name'])) {
    // If not logged in, redirect to the login page
    header('Location: index.html');
    exit;
}
// Include the database connection file
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $name = $_POST["username"] ?? '';
    $staff_id = $_POST["staff_id"] ?? '';

    // Check if fields are empty
    if (empty($username) || empty($staff_id)) {
        echo "All fields are required.";
        exit();
    }

    // Prepare and execute a query to check if the staff ID already exists
    $stmt = $pdo->prepare("SELECT * FROM table_1 WHERE staff_id = ?");
    $stmt->execute([$staff_id]);

    if ($stmt->rowCount() === 0) {
        // Insert the new user if the staff ID is not found
        $stmt = $pdo->prepare("INSERT INTO table_1 (name, staff_id) VALUES (?, ?)");
          $stmt->execute([$name, $staff_id]);
        echo "
        <div style='text-align: center; margin-top: 50px;'>
            <h2>Registered Successful!</h2>
            <p>User <strong> $name </strong> registered successfully..</p>
            <p>Staff ID: <strong> $staff_id </strong>.</p>
            <a href='dashboard.php' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>Go to Home</a>
        </div>";
    } else {
       
        echo "
        <div style='text-align: center; margin-top: 50px;'>
            <h2>Registered Successful!</h2>
            <p>User <strong>$staff_id </strong>  is already registered..</p>
    
            <a href='dashboard.php' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>Go to Home</a>
        </div>";
        
        
    }
   

    exit;
}


?>


