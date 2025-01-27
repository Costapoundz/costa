<?php
// Connect to the database
require "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"] ;
    $password = $_POST["password"];

    

    // Validate input
    if (empty($name) || empty($password)) {
        echo "Please enter your username and password.";
        exit;
    }

    try {
        // Prepare the SQL statement
        $stmt = $pdo->prepare("SELECT * FROM user WHERE name = :name AND password = :password");
        $stmt->execute([
            'name' => $name,
            'password' => $password,
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Successful login: Redirect to index.html
            header("Location: index.html");
            exit;
        } else {
            // Invalid login
            echo "Invalid username or password.";
        }
    } catch (PDOException $e) {
        // Handle database errors
        echo "Error: " . $e->getMessage();
    }
}
?>
