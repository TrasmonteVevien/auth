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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #d0e7ff; /* lighter blue */
            color: #2c3e50;
            padding: 30px;
            margin: 0;
            line-height: 1.6;
        }

        h2, h3 {
            color: #2c3e50;
            font-weight: 500;
            letter-spacing: -0.5px;
            text-align: center;
            margin-bottom: 20px;
        }

        .tab {
            display: none;
            max-width: 1000px;
            margin: 0 auto 20px;
            background: #ffffff;
            padding: 30px;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border: 2px solid #f1c40f; /* yellow border */
        }

        .active {
            display: block;
        }

        .tab-header {
            display: inline-block;
            margin: 5px;
            padding: 12px 24px;
            background: #8e44ad; /* purple button */
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s ease;
            min-width: 180px;
            text-align: center;
        }

        .tab-header:hover {
            background: #7d3c98;
            transform: translateY(-2px);
        }

        .tab-header.active {
            background: #7d3c98;
            box-shadow: 0 2px 8px rgba(142, 68, 173, 0.2);
        }

        .tab-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            max-width: 1000px;
            margin: 0 auto 30px;
            padding: 20px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 4px;
            overflow: hidden;
            border: 2px solid #f1c40f; /* yellow border */
        }

        th, td {
            border: 1px solid #f1c40f; /* yellow border */
            padding: 12px 16px;
            text-align: left;
            font-size: 14px;
        }

        th {
            background-color: #8e44ad;
            color: white;
            font-weight: 500;
            border: none;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tr:hover {
            background-color: #f5f6f7;
        }

        .button {
            background-color: #8e44ad; /* purple */
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.2s;
        }

        .button:hover {
            background-color: #7d3c98;
        }

        input[type="text"], input[type="password"], input[type="email"] {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 2px solid #f1c40f; /* yellow border */
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
            transition: border-color 0.2s, box-shadow 0.2s;
            background-color: #fafafa;
        }

        input[type="text"]:focus, input[type="password"]:focus, input[type="email"]:focus {
            border-color: #f1c40f; /* yellow focus */
            outline: none;
            box-shadow: 0 0 5px 2px rgba(241, 196, 15, 0.3);
            background-color: #ffffff;
        }

        .logout {
            background-color: #e74c3c;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            font-size: 13px;
            transition: background-color 0.2s;
            display: inline-block;
            margin: 0;
            text-decoration: none;
            position: absolute;
            top: 30px;
            right: 30px;
        }

        .logout:hover {
            background-color: #c0392b;
        }

        .section-title {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f1c40f; /* yellow border */
            color: #2c3e50;
            font-size: 20px;
            font-weight: 500;
            text-align: center;
        }

        #profile form {
            max-width: 400px;
            margin: 0 auto;
            background: #fafafa;
            padding: 20px;
            border-radius: 4px;
            border: 2px solid #f1c40f; /* yellow border */
        }

        footer {
            text-align: center;
            margin-top: 30px;
            color: #7f8c8d;
            font-size: 14px;
            padding: 20px;
        }
    </style>
    <script>
        function showTab(id) {
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            document.getElementById(id).classList.add('active');
            document.querySelectorAll('.tab-header').forEach(button => {
                button.classList.remove('active');
            });
            document.querySelector(`[onclick="showTab('${id}')"]`).classList.add('active');
        }
    </script>
</head>
<body>
    <h2>Welcome, <?= htmlspecialchars($user['username']) ?>!</h2>
    <a href="logout.php" class="logout">Logout</a>

    <div class="tab-container">
        <button class="tab-header active" onclick="showTab('profile')">🔐 Profile</button>
        <button class="tab-header" onclick="showTab('borrowed')">📚 Borrowed Books</button>
        <button class="tab-header" onclick="showTab('available')">📖 Available Books</button>
        <button class="tab-header" onclick="showTab('unavailable')">⌛ Unavailable Books</button>
        <button class="tab-header" onclick="showTab('update')">⚙️ Update Profile</button>
    </div>

    <div id="profile" class="tab active">
        <h3 class="section-title">User Profile</h3>
        <div style="max-width: 400px; margin: 0 auto; background: #fafafa; padding: 20px; border-radius: 4px; border: 1px solid #e1e1e1;">
            <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>Login Time:</strong> <?= date("Y-m-d H:i:s") ?></p>
        </div>
    </div>

    <div id="borrowed" class="tab">
        <h3 class="section-title">Borrowed Books</h3>
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
                        <button class="button" type="submit" style="background-color: #e74c3c;">Return</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div id="available" class="tab">
        <h3 class="section-title">Available Books</h3>
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
        <h3 class="section-title">Temporarily Unavailable Books</h3>
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
        <h3 class="section-title">Update Your Profile</h3>
        <form method="post" id="profile">
            <input type="hidden" name="update_credentials" value="1">
            <label>New Username:</label>
            <input type="text" name="new_username" value="<?= htmlspecialchars($user['username']) ?>" required>
            <label>New Password:</label>
            <input type="password" name="new_password" required>
            <label>Phone Number:</label>
            <input type="text" name="new_phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
            <label>Email:</label>
            <input type="email" name="new_email" value="<?= htmlspecialchars($user['email']) ?>">
            <button type="submit" class="button" style="width: 100%; margin-top: 15px;">Update Profile</button>
        </form>
    </div>

    <footer>
        &copy; 2025 Borrowing Books Management System | Loren D., V.Althia T.
    </footer>
</body>
</html>