<?php
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
        echo "User $name registered successfully.";
    } else {
        echo "Staff ID $staff_id is already registered.";

        
        
    }
   

    exit;
}


?>


