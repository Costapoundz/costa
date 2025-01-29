<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['name'])) {
    // If not logged in, redirect to the login page
    header('Location: index.html');
    exit;
}


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
                  <button class='print-btn' onclick='printTransaction()'>Print Transaction</button>
                        <form action='dashboard.php' method='GET'>
                           
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
