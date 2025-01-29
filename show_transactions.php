<?php
require "db.php";

$transactions = [];
$filter = "All"; 
$monthFilter = "All"; 
$searchStaffId = ""; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filter = $_POST['transaction_type'] ?? "All";
    $monthFilter = $_POST['transaction_month'] ?? "All";
    $searchStaffId = $_POST['search_staff_id'] ?? ""; 

    try {
        $query = "SELECT * FROM transactions WHERE 1"; 

        if ($filter !== "All") {
            $query .= " AND transaction_type = ?";
        }
        
        if ($monthFilter !== "All") {
            $query .= " AND MONTH(created_at) = ?";
        }

        if (!empty($searchStaffId)) {
            $query .= " AND staff_id LIKE ?";
        }

        $stmt = $pdo->prepare($query);

        $params = [];
        if ($filter !== "All") {
            $params[] = $filter;
        }
        if ($monthFilter !== "All") {
            $params[] = $monthFilter;
        }
        if (!empty($searchStaffId)) {
            $params[] = "%" . $searchStaffId . "%";
        }

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
        }
        .container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .filter-form {
            margin-bottom: 20px;
        }
        .actions {
            text-align: center;
            margin-bottom: 20px;
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
        th {
            background-color: #4CAF50;
            color: white;
        }
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .filter-form select, .filter-form input, .filter-form button {
                width: 100%;
                margin-bottom: 10px;
            }
            .actions a, .actions button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Transaction Management</h2>

        <!-- Navigation -->
        <div class="actions">
            <a href="dashboard.html">Go to Home</a>
            <button onclick="window.print()">Print</button>
        </div>

        <!-- Filter Form -->
        <form method="POST" class="filter-form row g-2">
            <div class="col-md-4 col-12">
                <label for="transaction_type">Filter by Type:</label>
                <select name="transaction_type" id="transaction_type" class="form-select">
                    <option value="All" <?= $filter === "All" ? "selected" : "" ?>>All</option>
                    <option value="Deposit" <?= $filter === "Deposit" ? "selected" : "" ?>>Deposit</option>
                    <option value="Withdrawal" <?= $filter === "Withdrawal" ? "selected" : "" ?>>Withdrawal</option>
                </select>
            </div>
            <div class="col-md-4 col-12">
                <label for="transaction_month">Filter by Month:</label>
                <select name="transaction_month" id="transaction_month" class="form-select">
                    <option value="All" <?= $monthFilter === "All" ? "selected" : "" ?>>All</option>
                    <?php
                    $months = [
                        "01" => "January", "02" => "February", "03" => "March", "04" => "April",
                        "05" => "May", "06" => "June", "07" => "July", "08" => "August",
                        "09" => "September", "10" => "October", "11" => "November", "12" => "December"
                    ];
                    foreach ($months as $key => $value) {
                        $selected = $monthFilter === $key ? "selected" : "";
                        echo "<option value='$key' $selected>$value</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-4 col-12">
                <label for="search_staff_id">Search by Staff ID:</label>
                <input type="text" name="search_staff_id" id="search_staff_id" class="form-control" placeholder="Enter staff ID" value="<?= htmlspecialchars($searchStaffId) ?>">
            </div>
            <div class="col-12 text-center">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>

        <!-- Transaction Table -->
        <div class="table-responsive">
            <table class="table table-bordered">
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
                        <tr><td colspan="6" class="text-center">No transactions found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
