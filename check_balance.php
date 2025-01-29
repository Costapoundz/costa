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
            // Display the user's name and balance with background and print option
            echo "
            <!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Balance Check</title>
                <style>
                    body {
                        background: url('img/i.png') no-repeat center center fixed;
                        background-size: cover;
                        font-family: Arial, sans-serif;
                        margin: 0;
                        padding: 0;
                        text-align: center;
                    }
                    .container {
                        margin-top: 100px;
                        padding: 20px;
                        background-color: rgba(255, 255, 255, 0.9);
                        border-radius: 10px;
                        display: inline-block;
                        text-align: center;
                    }
                    button, a {
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
            </head>
            <body>
                <div class='container' id='printArea'>
                    <h2>Balance Check Successful</h2>
                    <p><strong>User:</strong> " . htmlspecialchars($user['name']) . "</p>
                    <p><strong>Balance:</strong> Gh₵ " . htmlspecialchars($user['balance']) . "</p>
                    <button onclick='printContent()'>Print</button>
                    <form action='dashboard.html' method='GET'>
                        <button type='submit'>Go to Home</button>
                    </form>
                </div>
                <script>
                    function printContent() {
                        const printArea = document.getElementById('printArea').innerHTML;
                        const originalContents = document.body.innerHTML;
                        document.body.innerHTML = printArea;
                        window.print();
                        document.body.innerHTML = originalContents;
                    }
                </script>
            </body>
            </html>";
        } else {
            echo "<p style='color: red; text-align: center;'>No user found with Staff ID: $staff_id.</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red; text-align: center;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>
