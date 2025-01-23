<?php
require "db.php";

$transactions = [];
$filter = "All"; // Default filter

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filter = $_POST['transaction_type'] ?? "All";

    try {
        if ($filter === "Deposit" || $filter === "Withdraw") {
            // Fetch transactions based on the selected filter
            $stmt = $pdo->prepare("SELECT * FROM transactions WHERE transaction_type = ? ORDER BY id DESC");
            $stmt->execute([$filter]);
        } else {
            // Fetch all transactions if no filter is selected
            $stmt = $pdo->query("SELECT * FROM transactions ORDER BY id DESC");
        }

        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        echo "<p style='color: red; text-align: center;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('img/po.jpeg') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h2 {
            text-align: center;
            color: #fff;
            margin-bottom: 20px;
        }
        .filter-form {
            text-align: center;
            margin-bottom: 20px;
        }
        .filter-form button {
            margin-left: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        .actions {
            text-align: center;
            margin-top: 20px;
        }
        .actions a, .actions button {
            margin: 5px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .actions button:hover, .actions a:hover {
            background-color: #45a049;
        }
        .container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Transaction Management</h2>

        <!-- Navigation -->
        <div class="actions">
            <a href="index.html">Go to Home</a>
            <button onclick="window.print()">Print</button>
        </div>

        <!-- Filter Form -->
        <form method="POST" class="filter-form">
            <label for="transaction_type">Filter Transactions:</label>
            <select name="transaction_type" id="transaction_type" class="form-select d-inline w-auto">
                <option value="All" <?= $filter === "All" ? "selected" : "" ?>>All</option>
                <option value="Deposit" <?= $filter === "Deposit" ? "selected" : "" ?>>Deposit</option>
                <option value="Withdraw" <?= $filter === "Withdraw" ? "selected" : "" ?>>Withdraw</option>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        <!-- Transactions Table -->
        <?php if (!empty($transactions)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Staff ID</th>
                        <th>Name</th>
                        <th>Amount</th>
                        <th>Transaction Type</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td><?= htmlspecialchars($transaction['id']) ?></td>
                            <td><?= htmlspecialchars($transaction['staff_id']) ?></td>
                            <td><?= htmlspecialchars($transaction['name']) ?></td>
                            <td>Gh₵ <?= htmlspecialchars($transaction['amount']) ?></td>
                            <td><?= htmlspecialchars($transaction['transaction_type']) ?></td>
                            <td><?= htmlspecialchars($transaction['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center; margin-top: 20px;">No transactions found.</p>
        <?php endif; ?>

        <div class="actions">
            <a href="index.html">Go to Home</a>
            <button onclick="window.print()">Print</button>
        </div>
    </div>
</body>
</html>
