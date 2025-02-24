<?php
session_start();
require '../db.php';

// Ensure only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.html');
    exit();
}

// Fetch transaction history
$stmt = $pdo->query("SELECT t.user_id, u.name, t.action, t.old_balance, t.new_balance, t.change_time 
    FROM transaction_log t
    JOIN table_1 u ON t.user_id = u.id
    ORDER BY t.change_time DESC");

$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 20px; }
        table { width: 90%; margin: auto; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        th { background-color: #4CAF50; color: white; }
    </style>
</head>
<body>
    <h2>Transaction History</h2>
    <table>
        <tr>
            <th>User ID</th>
            <th>Name</th>
            <th>Action</th>
            <th>Old Balance</th>
            <th>New Balance</th>
            <th>Change Time</th>
        </tr>
        <?php foreach ($transactions as $transaction): ?>
        <tr>
            <td><?= htmlspecialchars($transaction['user_id']) ?></td>
            <td><?= htmlspecialchars($transaction['name']) ?></td>
            <td><?= htmlspecialchars($transaction['action']) ?></td>
            <td><?= number_format($transaction['old_balance'], 2) ?></td>
            <td><?= number_format($transaction['new_balance'], 2) ?></td>
            <td><?= htmlspecialchars($transaction['change_time']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
