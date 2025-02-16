<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['name'])) {
    // If not logged in, redirect to the login page
    header('Location: index.html');
    exit;
}
require 'db.php'; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $sql = "INSERT INTO loans (name, phone, staff_id, district, rank, amount_applied, amount_in_words, loan_date, momo_no, cheque_no, received_by, date_received, remarks) 
                VALUES (:name, :phone, :staff_id, :district, :rank, :amount_applied, :amount_in_words, :loan_date, :momo_no, :cheque_no, :received_by, :date_received, :remarks)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name' => $_POST['name'],
            ':phone' => $_POST['phone'],
            ':staff_id' => $_POST['staff_id'],
            ':district' => $_POST['district'],
            ':rank' => $_POST['rank'],
            ':amount_applied' => $_POST['amount_applied'],
            ':amount_in_words' => $_POST['amount_in_words'],
            ':loan_date' => $_POST['loan_date'],
            ':momo_no' => $_POST['momo_no'],
            ':cheque_no' => $_POST['cheque_no'],
            ':received_by' => $_POST['received_by'],
            ':date_received' => $_POST['date_received'],
            ':remarks' => $_POST['remarks']
        ]);

        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
}
?>
