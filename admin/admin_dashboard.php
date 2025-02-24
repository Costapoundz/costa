<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <div>
    <h1>Welcome, Admin!</h1>
    <a href="logout.php">Logout</a>
    </div>
    <div>
        <button onclick="window.location.href='registerinfo.php'">Register info</button>
    </div>

    <div>
        <button onclick="window.location.href='deposit_info.php'">balance + deposit info</button>
    </div>
    
</body>
</html>
