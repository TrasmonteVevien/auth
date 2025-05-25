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

    // Fetch admin credentials from database
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);
        
        // Set session variables
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];

        // Redirect to admin dashboard
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error_message = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Login</title>
<style>
    body {
        background-color: #004080; /* deep blue background */
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
        margin: 0;
        font-family: Arial, sans-serif;
        color: #004080; /* blue text */
    }
    .login-container {
        background-color: #ffffff; /* white box */
        padding: 30px;
        border-radius: 10px;
        width: 400px;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
        text-align: center;
        border: 3px solid #ffd633; /* yellow border */
    }
    h2 {
        margin-bottom: 20px;
        color: #004080;
        font-weight: bold;
    }
    input[type="text"], input[type="password"] {
        width: calc(100% - 22px);
        padding: 10px;
        margin: 10px 0;
        border: 2px solid #ffd633; /* yellow border */
        border-radius: 6px;
        font-size: 16px;
        box-sizing: border-box;
        transition: border-color 0.3s;
    }
    input[type="text"]:focus, input[type="password"]:focus {
        border-color: #ffeb3b; /* bright yellow on focus */
        outline: none;
        background-color: #fffde7;
    }
    button {
        width: 100%;
        padding: 12px;
        background-color: #ffd633; /* yellow button */
        color: #004080; /* blue text */
        border: none;
        border-radius: 6px;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s;
        margin-top: 10px;
    }
    button:hover {
        background-color: #e6c200; /* darker yellow on hover */
    }
    .error-message {
        color: #cc3300; /* dark red */
        background-color: #fff0b3; /* pale yellow background */
        padding: 10px;
        margin: 15px 0;
        border-radius: 5px;
        font-weight: bold;
    }
</style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <?php if (isset($error_message)) : ?>
            <div class="error-message"><?= htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required /><br />
            <input type="password" name="password" placeholder="Password" required /><br />
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
