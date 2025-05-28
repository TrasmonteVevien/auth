<?php
session_start();
session_destroy(); // Clear the session
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Logged Out</title>
  <style>
    :root {
      --primary: #8e44ad;
      --primary-dark: #7d3c98;
      --text: #ffffff; /* updated to white for better contrast */
      --text-light: #d6eaf8;
      --background: #004080; /* deep blue background */
      --white: #ffffff;
      --border: #f1c40f; /* yellow border */
    }

    body {
      background-color: var(--background);
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      margin: 0;
      padding: 20px;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: var(--text);
    }

    .container {
      background-color: var(--white);
      border: 2px solid var(--border);
      border-radius: 4px;
      max-width: 500px;
      width: 100%;
      padding: 40px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      text-align: center;
      color: #2c3e50; /* dark text for white container */
    }

    h1 {
      color: var(--primary);
      margin-bottom: 30px;
      font-weight: 500;
      font-size: 28px;
      letter-spacing: -0.5px;
    }

    .message {
      font-size: 16px;
      margin-bottom: 30px;
      line-height: 1.6;
    }

    .button-container {
      display: flex;
      gap: 15px;
      justify-content: center;
      margin-top: 30px;
    }

    .button {
      padding: 12px 24px;
      background-color: var(--primary);
      color: var(--white);
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
      font-weight: 500;
      text-align: center;
      transition: all 0.3s ease;
      text-decoration: none;
      min-width: 120px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .button:hover {
      background-color: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(142, 68, 173, 0.2);
    }

    .button.secondary {
      background-color: var(--text-light);
      color: #004080;
    }

    .button.secondary:hover {
      background-color: #aed6f1;
      box-shadow: 0 4px 12px rgba(127, 140, 141, 0.2);
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Successfully Logged Out</h1>
    <p class="message">Thank you for using our system. You have been safely logged out of your account.</p>
    <div class="button-container">
      <a href="login.php" class="button">Login Again</a>
      <a href="index.php" class="button secondary">Back to Home</a>
    </div>
  </div>
</body>
</html>
