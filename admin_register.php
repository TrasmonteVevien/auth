<?php
include 'config.php';
session_start();

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Check if passwords match
    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Check if username already exists
        $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error_message = "Username already exists. Please choose another.";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new admin into database
            $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
            if ($stmt->execute([$username, $hashed_password])) {
                // Redirect to login page
                header("Location: admin_login.php");
                exit();
            } else {
                $error_message = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Registration</title>
    <style>
       body {
        background-color: #336699; /* lighter blue background */
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
        margin: 0;
        font-family: Arial, sans-serif;
        color: #336699; /* match lighter blue text */   
        }

        .register-container {
            background-color: #ffffff; /* white background */
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
            border: 3px solid #ffd633; /* yellow border */
        }

        h2 {
            margin-bottom: 20px;
            color: #004080; /* dark blue */
        }

        input[type="text"],
        input[type="password"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border: 2px solid #ffd633; /* yellow border */
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #ffeb3b; /* bright yellow on focus */
            outline: none;
            background-color: #fffde7; /* pale yellow background */
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #8e44ad; /* purple button */
            color: #ffffff; /* white text */
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
            font-weight: bold;
        }

        button:hover {
            background-color: #7d3c98; /* darker purple */
        }

        .error-message {
            color: #cc3300; /* dark red for errors */
            margin: 10px 0;
        }

        a {
            color: #8e44ad; /* purple link */
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
            color: #7d3c98;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Admin Registration</h2>
        <?php if (isset($error_message)) : ?>
            <div class="error-message"><?= htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required /><br />
            <input type="password" name="password" placeholder="Password" required /><br />
            <input type="password" name="confirm_password" placeholder="Confirm Password" required /><br />
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="admin_login.php">Login here</a></p>
    </div>
</body>
</html>
