<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['name'])) {
    // If not logged in, redirect to the login page
    header('Location: index.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $staff_id = $_POST['staff_id'];
    $date = $_POST['date'];
    $period = $_POST['period'];
    $interest = $_POST['interest'];
    $amount = $_POST['amount'];

    //Validate the data
    if (empty($username) || empty($date) || empty($period) || empty($interest) || empty($amount)  || empty($staff_id)) {
        echo 'All fields are required';
        exit;
    }

    // Insert the data into the database
    $stmt = $pdo->prepare("INSERT INTO other_loans (username, date, period, interest, amount, staff_id) VALUES (?, ?, ?, ?, ?,?)");
    $stmt->execute([$username, $date, $period, $interest, $amount, $staff_id]);

    header('Location: other_loans.html');
    exit;
}



?>