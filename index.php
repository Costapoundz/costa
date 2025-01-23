<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banking System - Select Action</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
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
        }
        .action-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>
<div class="container text-center mt-5">
    <h1 class="mb-4">Welcome</h1>
    <p class="lead mb-5">Please select an action to proceed:</p>
    <div class="d-flex justify-content-center gap-5">
        <form action="register.html" method="GET">
            <input type="hidden" name="action" value="deposit">
            <button type="submit" class="btn btn-primary action-btn">Register</button>
        </form>
        <form action="deposit.html" method="GET">
            <input type="hidden" name="action" value="withdrawal">
            <button type="submit" class="btn btn-warning action-btn">Deposit</button>
        </form>
        <form action="withdrawal.html" method="GET">
            <input type="hidden" name="action" value="withdrawal">
            <button type="submit" class="btn btn-warning action-btn">Withdrawal</button>
        </form>
        <form method="post" action="transaction.html">
            <input type="hidden" name="action" value="show_transactions">
            <button type="submit" class="btn btn-warning action-btn">Show Transactions</button>
  
</form>

    </div>
</div>
</body>
</html>
