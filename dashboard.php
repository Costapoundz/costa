<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['name'])) {
    // If not logged in, redirect to the login page
    header('Location: index.html');
    exit;
}

// User is logged in, display dashboard content
echo "Welcome to the Dashboard, " . htmlspecialchars($_SESSION['name']) . "!";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banking System - Select Action</title>
    
    <link href="img/i.png" rel="icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Full-page background image */
        body {
            background: url('img/gra.jpg') no-repeat center center fixed;
            background-size: cover;
            color: white;
        }

        .action-btn {
            width: 200px;
            height: 200px;
            font-size: 1.5rem;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s, box-shadow 0.2s;
            color: white;
            margin: 10px; /* Added margin for spacing */
        }

        .action-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        h1, p {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
        }

        /* Responsive button size */
        @media (max-width: 768px) {
            .action-btn {
                width: 100%;
                height: 60px;
                font-size: 1.2rem;
            }
        }

        /* Center the buttons */
        .btn-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px; /* Added gap for spacing */
        }
    </style>
</head>
<body>
<div class="container text-center mt-5">
    <h1 class="mb-4">Welcome</h1>
    
    <form action="logout.php" method="POST">
        <button type="submit" class="btn btn-danger mb-3">Logout</button>
    </form>

    <p class="lead mb-4">Please select an action to proceed:</p>
    
    <div class="btn-container">
        <form action="register.html" method="GET">
            <button type="submit" class="btn btn-primary action-btn">Register</button>
        </form>
        <form action="deposit.html" method="GET">
            <button type="submit" class="btn btn-warning action-btn">Deposit</button>
        </form>
        <form action="withdrawal.html" method="GET">
            <button type="submit" class="btn btn-warning action-btn">Withdrawal</button>
        </form>
        <form action="show_transactions.html" method="GET">
            <button type="submit" class="btn btn-info action-btn">Transactions</button>
        </form>
        <form action="check_balance.html" method="GET">
            <button type="submit" class="btn btn-success action-btn">Check Balance</button>
        </form>
        <form action="loans.html" method="GET">
            <button type="submit" class="btn btn-success action-btn">Loans</button>
        </form>
    </div>
</div>

<script>
    window.history.forward(); // Prevents going back immediately
    function preventBack() { window.history.forward(); }
    setTimeout(preventBack, 0);
    window.onunload = function () { window.history.forward(); };
</script>

</body>
</html>