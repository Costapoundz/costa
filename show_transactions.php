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
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h2 {
            text-align: center;
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
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h2>Transaction Management</h2>

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
                        <td><?= htmlspecialchars($transaction['amount']) ?></td>
                        <td><?= htmlspecialchars($transaction['transaction_type']) ?></td>
                        <td><?= htmlspecialchars($transaction['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align: center; margin-top: 20px;">No transactions found.</p>
    <?php endif; ?>

    <div style="text-align: center;">
        <a href="index.html">Go to Home</a>
    </div>
</body>
</html>
