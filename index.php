<?php
session_start();
require "db.php"; // Ensure this correctly initializes $pdo

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $password = trim($_POST["password"]);

    // Validate input
    if (empty($name) || empty($password)) {
        echo "Please enter your username and password.";
        exit;
    }

    try {
        // Fetch user data, including role
        $stmt = $pdo->prepare("SELECT id, name, password, role FROM user WHERE name = :name");
        $stmt->execute(['name' => $name]);
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify user exists and password is correct
        if ($user && $user['password'] === $password) { // NOTE: Use password_hash() for real security
            $_SESSION['name'] = $user['name'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role']; // Store role in session

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location:admin/admin_dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit;
        } else {
            echo "<script>alert('Invalid username or password.'); window.location.href = 'index.html';</script>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>



            <!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>login Failed</title>
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
                </style>
            </head>
            <body>
                <div class='container'>
                    <h2>Invalid username or password</h2>
                    <form action='index.html' method='GET'>
                        <button type='submit'>Go to Home</button>
                    </form>
                </div>
            </body>
            </html>



