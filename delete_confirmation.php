<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: index.html');
    exit();
}

// Check if ID is received via POST
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
} else {
    header('Location: register.html');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Deletion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        .container {
            max-width: 400px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }
        .confirm {
            background-color: red;
            color: white;
        }
        .cancel {
            background-color: gray;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Are you sure you want to delete this user?</h2>
        <form action="delete_user.php" method="POST">
            <input type="hidden" name="id" value="<?= $id ?>">
            <button type="submit" class="confirm">Yes, Delete</button>
        </form>
        <form action="register.html">
            <button type="submit" class="cancel">No, Cancel</button>
        </form>
    </div>
</body>
</html>
