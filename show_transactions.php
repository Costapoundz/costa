<?php
require "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['show_transactions'])) {
    try {
        // Fetch all transactions from the database
        $stmt = $pdo->query("SELECT * FROM transactions ORDER BY id DESC");
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Check if transactions exist
        if (empty($transactions)) {
            echo "<p style='text-align: center; margin-top: 20px;'>No transactions found.</p>";
        } else {
            // Generate the table with transaction data
            echo "
            <div style='margin: 20px;'>
                <h2 style='text-align: center;'>All Transactions</h2>
                <table border='1' style='width: 100%; border-collapse: collapse; text-align: left;'>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Staff ID</th>
                            <th>Name</th>
                            <th>Amount</th>
                            <th>Transaction Type</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>";
            foreach ($transactions as $transaction) {
                echo "
                    <tr>
                        <td>" . htmlspecialchars($transaction['id']) . "</td>
                        <td>" . htmlspecialchars($transaction['staff_id']) . "</td>
                        <td>" . htmlspecialchars($transaction['name']) . "</td>
                        <td>" . htmlspecialchars($transaction['amount']) . "</td>
                        <td>" . htmlspecialchars($transaction['transaction_type']) . "</td>
                        <td>" . htmlspecialchars($transaction['created_at']) . "</td>
                    </tr>";
            }
            echo "
                    </tbody>
                </table>
                <a href='transaction.html' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>hoome</a>
            </div>";
        }
    } catch (Exception $e) {
        echo "<p style='color: re   d; text-align: center;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    // Redirect to the form if the request is invalid
    echo "
        <div style='text-align: center; margin-top: 50px;'>
           
            <a href='index.php' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>Go to Home</a>
        </div>";
}
?>