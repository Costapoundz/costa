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
                echo "$amount withdrawn successfully. Current balance: $newBalance";

                 // Display success message and button to redirect
      echo "
      <div style='text-align: center; margin-top: 50px;'>
          <h2>Transaction Successful!</h2>
          <p>Transaction recorded successfully for <strong>$name</strong>.</p>
          <p>Amount withdraw GHC: <strong>$amount</strong>. New balance GHC: <strong>$newBalance</strong>.</p>
          <a href='index.html' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>Go to Home</a>
      </div>";
      
            }
             else {
                echo "Insufficient balance for withdrawal.";
                        // Display success message and button to redirect
      echo "
      <div style='text-align: center; margin-top: 50px;'>
          <h2>Transaction failed!</h2>
          <p>Insufficient balance for withdrawal. .</p>

          <a href='index.html' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>Go to Home</a>
      </div>";
                $pdo->rollBack();
                
            }
            
        } 
        else {
            echo "Staff ID $staff_id not found.";
            $pdo->rollBack();
        }
    } 

     
    catch (Exception $e) {
        $pdo->rollBack();
        echo "An error occurred: " . $e->getMessage();
    }
}
?>
