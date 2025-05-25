<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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
        echo "<script>alert('Credentials updated. Please login again.'); window.location.href='logout.php';</script>";
        exit();
    } else {
        echo "<script>alert('Failed to update credentials. Try again.');</script>";
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

// Temporarily unavailable books
$tempUnavailableStmt = $pdo->prepare("
    SELECT books.id, books.title, books.author, users.username
    FROM books
    JOIN borrowed_books ON books.id = borrowed_books.book_id
    JOIN users ON borrowed_books.user_id = users.id
    WHERE borrowed_books.user_id != ?
");
$tempUnavailableStmt->execute([$_SESSION['user_id']]);
$tempUnavailableBooks = $tempUnavailableStmt->fetchAll();

// Borrow/Return logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            text-align: center;
            padding: 20px;
        }

        h2, h3 {
            color: #333;
        }

        .tab {
            display: none;
            max-width: 800px;
            margin: 0 auto 20px;
            background: #fff;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .active {
            display: block;
        }

        .tab-header {
            display: inline-block;
            margin: 5px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .tab-header:hover {
            background: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #007bff;
            padding: 8px;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        .button {
            background-color: #28a745;
            color: white;
            padding: 6px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
        }

        .button:hover {
            background-color: #218838;
        }

        input[type="text"], input[type="password"], input[type="email"] {
            width: 100%;
            padding: 8px;
            margin: 5px 0 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        footer {
            margin-top: 20px;
            color: #666;
            font-size: 14px;
        }

        .logout {
            background-color: #dc3545;
            margin-bottom: 10px;
        }

        .logout:hover {
            background-color: #c82333;
        }

    </style>
    <script>
        function showTab(id) {
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            document.getElementById(id).classList.add('active');
        }
    </script>
</head>
<body>
    <h2>Welcome, <?= htmlspecialchars($user['username']) ?>!</h2>
    <p><strong>Login Time:</strong> <?= date("Y-m-d H:i:s") ?></p>

    <button class="tab-header" onclick="showTab('profile')">Profile</button>
    <button class="tab-header" onclick="showTab('borrowed')">Borrowed Books</button>
    <button class="tab-header" onclick="showTab('available')">Available Books</button>
    <button class="tab-header" onclick="showTab('unavailable')">Unavailable Books</button>
    <button class="tab-header" onclick="showTab('update')">Update Credentials</button>
    <a href="logout.php" class="button logout">Logout</a>

    <div id="profile" class="tab active">
        <h3>User Info</h3>
        <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
    </div>

    <div id="borrowed" class="tab">
        <h3>Borrowed Books</h3>
        <table>
            <tr><th>ID</th><th>Title</th><th>Author</th><th>Borrow Date</th><th>Action</th></tr>
            <?php foreach ($borrowedBooks as $book): ?>
            <tr>
                <td><?= $book['id'] ?></td>
                <td><?= htmlspecialchars($book['title']) ?></td>
                <td><?= htmlspecialchars($book['author']) ?></td>
                <td><?= $book['borrow_date'] ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="delete_borrowed_book_id" value="<?= $book['id'] ?>">
                        <button class="button" type="submit">Return</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div id="available" class="tab">
        <h3>Available Books</h3>
        <table>
            <tr><th>ID</th><th>Title</th><th>Author</th><th>Action</th></tr>
            <?php foreach ($availableBooks as $book): ?>
            <tr>
                <td><?= $book['id'] ?></td>
                <td><?= htmlspecialchars($book['title']) ?></td>
                <td><?= htmlspecialchars($book['author']) ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="borrow_book_id" value="<?= $book['id'] ?>">
                        <button class="button" type="submit">Borrow</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div id="unavailable" class="tab">
        <h3>Temporarily Unavailable Books</h3>
        <table>
            <tr><th>ID</th><th>Title</th><th>Author</th><th>Borrowed By</th></tr>
            <?php foreach ($tempUnavailableBooks as $book): ?>
            <tr>
                <td><?= $book['id'] ?></td>
                <td><?= htmlspecialchars($book['title']) ?></td>
                <td><?= htmlspecialchars($book['author']) ?></td>
                <td><?= htmlspecialchars($book['username']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div id="update" class="tab">
        <h3>Update Your Credentials</h3>
        <form method="post">
            <input type="hidden" name="update_credentials" value="1">
            <label>New Username:</label>
            <input type="text" name="new_username" value="<?= htmlspecialchars($user['username']) ?>" required>
            <label>New Password:</label>
            <input type="password" name="new_password" required>
            <label>Phone Number:</label>
            <input type="text" name="new_phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
            <label>Email:</label>
            <input type="email" name="new_email" value="<?= htmlspecialchars($user['email']) ?>">
            <button type="submit" class="button">Update</button>
        </form>
    </div>

    <footer>
        &copy;    Loren D., V.Althia T.
        | 2025 Borrowing Books Management System
    </footer>
</body>
</html>
