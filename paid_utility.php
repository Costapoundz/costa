<?php
session_start();
require 'db.php';

// Initialize filter variables
$filter_name = isset($_GET['filter_name']) ? trim($_GET['filter_name']) : '';
$filter_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : '';
$filter_year = isset($_GET['filter_year']) ? $_GET['filter_year'] : '';

// Build query with dynamic filters
$query = "SELECT * FROM utilities WHERE is_paid = 1";
$params = [];

// Name filter
if (!empty($filter_name)) {
    $query .= " AND utility_name LIKE ?";
    $params[] = "%{$filter_name}%";
}

// Date filter
if (!empty($filter_date)) {
    $query .= " AND DATE(paid_at) = ?";
    $params[] = $filter_date;
}

// Year filter
if (!empty($filter_year)) {
    $query .= " AND YEAR(paid_at) = ?";
    $params[] = $filter_year;
}

// Add sorting
$query .= " ORDER BY paid_at DESC";

// Prepare and execute statement
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$paid_utilities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Generate years for dropdown
$years_stmt = $pdo->query("SELECT DISTINCT YEAR(paid_at) as year FROM utilities WHERE is_paid = 1 ORDER BY year DESC");
$available_years = $years_stmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paid Utilities</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('img/po.jpeg') no-repeat center center fixed;
            background-size: cover;
        }
        .utility-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 20px;
            margin-top: 20px;
        }
        .paid-utility {
            background-color: #d4edda;
            border-left: 4px solid #28a745;
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
                    <h2 class="text-center mb-4">Paid Utilities</h2>
                    <a href="utility.php" class="btn btn-primary mb-3">Back to Utilities</a>
                    
                    <!-- Filter Form -->
                    <form method="GET" action="" class="mb-4">
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <input type="text" name="filter_name" class="form-control" 
                                       placeholder="Filter by Name" 
                                       value="<?php echo htmlspecialchars($filter_name); ?>">
                            </div>
                            <div class="col-md-4 mb-2">
                                <input type="date" name="filter_date" class="form-control" 
                                       placeholder="Filter by Date" 
                                       value="<?php echo htmlspecialchars($filter_date); ?>">
                            </div>
                            <div class="col-md-4 mb-2">
                                <select name="filter_year" class="form-control">
                                    <option value="">Select Year</option>
                                    <?php foreach ($available_years as $year): ?>
                                        <option value="<?php echo $year; ?>" 
                                                <?php echo ($filter_year == $year) ? 'selected' : ''; ?>>
                                            <?php echo $year; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success w-100 mt-2">Apply Filters</button>
                        <a href="paid_utility.php" class="btn btn-secondary w-100 mt-2">Clear Filters</a>
                    </form>

                    <!-- Paid Utilities Section -->
                    <?php if (empty($paid_utilities)): ?>
                        <p class="text-muted">No paid utilities found</p>
                    <?php else: ?>
                        <div class="text-muted mb-2">
                            Showing <?php echo count($paid_utilities); ?> utility(ies)
                        </div>
                        <?php foreach ($paid_utilities as $utility): ?>
                            <div class="paid-utility">
                                <strong><?php echo htmlspecialchars($utility['utility_name']); ?></strong>
                                <br>
                                Reason :<?php echo htmlspecialchars($utility['reason']); ?>
                                        <br>
                                        Payment to: <?php echo htmlspecialchars($utility['payment_to']); ?>
                                GHC<?php echo number_format($utility['amount'], 2); ?> 
                                | Paid: <?php echo date('M d, Y H:i', strtotime($utility['paid_at'])); ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>