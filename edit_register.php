<?php
session_start();
require 'db.php';

// Ensure only the admin can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: index.html');
    exit();
}

// Get the user ID from URL
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    die("Invalid User ID.");
}

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM table_1 WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

// Handle update request
$message = '';
$showModal = false;
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name'] ?? '');
    $staff_id = trim($_POST['staff_id'] ?? '');

    if (!empty($name) && !empty($staff_id)) {
        // Fetch the current user data
        $stmt = $pdo->prepare("SELECT name FROM table_1 WHERE id = ?");
        $stmt->execute([$id]);
        $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($currentUser && $currentUser['name'] !== $name) {
            // Insert name change into username_changes table
            $logStmt = $pdo->prepare("INSERT INTO username_changes (user_id, old_name, new_name, changed_by) VALUES (?, ?, ?, ?)");
            $logStmt->execute([$id, $currentUser['name'], $name, $_SESSION['user_id']]);
        }
    
        // Update the user record
        $stmt = $pdo->prepare("UPDATE table_1 SET name = ?, staff_id = ? WHERE id = ?");
        if ($stmt->execute([$name, $staff_id, $id])) {
            $showModal = true; // Show success modal
        } else {
            $message = "<p style='color: red; text-align: center;'>Failed to update user.</p>";
        }
    }
}   
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <style>
        body {
            background: url('img/y.jpeg') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            max-width: 400px;
            width: 100%;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        h2 {
            color: #333;
        }
        label {
            display: block;
            text-align: left;
            margin-top: 10px;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .btn-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .btn {
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            width: 48%;
            text-align: center;
        }
        .update-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .update-btn:hover {
            background-color: #45a049;
        }
        .cancel-btn {
            background-color: #ff4c4c;
            color: white;
            text-decoration: none;
            display: inline-block;
        }
        .cancel-btn:hover {
            background-color: #e63c3c;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            width: 300px;
        }
        .modal button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 20px;
            margin-top: 10px;
            cursor: pointer;
            border-radius: 5px;
        }
        .modal button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit User</h2>
        <?= $message ?>
        <form method="POST">
            <label>Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
            
            <label>Staff ID:</label>
            <input type="text" name="staff_id" value="<?= htmlspecialchars($user['staff_id']) ?>" required>

            <div class="btn-container">
                <button type="submit" class="btn update-btn">Update</button>
                <a href="show_register.php" class="btn cancel-btn">Cancel</a>
            </div>
        </form>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <h3>Success!</h3>
            <p>User updated successfully.</p>
            <button onclick="window.location.href='show_register.php'">Back</button>
        </div>
    </div>

    <script>
        // Show the modal if update was successful
        <?php if ($showModal) : ?>
            document.getElementById('successModal').style.display = 'flex';
        <?php endif; ?>
    </script>
</body>
</html>
