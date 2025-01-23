<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["check_balance"])) {
    $staff_id = $_POST["staff_id"] ?? '';

    // Check if the staff ID is provided
    if (empty($staff_id)) {
        echo "<p style='color: red; text-align: center;'>Please enter a Staff ID.</p>";
        exit();
    }

    try {
        // Fetch the user's data from the database
        $stmt = $pdo->prepare("SELECT name, balance FROM table_1 WHERE staff_id = ?");
        $stmt->execute([$staff_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Display the user's name and balance
            echo "<div style='text-align: center; margin-top: 20px;'>
                    <h3>User: " . htmlspecialchars($user['name']) . "</h3>
                    <h4>Balance:  Gh₵" . htmlspecialchars($user['balance']) . "</h4>
                                <a href='index.html' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>Go to Home</a>
                  </div>";
        } else {
            echo "<p style='color: red; text-align: center;'>No user found with Staff ID: $staff_id.</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red; text-align: center;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>
