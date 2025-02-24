<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['name'])) {
    header('Location: index.html');
    exit;
}

// Connect to the database
require "db.php";

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $staff_id = trim($_POST["staff_id"] ?? '');
    $amount = floatval($_POST["amount"] ?? 0); // Ensure it's a valid number

    // Validation
    if (empty($staff_id) || $amount <= 0) {
        echo "Invalid input. Please provide a valid staff ID and a positive amount.";
        exit();
    }

    try {
        // Begin transaction
        $pdo->beginTransaction();

        // Check if the staff ID exists in table_1
        $stmt = $pdo->prepare("SELECT id, name, balance FROM table_1 WHERE staff_id = ?");
        $stmt->execute([$staff_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new Exception("Staff ID not found.");
        }

        // Retrieve current balance and name
        $user_id = $user['id'];
        $name = $user['name'];
        $currentBalance = $user['balance'];

        // Calculate new balance
        $newBalance = $currentBalance + $amount;

        // Update the balance in table_1
        $stmt = $pdo->prepare("UPDATE table_1 SET balance = ? WHERE staff_id = ?");
        $stmt->execute([$newBalance, $staff_id]);

        // Insert the transaction into `transactions` table
        $stmt = $pdo->prepare("INSERT INTO transactions (staff_id, name, amount, transaction_type) VALUES (?, ?, ?, 'deposit')");
        $stmt->execute([$staff_id, $name, $amount]);

        // Log the deposit in `transaction_log`
        $stmt = $pdo->prepare("INSERT INTO transaction_log (user_id, action, old_balance, new_balance, changed_by) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, 'Deposit', $currentBalance, $newBalance, $_SESSION['user_id']]);

        // Commit transaction
        $pdo->commit();

        // Display success message with styled background and form
        echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Transaction Successful</title>
            <style>
                body {
                    background: url('img/po.jpeg') no-repeat center center fixed;
                    background-size: cover;
                    font-family: Arial, sans-serif;
                    text-align: center;
                    margin: 0;
                    padding: 0;
                }
                .container {
                    margin-top: 100px;
                    padding: 20px;
                    background-color: rgba(255, 255, 255, 0.9);
                    border-radius: 10px;
                    display: inline-block;
                }
                a, button {
                    display: inline-block;
                    margin-top: 20px;
                    padding: 10px 20px;
                    background-color: #4CAF50;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                    border: none;
                    cursor: pointer;
                }
                button:hover, a:hover {
                    background-color: #45a049;
                }
            </style>
            <script>
                function printTransaction() {
                    window.print();
                }
            </script>
        </head>
        <body>
            <div class='container'>
                <h2>Transaction Successful!</h2>
                <p>Transaction recorded successfully for <strong>$name</strong>.</p>
                <p>Amount deposited: Gh₵ <strong>$amount</strong></p>
                <p>New balance: Gh₵ <strong>$newBalance</strong></p>
                <button onclick='printTransaction()'>Print Transaction</button>
                <form action='dashboard.php' method='GET'>
                    <button type='submit'>Go to Home</button>
                </form>
            </div>
        </body>
        </html>";
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>
