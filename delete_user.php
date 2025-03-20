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

    try {
        // Delete the user from the database
        $stmt = $pdo->prepare("DELETE FROM table_1 WHERE id = ?");
        $stmt->execute([$id]);

        // Redirect back to register.html with a success message
        header("Location: show_register.php?message=User Deleted Successfully");
        exit();
    } catch (PDOException $e) {
        die("Error deleting user: " . $e->getMessage());
    }
} else {
    // Redirect if no ID is provided
    header("Location: registered_users.php");
    exit();
}
?>
