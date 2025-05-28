<?php
include 'config.php';
session_start();

// Initialize session variables
if (!isset($_SESSION['failed_attempts'])) $_SESSION['failed_attempts'] = 0;
$username = $_SESSION['last_username'] ?? '';
$error_message = '';
$show_reset_popup = false;
$lockout_remaining = 0;

// Handle lockout timing
if (isset($_SESSION['lockout_time'])) {
    $time_diff = time() - $_SESSION['lockout_time'];
    if ($time_diff < 10) {
        $lockout_remaining = 10 - $time_diff;
        $error_message = "⏳ Please wait <span id='timer'>$lockout_remaining</span> seconds.";
    } else {
        unset($_SESSION['lockout_time']);
        $_SESSION['failed_attempts'] = 0;
    }
}

// Handle login request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $lockout_remaining === 0) {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = trim($_POST['username']);
        $_SESSION['last_username'] = $username;
        $password = trim($_POST['password']);
        $ip = $_SERVER['REMOTE_ADDR'];

        // Fetch user
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        $phone = $user['phone'] ?? 'Unknown';

        // 🚫 Check if account is blocked
        if ($user && $user['blocked']) {
            $error_message = '🚫 Your account has been blocked. Contact admin.';
        } else if ($user && password_verify($password, $user['password'])) {
            // ✅ SUCCESS
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['failed_attempts'] = 0;
            unset($_SESSION['last_username'], $_SESSION['lockout_time']);

            $pdo->prepare("INSERT INTO failed_logins (username, phone, ip_address, status) 
                           VALUES (?, ?, ?, 'Success')")
                ->execute([$username, $phone, $ip]);

            header("Location: dashboard.php");
            exit();
        } else {
            // ❌ FAILED LOGIN
            $_SESSION['failed_attempts']++;

            $pdo->prepare("INSERT INTO failed_logins (username, phone, ip_address, status) 
                           VALUES (?, ?, ?, 'Failed')")
                ->execute([$username, $phone, $ip]);

            if ($_SESSION['failed_attempts'] >= 2) {
                $_SESSION['lockout_time'] = time();
                $lockout_remaining = 10;
                $error_message = "⏳ Please wait <span id='timer'>$lockout_remaining</span> seconds.";
            } else {
                $error_message = "⚠️ Invalid credentials. Attempt {$_SESSION['failed_attempts']} of 2.";
                $show_reset_popup = true;
            }
        }
    }
}
?>


<!-- HTML BELOW (unchanged except PHP logic uses updated variables) -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
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
    .login-container {
        background-color: #ffffff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        width: 400px;
        text-align: center;
        border: 2px solid #f1c40f; /* Yellow border */
    }

    h2 {
        margin-bottom: 20px;
        color: #6a0dad; /* Purple */
    }

    input[type="text"], input[type="password"] {
        width: calc(100% - 20px);
        padding: 10px;
        margin: 10px 0;
        border: 2px solid #f1c40f; /* Yellow */
        border-radius: 6px;
        font-size: 14px;
    }

    button {
        width: 100%;
        padding: 12px;
        background-color: #6a0dad; /* Purple */
        color: white;
        border: 2px solid #f1c40f; /* Yellow border */
        border-radius: 6px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #4b0082; /* Darker purple */
    }

    .message.warning {
        color: red;
        margin: 10px 0;
    }

    p a {
        color: #6a0dad;
        text-decoration: none;
    }

    p a:hover {
        text-decoration: underline;
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
        background-color: #ffffff;
        margin: 10% auto;
        padding: 20px;
        border-radius: 10px;
        width: 80%;
        max-width: 400px;
        box-shadow: 0 0 15px rgba(0,0,0,0.3);
        border: 2px solid #f1c40f;
    }

    .modal-content h3 {
        color: #6a0dad;
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