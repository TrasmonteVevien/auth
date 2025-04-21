<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch user info
$userStmt = $pdo->prepare("SELECT username, phone, email FROM users WHERE id = ?");
$userStmt->execute([$_SESSION['user_id']]);
$user = $userStmt->fetch();

// Handle credential update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_credentials'])) {
    $newUsername = $_POST['new_username'];
    $newPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $newPhone = $_POST['new_phone'];
    $newEmail = $_POST['new_email'];

    $updateStmt = $pdo->prepare("UPDATE users SET username = ?, password = ?, phone = ?, email = ? WHERE id = ?");
    if ($updateStmt->execute([$newUsername, $newPassword, $newPhone, $newEmail, $_SESSION['user_id']])) {
        echo "<script>alert('Credentials updated, login again.'); window.location.href='logout.php';</script>";
        exit();
    } else {
        echo "<script>alert('Failed to update credentials. Please try again.');</script>";
    }
}

// Fetch borrowed books
$stmt = $pdo->prepare("
    SELECT books.id, books.title, books.author, borrowed_books.borrow_date
    FROM books
    JOIN borrowed_books ON books.id = borrowed_books.book_id
    WHERE borrowed_books.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$borrowedBooks = $stmt->fetchAll();

// Available books
$availableBooksStmt = $pdo->prepare("
    SELECT * FROM books
    WHERE id NOT IN (SELECT book_id FROM borrowed_books)
");
$availableBooksStmt->execute();
$availableBooks = $availableBooksStmt->fetchAll();

// Temporarily unavailable
$tempUnavailableStmt = $pdo->prepare("
    SELECT books.id, books.title, books.author, users.username
    FROM books
    JOIN borrowed_books ON books.id = borrowed_books.book_id
    JOIN users ON borrowed_books.user_id = users.id
    WHERE borrowed_books.user_id != ?
");
$tempUnavailableStmt->execute([$_SESSION['user_id']]);
$tempUnavailableBooks = $tempUnavailableStmt->fetchAll();

// Borrow/return logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['borrow_book_id'])) {
        $bookId = $_POST['borrow_book_id'];
        $insertStmt = $pdo->prepare("INSERT INTO borrowed_books (user_id, book_id, borrow_date) VALUES (?, ?, NOW())");
        if ($insertStmt->execute([$_SESSION['user_id'], $bookId])) {
            header("Refresh:0");
            exit();
        }
    } elseif (isset($_POST['delete_borrowed_book_id'])) {
        $deleteBookId = $_POST['delete_borrowed_book_id'];
        $deleteStmt = $pdo->prepare("DELETE FROM borrowed_books WHERE user_id = ? AND book_id = ?");
        if ($deleteStmt->execute([$_SESSION['user_id'], $deleteBookId])) {
            header("Refresh:0");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <style>
        body {
            background-color: #f9f9f9;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0;
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        h2, h3, h4 {
            color: #333;
            margin-bottom: 10px;
        }

        .tab {
            display: none;
            width: 90%;
            max-width: 800px;
            margin-bottom: 20px;
            background-color: #fff;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .tab-header {
            cursor: pointer;
            padding: 10px;
            background-color: #007bff;
            color: white;
            width: 90%;
            max-width: 800px;
            border: none;
            text-align: center;
            font-size: 16px;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .active {
            display: block;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #007bff;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .button-container {
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            width: 100%;
            max-width: 800px;
        }

        .button {
            padding: 8px 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .button:hover {
            background-color: #218838;
        }

        .back-button {
            background-color: #007bff;
        }

        .back-button:hover {
            background-color: #0056b3;
        }

        input[type="text"], input[type="password"], input[type="email"] {
            width: 100%;
            padding: 8px;
            margin-top: 4px;
            margin-bottom: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        footer {
            margin-top: 20px;
            font-size: 14px;
            color: #666666;
        }
    </style>
    <script>
        function showTab(tabId) {
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => tab.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
        }
    </script>
</head>
<body>
    <h2>Dashboard - <?= htmlspecialchars($user['username']) ?>'s Profile</h2>

    <div class="button-container">
        <a href="logout.php" class="button">Logout</a>
    </div>

    <button class="tab-header" onclick="showTab('userProfileTab')">User Profile</button>
    <div id="userProfileTab" class="tab active">
        <h3>User Profile</h3>
        <p><strong>Login Time:</strong> <?= date("Y-m-d H:i:s") ?></p>

        <h4>Borrowed Books</h4>
        <table>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Borrow Date</th>
                <th>Action</th>
            </tr>
            <?php foreach ($borrowedBooks as $book): ?>
                <tr>
                    <td><?= htmlspecialchars($book['id']) ?></td>
                    <td><?= htmlspecialchars($book['title']) ?></td>
                    <td><?= htmlspecialchars($book['author']) ?></td>
                    <td><?= htmlspecialchars($book['borrow_date']) ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="delete_borrowed_book_id" value="<?= $book['id'] ?>">
                            <button type="submit" class="button">Return</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h4>Temporarily Unavailable Books</h4>
        <table>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Borrowed By</th>
            </tr>
            <?php foreach ($tempUnavailableBooks as $book): ?>
                <tr>
                    <td><?= htmlspecialchars($book['id']) ?></td>
                    <td><?= htmlspecialchars($book['title']) ?></td>
                    <td><?= htmlspecialchars($book['author']) ?></td>
                    <td><?= htmlspecialchars($book['username']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h4>Update Your Credentials</h4>
        <form method="post">
            <input type="hidden" name="update_credentials" value="1">

            <label>New Username:</label>
            <input type="text" name="new_username" value="<?= htmlspecialchars($user['username']) ?>" required>

            <label>New Password:</label>
            <input type="password" name="new_password" required>

            <label>Phone Number:</label>
            <input type="text" name="new_phone" value="<?= htmlspecialchars($user['phone']) ?>" required>

            <label>Email (optional):</label>
            <input type="email" name="new_email" value="<?= htmlspecialchars($user['email']) ?>">

            <button type="submit" class="button">Update Credentials</button>
        </form>
    </div>

    <button class="tab-header" onclick="showTab('availableBooksTab')">Available Books</button>
    <div id="availableBooksTab" class="tab">
        <h3>Available Books</h3>
        <div class="button-container">
            <button class="button back-button" onclick="showTab('userProfileTab')">Back to Profile</button>
        </div>
        <table>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Action</th>
            </tr>
            <?php foreach ($availableBooks as $book): ?>
                <tr>
                    <td><?= htmlspecialchars($book['id']) ?></td>
                    <td><?= htmlspecialchars($book['title']) ?></td>
                    <td><?= htmlspecialchars($book['author']) ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="borrow_book_id" value="<?= $book['id'] ?>">
                            <button type="submit" class="button">Borrow</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <footer>
        &copy; Developer, Vevien Althia A.Trasmonte | 2025 Borrowing Books Management System
    </footer>
</body>
</html>
