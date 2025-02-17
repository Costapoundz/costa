<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['name'])) {
    header('Location: index.html');
    exit;
}

// Include the database connection file
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $name = trim($_POST["username"] ?? '');
    $staff_id = trim($_POST["staff_id"] ?? '');

    // Check if fields are empty
    if (empty($name) || empty($staff_id)) {
        echo "<script>alert('All fields are required.'); window.location.href='register.php';</script>";
        exit;
    }

    // Prepare and execute a query to check if the staff ID already exists
    $stmt = $pdo->prepare("SELECT id FROM table_1 WHERE staff_id = :staff_id");
    $stmt->execute(['staff_id' => $staff_id]);

    if ($stmt->rowCount() === 0) {
        // Insert the new user if the staff ID is not found
        $stmt = $pdo->prepare("INSERT INTO table_1 (name, staff_id) VALUES (:name, :staff_id)");
        $stmt->execute(['name' => $name, 'staff_id' => $staff_id]);

        echo "
        <div style='text-align: center; margin-top: 50px;'>
            <h2>Registration Successful!</h2>
            <p>User <strong>$name</strong> registered successfully.</p>
            <p>Staff ID: <strong>$staff_id</strong></p>
            <a href='dashboard.php' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>Go to Dashboard</a>
        </div>";
    } else {
        echo "
        <div style='text-align: center; margin-top: 50px;'>
            <h2>Registration Failed!</h2>
            <p>Staff ID <strong>$staff_id</strong> is already registered.</p>
            <a href='register.html' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: red; color: white; text-decoration: none; border-radius: 5px;'>Try Again</a>
        </div>";
    }

    exit;
}
?>
