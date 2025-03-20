<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['name'])) {
    // If not logged in, redirect to the login page
    header('Location: index.html');
    exit;
}

// Fetch total balance
$stmt = $pdo->query("SELECT SUM(balance) AS total_balance FROM table_1");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$totalBalance = $row['total_balance'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Total Balance</title>  
    <style>
        /* Centering the content */
        body {
            display: flex;
            flex-direction: column; /* Stack items vertically */
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f8f8f8;
            font-family: Arial, sans-serif;
            background-image: url('img/i.png');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-position: center;
        }
        .balance-box {
            text-align: center;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: white;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px; /* Space between balance and button */
        }
        .balance-text {
            color: black;
            font-size: 24px;
            font-weight: bold;
        }
        .btn {
            padding: 10px 20px;
            background-color: black;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s;
        }
        .btn:hover {
            background-color:rgb(14, 20, 26);
        }
    </style>  
    <meta http-equiv="refresh" content="10"> <!-- Auto-refresh page every 10 seconds -->  
</head>
<body>

    <!-- Balance Section -->
    <div class="balance-box">
        <h3>Current Balance</h3>
        <h2 class="balance-text">₵<?php echo number_format($totalBalance, 2); ?></h2>
    </div>

    <!-- Button Section -->
    <button onclick="window.location.href='users_balance.php'" class="btn">Total Balance</button>

</body>
</html>
