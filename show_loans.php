<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['name'])) {
    // If not logged in, redirect to the login page
    header('Location: index.html');
    exit;
}
// Include database connection file
include('db.php'); // Assuming you have a file that connects to the database using PDO
$loans = []; 
// Prepare query for filtering based on user input
$query = "SELECT * FROM loans WHERE 1";

if (isset($_POST['filter'])) {
    if (!empty($_POST['name'])) {
        $name = $_POST['name'];
        $query .= " AND name LIKE :name";
    }

    if (!empty($_POST['month'])) {
        $month = $_POST['month'];
        $query .= " AND MONTH(loan_date) = :month";
    }

    if (!empty($_POST['staff_id'])) {
        $staff_id = $_POST['staff_id'];
        $query .= " AND staff_id LIKE :staff_id";
    }
}

$stmt = $pdo->prepare($query);

// Bind parameters if set
if (isset($name)) {
    $stmt->bindValue(':name', '%' . $name . '%');
}
if (isset($month)) {
    $stmt->bindValue(':month', $month);
}
if (isset($staff_id)) {
    $stmt->bindValue(':staff_id', '%' . $staff_id . '%');
}

$stmt->execute();
$loans = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Loans</title>
    <style>
        body {
            background-image:url('img/y.jpeg');
            background-color:white;
            font-family: Arial, sans-serif;
            margin: 39px;
            padding: 59px;
        }
        .container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white; /* Added white background */
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
            background-color: white; /* Added white background for cells */
        }
        th {
            background-color: #f2f2f2;
        }
        .filter-form input, .filter-form select {
            padding: 8px;
            margin: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>

<h2>View All Loans</h2>
<div class="actions">
    <a href="dashboard.php" style='display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>Go to Home </a>
    <button onclick="window.print()">Print</button>
</div>

<!-- Filter Form -->
<form method="POST" class="filter-form">
    <label for="name">Name:</label>
    <input type="text" id="name" name="name">

    <label for="month">Month:</label>
    <select name="month" id="month">
        <option value="">Select Month</option>
        <?php for ($i = 1; $i <= 12; $i++) { ?>
            <option value="<?php echo $i; ?>"><?php echo date('F', mktime(0, 0, 0, $i, 10)); ?></option>
        <?php } ?>
    </select>

    <label for="staff_id">Staff ID:</label>
    <input type="text" id="staff_id" name="staff_id">

    <button type="submit" name="filter">Filter</button>
</form>

<!-- Loans Table -->
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Staff ID</th>
            <th>District</th>
            <th>Rank</th>
            <th>Amount Applied</th>
            <th>Loan Date</th>
            <th>Momo No</th>
            <th>Cheque No</th>
            <th>Received By</th>
            <th>Date Received</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($loans) > 0) : ?>
            <?php foreach ($loans as $loan) : ?>
                <tr>
                    <td><?php echo $loan['id']; ?></td>
                    <td><?php echo $loan['name']; ?></td>
                    <td><?php echo $loan['phone']; ?></td>
                    <td><?php echo $loan['staff_id']; ?></td>
                    <td><?php echo $loan['district']; ?></td>
                    <td><?php echo $loan['rank']; ?></td>
                    <td><?php echo $loan['amount_applied']; ?></td>
                    <td><?php echo $loan['loan_date']; ?></td>
                    <td><?php echo $loan['momo_no']; ?></td>
                    <td><?php echo $loan['cheque_no']; ?></td>
                    <td><?php echo $loan['received_by']; ?></td>
                    <td><?php echo $loan['date_received']; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="12">No loans found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
