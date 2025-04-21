<?php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $phone = trim($_POST['phone']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND phone = ?");
    $stmt->execute([$username, $phone]);
    $user = $stmt->fetch();

    if ($user) {
        // Generate a new password or redirect to reset form
        $newPassword = bin2hex(random_bytes(4)); // e.g., 'a3b9d8f1'
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);

        $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update->execute([$hashed, $user['id']]);

        echo "<p style='text-align:center; font-family:sans-serif;'>✅ New password: <strong>$newPassword</strong><br><a href='index.php'>Go back to login</a></p>";
        exit();
    } else {
        echo "<p style='text-align:center; font-family:sans-serif; color:red;'>❌ Username and phone do not match. <a href='index.php'>Try again</a></p>";
        exit();
    }
}
?>