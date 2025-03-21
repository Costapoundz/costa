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

// Get unique years from the database for filtering
$yearsStmt = $pdo->query("SELECT DISTINCT YEAR(date) AS year FROM other_loans ORDER BY year DESC");
$years = $yearsStmt->fetchAll(PDO::FETCH_COLUMN);

// Define month options
$months = [
    '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April',
    '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August',
    '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
];
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
        .form-container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
        }
        .table-container {
            background: rgba(0, 0, 0, 0.7);
            padding: 20px;
            border-radius: 10px;
        }
        .table {
            color: white;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">View Other Loans</h1>
        <div>
            <a href="loans.html" class="btn btn-primary"> Back </a>
        </div>
        <div class="form-container">
            <form method="POST" action="view_loans.php">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>">
                </div>
                <div class="mb-3">
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
                <div class="mb-3">
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
                <div class="mb-3">
                    <label for="staff_id" class="form-label">Staff ID</label>
                    <input type="text" class="form-control" id="staff_id" name="staff_id" value="<?php echo htmlspecialchars($staff_id); ?>">
                </div>
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </form>
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
                                <td><?php echo htmlspecialchars($loan['amount']); ?></td>
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
</body>
</html>
