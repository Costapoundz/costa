<?php
require 'db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['name'])) {
    header('Location: index.html');
    exit;
}

// Initialize filter variables
$username = '';
$year = '';
$month = '';
$staff_id = '';

// Prepare the SQL query
$sql = "SELECT * FROM other_loans WHERE 1=1"; // 1=1 helps simplify query conditions

// Add filters if they are set
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['username'])) {
        $username = $_POST['username'];
        $sql .= " AND username LIKE :username";
    }
    if (!empty($_POST['year'])) {
        $year = $_POST['year'];
        $sql .= " AND YEAR(date) = :year";
    }
    if (!empty($_POST['month'])) {
        $month = $_POST['month'];
        $sql .= " AND MONTH(date) = :month";
    }
    if (!empty($_POST['staff_id'])) {
        $staff_id = $_POST['staff_id'];
        $sql .= " AND staff_id = :staff_id";
    }
}

// Prepare and execute the statement
$stmt = $pdo->prepare($sql);

// Bind parameters if they exist
if (!empty($username)) {
    $stmt->bindValue(':username', '%' . $username . '%');
}
if (!empty($year)) {
    $stmt->bindValue(':year', $year);
}
if (!empty($month)) {
    $stmt->bindValue(':month', $month);
}
if (!empty($staff_id)) {
    $stmt->bindValue(':staff_id', $staff_id);
}

$stmt->execute();
$loans = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals
$totalInterest = 0;
$totalAmount = 0;

foreach ($loans as $loan) {
    // Calculate interest amount based on percentage and principal
    $interestRate = floatval($loan['interest']);
    $amount = floatval($loan['amount']);
    $period = intval($loan['period']);
    
    // Calculate total interest based on percentage only (no period)
$interestAmount = ($amount * ($interestRate / 100)); 
$totalInterest += $interestAmount;
$totalAmount += $amount;

}

// Get unique years from the database for filtering
$yearsStmt = $pdo->query("SELECT DISTINCT YEAR(date) AS year FROM other_loans ORDER BY year DESC");
$years = $yearsStmt->fetchAll(PDO::FETCH_COLUMN);

// Define month options
$months = [
    '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April',
    '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August',
    '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
];

