<?php
session_start();
require 'db.php';

// Handle utility submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_utility'])) {
    $utility_name = $_POST['utility_name'];
    $amount = $_POST['amount'];
    $due_date = $_POST['due_date'];

    $stmt = $pdo->prepare("INSERT INTO utilities (utility_name, amount, due_date) VALUES (?, ?, ?)");
    $stmt->execute([$utility_name, $amount, $due_date]);
}

// Handle marking utility as paid
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_paid'])) {
    $utility_id = $_POST['utility_id'];

    $stmt = $pdo->prepare("UPDATE utilities SET is_paid = 1, paid_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->execute([$utility_id]);

    // Redirect to prevent form resubmission
    header("Location: utility_tracker.php");
    exit();
}

// Fetch unpaid utilities
$unpaid_stmt = $pdo->query("SELECT * FROM utilities WHERE is_paid = 0 ORDER BY due_date ASC");
$unpaid_utilities = $unpaid_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Utility Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('img/po.jpeg') no-repeat center center fixed;
            background-size: cover;
        }
        .utility-container {
            background: white;
            background-size: cover;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 20px;
            margin-top: 20px;
        }
        .unpaid-utility {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            margin-bottom: 10px;
            padding: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="utility-container">
                    <h2 class="text-center mb-4">Utility Tracker</h2>
                    <button onclick="window.location.href='dashboard.php'">Back</button>
                    <!-- Utility Input Form -->
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="utility_name" class="form-label">Utility Name</label>
                            <input type="text" class="form-control" id="utility_name" name="utility_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                        </div>
                        <div class="mb-3">
                            <label for="due_date" class="form-label">Due Date</label>
                            <input type="date" class="form-control" id="due_date" name="due_date" required>
                        </div>
                        <button type="submit" name="submit_utility" class="btn btn-primary w-100">Add Utility</button>
                    </form>

                    <!-- Unpaid Utilities Section -->
                    <div class="mt-4">
                        <h4>Unpaid Utilities</h4>
                        <?php if (empty($unpaid_utilities)): ?>
                            <p class="text-muted">No unpaid utilities</p>
                        <?php else: ?>
                            <?php foreach ($unpaid_utilities as $utility): ?>
                                <div class="unpaid-utility d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?php echo htmlspecialchars($utility['utility_name']); ?></strong>
                                        <br>
                                        $<?php echo number_format($utility['amount'], 2); ?> 
                                        | Due: <?php echo date('M d, Y', strtotime($utility['due_date'])); ?>
                                    </div>
                                    <form method="POST" action="" class="m-0">
                                        <input type="hidden" name="utility_id" value="<?php echo $utility['id']; ?>">
                                        <button type="submit" name="mark_paid" class="btn btn-success btn-sm">Mark Paid</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Link to Paid Utilities Page -->
                    <div class="text-center mt-4">
                        <a href="paid_utility.php" class="btn btn-info">View Paid Utilities</a>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
