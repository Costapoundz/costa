 
<?php
session_start();
require 'db.php'; // Adjust path if needed


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location:index.html');
    exit();
}

try {
    // Fetch all registered users
    $stmt = $pdo->prepare("SELECT * FROM table_1 ORDER BY id DESC");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Users</title>

    <div>
        <button class ="btn btn-primary" onclick="window.location.href='register.html'">Back</button>
    </div>
    <style>
        body {
            background: url('img/y.jpeg') no-repeat center center fixed;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
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
            background: #007BFF;
            color: white;
        }
        .btn {
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
    <div class="container">
        <h2>Registered Users</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Staff ID</th>
                <th>Action</th>
            </tr>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['staff_id']) ?></td>
                <td><a href="edit_register.php?id=<?= $user['id'] ?>" style="color: blue;">Edit</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <a href="dashboard.php" class="btn">Back to Dashboard</a>
    </div>
</body>
</html>
