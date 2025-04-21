<?php
include 'config.php';
session_start();

$verification_code = $_SESSION['verification_code'] ?? '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['verification_code'])) {
        $input_code = trim($_POST['verification_code']);

        // Check if the input verification code matches the one stored in session
        if ($input_code == $verification_code) {
            // Mark the phone as verified
            $stmt = $pdo->prepare("UPDATE users SET phone_verified = 1 WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);

            // Redirect to dashboard after successful verification
            header("Location: dashboard.php");
            exit();
        } else {
            $error_message = "⚠️ Invalid verification code. Please try again.";
        }
    }
}

// Generate a random verification code and send it to the user's phone via SMS API
$_SESSION['verification_code'] = rand(100000, 999999);
$phone = $_SESSION['phone']; // From session

// Simulate sending the SMS
// In a real-world scenario, integrate with an SMS API here
echo "Verification code sent to your phone: $phone. Code: " . $_SESSION['verification_code'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Phone Verification</title>
</head>
<body>
    <div>
        <h2>Enter the verification code sent to your phone</h2>

        <?php if ($error_message): ?>
            <div><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="text" name="verification_code" placeholder="Enter verification code" required>
            <button type="submit">Verify</button>
        </form>
    </div>
</body>
</html>
