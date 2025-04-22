<?php
include 'config.php';
session_start();

if (!isset($_SESSION['failed_attempts'])) $_SESSION['failed_attempts'] = 0;
$username = $_SESSION['last_username'] ?? '';
$error_message = '';
$show_reset_popup = false;
$lockout_remaining = 0;

if (isset($_SESSION['lockout_time'])) {
    $time_diff = time() - $_SESSION['lockout_time'];
    if ($time_diff < 10) {
        $lockout_remaining = 10 - $time_diff;
        $error_message = "⏳ Failed attempt. Please wait <span id='timer'>$lockout_remaining</span> seconds.";
    } else {
        unset($_SESSION['lockout_time']);
        $_SESSION['failed_attempts'] = 0;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $lockout_remaining === 0) {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = trim($_POST['username']);
        $_SESSION['last_username'] = $username;
        $password = trim($_POST['password']);
        $ip = $_SERVER['REMOTE_ADDR'];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['failed_attempts'] = 0;
            unset($_SESSION['last_username'], $_SESSION['lockout_time']);
            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['failed_attempts']++;
            $pdo->prepare("INSERT INTO login_attempts (username, ip_address, attempt_time) VALUES (?, ?, NOW())")
                ->execute([$username, $ip]);

            if ($_SESSION['failed_attempts'] >= 2) {
                $_SESSION['lockout_time'] = time();
                $lockout_remaining = 10;
                $error_message = "⏳ Failed attempt. Please wait <span id='timer'>$lockout_remaining</span> seconds.";
            } else {
                $error_message = "⚠️ Invalid credentials. Attempt {$_SESSION['failed_attempts']} of 1.";
                $show_reset_popup = true;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <style>
        body {
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .login-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
        }
        input[type="text"], input[type="password"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #cccccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message.warning {
            color: red;
            margin: 10px 0;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 100;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 400px;
            box-shadow: 0 0 15px rgba(0,0,0,0.3);
            text-align: center;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 22px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: #000;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Login</h2>

        <?php if ($error_message): ?>
            <div class="message warning"><?= $error_message ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="text" name="username" placeholder="Username" value="<?= htmlspecialchars($username) ?>" required <?= $lockout_remaining ? 'disabled' : '' ?>>
            <input type="password" name="password" placeholder="Password" required <?= $lockout_remaining ? 'disabled' : '' ?>>
            <button type="submit" <?= $lockout_remaining ? 'disabled' : '' ?>>Login</button>
        </form>

        <p>Don't have an account? <a href="register.php">Register</a></p>
    </div>

    <!-- Modal -->
    <div id="resetModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('resetModal').style.display='none'">&times;</span>
            <h3>Forgot Password?</h3>
            <form action="reset_password.php" method="post">
                <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>">
                <input type="text" name="phone" placeholder="Enter your phone number" required>
                <button type="submit">Request Reset</button>
            </form>
        </div>
    </div>

    <?php if ($show_reset_popup): ?>
    <script>
        document.getElementById('resetModal').style.display = 'block';
    </script>
    <?php endif; ?>

    <?php if ($lockout_remaining): ?>
    <script>
        let countdown = <?= $lockout_remaining ?>;
        const timerEl = document.getElementById("timer");
        const interval = setInterval(() => {
            countdown--;
            if (countdown <= 0) {
                clearInterval(interval);
                location.reload(); // Reload page to re-enable login
            } else {
                timerEl.textContent = countdown;
            }
        }, 1000);
    </script>
    <?php endif; ?>

</body>
</html>