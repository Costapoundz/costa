<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['name'])) {
    header('Location: index.html');
    exit;
}

// Fetch all users from table_1
$stmt = $pdo->query("SELECT staff_id, name, balance FROM table_1");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Variables for balance check response
$userName = "";
$userBalance = "";
$showModal = false;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["check_balance"])) {
    $staff_id = $_POST["staff_id"] ?? '';

    if (!empty($staff_id)) {
        try {
            $stmt = $pdo->prepare("SELECT name, balance FROM table_1 WHERE staff_id = ?");
            $stmt->execute([$staff_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $userName = htmlspecialchars($user['name']);
                $userBalance = "Gh₵ " . number_format($user['balance'], 2);
                $showModal = true; // Show the modal
            } else {
                $userName = "No user found with Staff ID: " . htmlspecialchars($staff_id);
                $userBalance = "";
                $showModal = true; // Show error in the modal
            }
        } catch (Exception $e) {
            $userName = "Error: " . htmlspecialchars($e->getMessage());
            $showModal = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Balance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Background image */
        body {
            background: url('img/y.jpeg') no-repeat center center fixed;
            background-size: cover;
            color: white;
        }
        

        .table-container {
            margin-top: 30px;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
        }

        /* Print Area Styling */
        #printArea {
            text-align: center;
            padding: 30px;
            background: white;
            color: black;
            border-radius: 10px;
            border: 2px solid #000;
            width: 300px;
            margin: auto;
            font-family: Arial, sans-serif;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }
        

        #printArea h4 {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        #printArea p {
            font-size: 16px;
            margin: 5px 0;
        }

        /* Center Print Content on Paper */
        @media print {
            body * {
                visibility: hidden;
            }
            #printArea, #printArea * {
                visibility: visible;
            }
            #printArea {
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%);
                width: 300px;
                padding: 20px;
                box-shadow: none;
            }
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">Check Your Balance</h1>
        <div>
    <button  onclick="window.location.href='dashboard.php'" 
        class="btn-back" 
        style="background-color: green; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Back</button>
    </div>
        <form method="POST" action="" class="mt-4">
            <div class="mb-3">
                <label for="staff_id" class="form-label">Staff ID:</label>
                <input type="text" id="staff_id" name="staff_id" class="form-control" placeholder="Enter your Staff ID" required>
            </div>
            <div class="text-center">
                <button type="submit" name="check_balance" class="btn btn-primary">Check Balance</button>
            </div>
        </form>
    </div>

    <!-- Modal (Popup for Balance) -->
    <div class="modal fade" id="balanceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Balance Check Result</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="closeModal()"></button>
                </div>
                <div class="modal-body">
                    <div id="printArea">
                        <h4><?php echo $userName; ?></h4>
                        <p><strong>Balance:</strong> <?php echo $userBalance; ?></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button onclick="printContent()" class="btn btn-success">Print</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="closeModal()">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Users List Table -->
    <div class="container mt-5 table-container">
        <h2 class="text-center text-dark">Users and Balances</h2>
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Staff ID</th>
                    <th>Name</th>
                    <th>Balance (₵)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['staff_id']); ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td>₵<?php echo number_format($user['balance'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- JavaScript for Modal & Print -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function printContent() {
            const modal = document.getElementById('balanceModal');
            if (modal.classList.contains('show')) {
                const printWindow = window.open('', '_blank');
                const printArea = document.getElementById("printArea").innerHTML;

                printWindow.document.write(`
                    <html>
                        <head>
                            <title>Print Balance</title>
                            <style>
                                body {
                                    font-family: Arial, sans-serif;
                                    text-align: center;
                                    padding: 20px;
                                }
                                h4 {
                                    font-size: 20px;
                                    font-weight: bold;
                                    margin-bottom: 10px;
                                }
                                p {
                                    font-size: 16px;
                                    margin: 5px 0;
                                }
                            </style>
                        </head>
                        <body>
                            ${printArea}
                        </body>
                    </html>
                `);

                printWindow.document.close();
                printWindow.print();
            } else {
                alert("Please check the balance first to open the modal.");
            }
        }

        function closeModal() {
            let modal = document.getElementById('balanceModal');
            let bootstrapModal = bootstrap.Modal.getInstance(modal);
            bootstrapModal.hide();
        }

        <?php if ($showModal): ?>
            let modal = new bootstrap.Modal(document.getElementById('balanceModal'));
            modal.show();
        <?php endif; ?>
    </script>
</body>
</html>