// If it's an AJAX request, only return the table and totals
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    // Return the totals and table data for AJAX requests
    ?>
    <div class="summary-container mb-4">
        <div class="card bg-dark text-white">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Loan Summary</h5>
                        <p><strong>Total Interest Generated:</strong> $<?php echo number_format($totalInterest, 2); ?></p>
                        <p><strong>Total Loan Amount:</strong> $<?php echo number_format($totalAmount, 2); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Number of Loans:</strong> <?php echo count($loans); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="table-container mt-4">
        <table class="table table-striped table-dark">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Date</th>
                    <th>Period (Months)</th>
                    <th>Interest Rate (%)</th>
                    <th>Amount</th>
                    <th>Staff ID</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($loans) > 0): ?>
                    <?php foreach ($loans as $loan): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($loan['username']); ?></td>
                            <td><?php echo htmlspecialchars($loan['date']); ?></td>
                            <td><?php echo htmlspecialchars($loan['period']); ?></td>
                            <td><?php echo htmlspecialchars($loan['interest']); ?></td>
                            <td>$<?php echo number_format(floatval($loan['amount']), 2); ?></td>
                            <td><?php echo htmlspecialchars($loan['staff_id']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No loans found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
    exit; // Stop here for AJAX requests
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>View Other Loans</title>
    <style>
        body {
            background: url('img/y.jpeg') no-repeat center center fixed;
            background-size: cover;
            color: white;
        }
        .table-container {
            background: rgba(0, 0, 0, 0.7);
            padding: 20px;
            border-radius: 10px;
        }
        .table {
            color: white;
        }
        .card {
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
        }
        .card-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        .summary-container .card {
            background-color: rgba(0, 0, 0, 0.7);
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">View Other Loans</h1>
        <div class="mb-3">
            <a href="loans.html" class="btn btn-primary"> Back </a>
        </div>
        
        <!-- Loan Summary Section -->
        <div class="summary-container mb-4">
            <div class="card bg-dark text-white">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Loan Summary</h5>
                            <p><strong>Total Interest Generated:</strong> $<?php echo number_format($totalInterest, 2); ?></p>
                            <p><strong>Total Loan Amount:</strong> $<?php echo number_format($totalAmount, 2); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Number of Loans:</strong> <?php echo count($loans); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <h5>Loan Filters</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Username Field -->
                    <div class="col-md-6 col-lg-3 mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>">
                    </div>
                    
                    <!-- Year Field -->
                    <div class="col-md-6 col-lg-3 mb-3">
                        <label for="year" class="form-label">Year</label>
                        <select class="form-control" id="year" name="year">
                            <option value="">Select Year</option>
                            <?php foreach ($years as $y): ?>
                                <option value="<?php echo $y; ?>" <?php echo ($y == $year) ? 'selected' : ''; ?>>
                                    <?php echo $y; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Month Field -->
                    <div class="col-md-6 col-lg-3 mb-3">
                        <label for="month" class="form-label">Month</label>
                        <select class="form-control" id="month" name="month">
                            <option value="">Select Month</option>
                            <?php foreach ($months as $key => $name): ?>
                                <option value="<?php echo $key; ?>" <?php echo ($key == $month) ? 'selected' : ''; ?>>
                                    <?php echo $name; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Staff ID Field -->
                    <div class="col-md-6 col-lg-3 mb-3">
                        <label for="staff_id" class="form-label">Staff ID</label>
                        <input type="text" class="form-control" id="staff_id" name="staff_id" value="<?php echo htmlspecialchars($staff_id); ?>">
                    </div>
                </div>
                
                <!-- Filter Button -->
                <div class="row">
                    <div class="col-12">
                        <button type="button" id="filter-btn" class="btn btn-primary" onclick="submitFilter()">Filter</button>
                        <button type="button" id="reset-btn" class="btn btn-outline-secondary ms-2" onclick="resetFilters()">Reset</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Container - Initial Table -->
        <div id="results-container">
            <div class="table-container mt-4">
                <table class="table table-striped table-dark">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Date</th>
                            <th>Period (Months)</th>
                            <th>Interest Rate (%)</th>
                            <th>Amount</th>
                            <th>Staff ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($loans) > 0): ?>
                            <?php foreach ($loans as $loan): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($loan['username']); ?></td>
                                    <td><?php echo htmlspecialchars($loan['date']); ?></td>
                                    <td><?php echo htmlspecialchars($loan['period']); ?></td>
                                    <td><?php echo htmlspecialchars($loan['interest']); ?></td>
                                    <td>$<?php echo number_format(floatval($loan['amount']), 2); ?></td>
                                    <td><?php echo htmlspecialchars($loan['staff_id']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No loans found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function submitFilter() {
            // Collect all filter values
            const username = document.getElementById('username').value;
            const year = document.getElementById('year').value;
            const month = document.getElementById('month').value;
            const staffId = document.getElementById('staff_id').value;
            
            // Create form data
            const formData = new FormData();
            formData.append('username', username);
            formData.append('year', year);
            formData.append('month', month);
            formData.append('staff_id', staffId);
            
            // Show loading indicator
            document.getElementById('results-container').innerHTML = '<div class="text-center"><div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div></div>';
            
            // Submit using fetch with AJAX header
            fetch('view_loans.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(data => {
                // Update the page with results
                document.getElementById('results-container').innerHTML = data;
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('results-container').innerHTML = '<div class="alert alert-danger">Error loading data. Please try again.</div>';
            });
        }
        
        function resetFilters() {
            document.getElementById('username').value = '';
            document.getElementById('year').value = '';
            document.getElementById('month').value = '';
            document.getElementById('staff_id').value = '';
            submitFilter(); // Reload with no filters
        }
    </script>
</body>
</html>