<?php
session_start();
require '../db.php';

// Ensure only the admin can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.html');
    exit();
}

// Fetch name change history
$stmt = $pdo->query("SELECT uc.user_id, t.name AS current_name, uc.old_name, uc.new_name, uc.change_date, COUNT(uc.id) AS change_count 
    FROM username_changes uc
    JOIN table_1 t ON uc.user_id = t.id
    GROUP BY uc.user_id, uc.old_name, uc.new_name, uc.change_date");

$nameChanges = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Name Change History</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 20px; }
        table { width: 80%; margin: auto; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        th { background-color: #4CAF50; color: white; }
    </style>
</head>

<body>
    <h2>Name Change History</h2>
    <table>
        <tr>
            <th>User ID</th>
            <th>Current Name</th>
            <th>Old Name</th>
            <th>New Name</th>
            <th>Change Date</th>
            <th>Times Modified</th>
        </tr>
        <?php foreach ($nameChanges as $change): ?>
        <tr>
            <td><?= htmlspecialchars($change['user_id']) ?></td>
            <td><?= htmlspecialchars($change['current_name']) ?></td>
            <td><?= htmlspecialchars($change['old_name']) ?></td>
            <td><?= htmlspecialchars($change['new_name']) ?></td>
            <td><?= htmlspecialchars($change['change_date']) ?></td>
            <td><?= htmlspecialchars($change['change_count']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
