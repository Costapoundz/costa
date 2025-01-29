<?php
// Connect to the database
require "db.php";

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $name = htmlspecialchars($_POST["name"]);
    $staff_id = htmlspecialchars($_POST["staff_id"]);
    $amount = floatval($_POST["amount"]);

    // Validate input
    if (empty($name) || empty($staff_id) || !is_numeric($amount) || $amount <= 0) {
        echo "Please input a valid Staff ID and a positive amount.";
        exit;
    }

    try {
        // Begin transaction
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("SELECT * FROM table_1 WHERE staff_id = ? AND name = ?");
        $stmt->execute([$staff_id, $name]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Check if the user has enough balance
            if ($user['balance'] >= $amount) {
                $newBalance = $user['balance'] - $amount;

                // Update user balance
                $stmt = $pdo->prepare("UPDATE table_1 SET balance = ? WHERE staff_id = ?");
                $stmt->execute([$newBalance, $staff_id]);

                // Log the withdrawal transaction
                $stmt = $pdo->prepare("INSERT INTO transactions (staff_id, name, amount, transaction_type) VALUES (?, ?, ?, 'withdrawal')");
                $stmt->execute([$staff_id, $name, $amount]);
                $pdo->commit();

                // Success message with print and return options
                echo "
                <!DOCTYPE html>
                <html lang='en'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>Transaction Successful</title>
                    <style>
                        body {
                            background: url('img/y.jpeg') no-repeat center center fixed;
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
                        .print-btn {
                            margin-top: 20px;
                            background-color: #007BFF;
                        }
                        .print-btn:hover {
                            background-color: #0056b3;
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
                        <p>Amount withdrawn: GHC <strong>$amount</strong></p>
                        <p>New balance: GHC <strong>$newBalance</strong></p>
                        <button class='print-btn' onclick='printTransaction()'>Print Transaction</button>
                        <form action='dashboard.html' method='GET'>
                            <button type='submit'>Go to Home</button>
                        </form>
                    </div>
                </body>
                </html>";
            } else {
                // Insufficient balance message
                echo "
                <div style='text-align: center; margin-top: 50px;'>
                    <h2>Transaction Failed!</h2>
                    <p>Insufficient balance for withdrawal.</p>
                    <a href='dashboard.html'>Go to Home</a>
                </div>";
                $pdo->rollBack();
            }
        } else {
            echo "Staff ID $staff_id not found.";
            $pdo->rollBack();
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "An error occurred: " . $e->getMessage();
    }
}
?>
