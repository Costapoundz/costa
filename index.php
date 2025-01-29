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
        // Fetch user data, including the plain text password
        $stmt = $pdo->prepare("SELECT id, name, password FROM user WHERE name = :name AND password = :password");
        $stmt->execute([
            'name' => $name,
            'password' => $password  // Direct comparison (NOT recommended for real security)
        ]);
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Store user data in session
            $_SESSION['name'] = $user['name'];
            $_SESSION['user_id'] = $user['id']; // Assuming user table has an 'id' column

            // Redirect to home page
            header("Location: dashboard.php");
            exit;
        } else {
            // Invalid index
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



