<?php
require "db.php";

$transactions = [];
$filter = "All"; // Default filter
$monthFilter = "All"; // Default month filter
$searchStaffId = ""; // Default search for staff ID (empty)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filter = $_POST['transaction_type'] ?? "All";
    $monthFilter = $_POST['transaction_month'] ?? "All";
    $searchStaffId = $_POST['search_staff_id'] ?? ""; // Capture the search term for staff ID

    try {
        // Build the base query
        $query = "SELECT * FROM transactions WHERE 1"; // Default condition (SELECT all)

        // Add conditions based on filters
        if ($filter !== "All") {
            $query .= " AND transaction_type = ?";
        }
        
        if ($monthFilter !== "All") {
            $query .= " AND MONTH(created_at) = ?";
        }

        if (!empty($searchStaffId)) {
            $query .= " AND staff_id LIKE ?"; // Filter by staff ID
        }

        // Prepare the query
        $stmt = $pdo->prepare($query);

        // Bind parameters
        $params = [];
        if ($filter !== "All") {
            $params[] = $filter;
        }
        if ($monthFilter !== "All") {
            $params[] = $monthFilter;
        }
        if (!empty($searchStaffId)) {
            $params[] = "%" . $searchStaffId . "%"; // Use LIKE for partial matching
        }

        // Execute the query with the parameters
        $stmt->execute($params);
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
            <label for="transaction_type">Filter by Type:</label>
            <select name="transaction_type" id="transaction_type" class="form-select d-inline w-auto">
                <option value="All" <?= $filter === "All" ? "selected" : "" ?>>All</option>
                <option value="Deposit" <?= $filter === "Deposit" ? "selected" : "" ?>>Deposit</option>
                <option value="Withdrawal" <?= $filter === "Withdrawal" ? "selected" : "" ?>>Withdrawal</option>
            </select>

            <label for="transaction_month">Filter by Month:</label>
            <select name="transaction_month" id="transaction_month" class="form-select d-inline w-auto">
                <option value="All" <?= $monthFilter === "All" ? "selected" : "" ?>>All</option>
                <option value="01" <?= $monthFilter === "01" ? "selected" : "" ?>>January</option>
                <option value="02" <?= $monthFilter === "02" ? "selected" : "" ?>>February</option>
                <option value="03" <?= $monthFilter === "03" ? "selected" : "" ?>>March</option>
                <option value="04" <?= $monthFilter === "04" ? "selected" : "" ?>>April</option>
                <option value="05" <?= $monthFilter === "05" ? "selected" : "" ?>>May</option>
                <option value="06" <?= $monthFilter === "06" ? "selected" : "" ?>>June</option>
                <option value="07" <?= $monthFilter === "07" ? "selected" : "" ?>>July</option>
                <option value="08" <?= $monthFilter === "08" ? "selected" : "" ?>>August</option>
                <option value="09" <?= $monthFilter === "09" ? "selected" : "" ?>>September</option>
                <option value="10" <?= $monthFilter === "10" ? "selected" : "" ?>>October</option>
                <option value="11" <?= $monthFilter === "11" ? "selected" : "" ?>>November</option>
                <option value="12" <?= $monthFilter === "12" ? "selected" : "" ?>>December</option>
            </select>

            <label for="search_staff_id">Search by Staff ID:</label>
            <input type="text" name="search_staff_id" id="search_staff_id" class="form-control d-inline w-auto" placeholder="Enter staff ID" value="<?= htmlspecialchars($searchStaffId) ?>">

            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        <!-- Transaction Table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Staff ID</th>
                    <th>User</th>
                    <th>Transaction Type</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($transactions)): ?>
                    <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td><?= htmlspecialchars($transaction['id']) ?></td>
                            <td><?= htmlspecialchars($transaction['staff_id']) ?></td>
                            <td><?= htmlspecialchars($transaction['name']) ?></td>
                            <td><?= htmlspecialchars($transaction['transaction_type']) ?></td>
                            <td><?= htmlspecialchars($transaction['amount']) ?></td>
                            <td><?= htmlspecialchars($transaction['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center;">No transactions found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
 