<?php
// Connect to the database
require "db.php";

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $name = $_POST["name"] ?? '';
    $staff_id = $_POST["staff_id"] ?? '';
    $amount = $_POST["amount"] ?? '';

    // Validation
    if (empty($staff_id) || !is_numeric($amount) || $amount <= 0) {
        echo "Invalid input. Please provide a valid staff ID and a positive amount.";
        exit();
    }

    try {
        // Begin transaction
        $pdo->beginTransaction();

        // Check if the staff ID exists in table_1
        $stmt = $pdo->prepare("SELECT name, balance FROM table_1 WHERE staff_id = ?");
        $stmt->execute([$staff_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new Exception("Staff ID not found.");
        }

        // Retrieve current balance and name
        $name = $user['name'];
        $currentBalance = $user['balance'];

        // Update the balance in table_1
        $newBalance = $currentBalance + $amount;
        $stmt = $pdo->prepare("UPDATE table_1 SET balance = ? WHERE staff_id = ?");
        $stmt->execute([$newBalance, $staff_id]);

        // Insert the transaction details into the transactions table
        $stmt = $pdo->prepare("INSERT INTO transactions (staff_id, name, amount, transaction_type) VALUES (?, ?, ?, 'deposit')");
        $stmt->execute([$staff_id, $name, $amount]);

        // Commit the transaction
        $pdo->commit();

        // Display success message and button to redirect
        echo "
        <div style='text-align: center; margin-top: 50px;'>
            <h2>Transaction Successful!</h2>
            <p>Transaction recorded successfully for <strong>$name</strong>.</p>
            <p>Amount deposited: <strong>$amount</strong>. New balance: <strong>$newBalance</strong>.</p>
            <a href='index.php' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>Go to Home</a>
        </div>";
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>
