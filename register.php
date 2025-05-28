<?php
include 'config.php';

$username = $password = $phone = $email = "";
$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $phone = trim($_POST["phone"]);
    $email = trim($_POST["email"]);

    if (empty($username) || empty($password) || empty($phone)) {
        $error = "⚠️ Username, password, and phone number are required.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->rowCount() > 0) {
            $error = "❌ Username already taken. Please choose another.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, phone, email) VALUES (?, ?, ?, ?)");
            $result = $stmt->execute([$username, $hashed_password, $phone, $email]);

            if ($result) {
                $success = "✅ Registration successful.";
                $username = $password = $phone = $email = "";
            } else {
                $error = "⚠️ Registration failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | E-Borrowing Book System</title>
<style>
    :root {
        --light-blue: #4682B4;
        --purple: #8e44ad;
        --purple-dark: #732d91;
        --yellow: #f1c40f;
        --white: #ffffff;
    }

    body {
        background-color: #336699; /* lighter blue background */
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
        margin: 0;
        font-family: Arial, sans-serif;
        color: #336699; /* match lighter blue text */    }

    .register-box {
        background-color: var(--white);
        padding: 40px;
        border-radius: 10px;
        width: 100%;
        max-width: 400px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        text-align: center;
        border: 2px solid var(--yellow);
    }

    h2 {
        margin-bottom: 20px;
        color: var(--purple);
    }

    input[type="text"],
    input[type="password"],
    input[type="email"] {
        width: calc(100% - 20px);
        padding: 12px;
        margin: 10px 0;
        border: 2px solid var(--yellow);
        border-radius: 6px;
        font-size: 14px;
    }

    button {
        width: 100%;
        padding: 12px;
        background-color: var(--purple);
        color: var(--white);
        border: 2px solid var(--yellow);
        border-radius: 6px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    button:hover {
        background-color: var(--purple-dark);
    }

    .message {
        margin-top: 15px;
        font-size: 14px;
    }

    .message.error {
        color: red;
    }

    .message.success {
        color: green;
    }

    p a {
        color: var(--purple);
        text-decoration: none;
    }

    p a:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>

<div class="register-box">
    <h2>Create Your Account</h2>

    <?php if ($error): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="message success"><?= $success ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="username" placeholder="Username" value="<?= htmlspecialchars($username) ?>" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="text" name="phone" placeholder="Phone Number" value="<?= htmlspecialchars($phone) ?>" required>
        <input type="email" name="email" placeholder="Email (optional)" value="<?= htmlspecialchars($email) ?>">
        <button type="submit">Register</button>
    </form>

    <p>Already registered? <a href="login.php">Login here</a></p>
</div>

</body>
</html>
