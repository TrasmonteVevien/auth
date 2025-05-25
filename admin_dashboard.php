<?php
include 'config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$adminStmt = $pdo->prepare("SELECT username FROM admins WHERE id = ?");
$adminStmt->execute([$_SESSION['admin_id']]);
$admin = $adminStmt->fetch();

if (isset($_POST['update_admin'])) {
    $newUsername = $_POST['username'];
    $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $updateStmt = $pdo->prepare("UPDATE admins SET username = ?, password = ? WHERE id = ?");
    if ($updateStmt->execute([$newUsername, $newPassword, $_SESSION['admin_id']])) {
        echo "<script>alert('Credentials updated. Please log in again.'); window.location.href='admin_logout.php';</script>";
        exit();
    }
}

if (isset($_POST['add_book'])) {
    $stmt = $pdo->prepare("INSERT INTO books (title, author) VALUES (?, ?)");
    $stmt->execute([$_POST['title'], $_POST['author']]);
}
if (isset($_POST['edit_book'])) {
    $stmt = $pdo->prepare("UPDATE books SET title = ?, author = ? WHERE id = ?");
    $stmt->execute([$_POST['title'], $_POST['author'], $_POST['book_id']]);
}
if (isset($_POST['delete_book'])) {
    $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
    $stmt->execute([$_POST['book_id']]);
}

if (isset($_POST['generate_report'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=site_report.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Username', 'Phone', 'Email', 'Borrowed Book']);
    $allUsers = $pdo->query("SELECT users.username, users.phone, users.email, books.title AS borrowed_title FROM users LEFT JOIN borrowed_books ON users.id = borrowed_books.user_id LEFT JOIN books ON borrowed_books.book_id = books.id")->fetchAll();
    foreach ($allUsers as $user) {
        fputcsv($output, [$user['username'], $user['phone'], $user['email'], $user['borrowed_title'] ?? 'None']);
    }
    fclose($output);
    exit();
}

$books = $pdo->query("SELECT * FROM books")->fetchAll();
$users = $pdo->query("SELECT users.id, users.username, users.phone, users.email, books.title AS borrowed_title FROM users LEFT JOIN borrowed_books ON users.id = borrowed_books.user_id LEFT JOIN books ON borrowed_books.book_id = books.id")->fetchAll();
$intruders = $pdo->query("SELECT * FROM failed_logins ORDER BY attempt_time DESC")->fetchAll();

$totalBooks = count($books);
$totalUsers = count($users);
$totalBorrowed = $pdo->query("SELECT COUNT(*) FROM borrowed_books")->fetchColumn();
$totalLogins = count($intruders);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            text-align: center;
            padding: 20px;
            margin: 0;
        }

        h2, h3 {
            color: #333;
        }

        .tab {
            display: none;
            max-width: 800px;
            margin: 0 auto 20px;
            background: #fffbea;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: left;
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
            font-weight: bold;
            user-select: none;
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

        .logout {
            background-color: #dc3545;
            margin-bottom: 20px;
            padding: 10px 15px;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }

        .logout:hover {
            background-color: #c82333;
        }

        .form-inline {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
            margin-top: 10px;
        }

        .form-inline input[type="text"] {
            flex-grow: 1;
            margin: 0;
        }

        .section-title {
            margin-bottom: 10px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
            font-size: 1.2em;
            color: #0056b3;
        }

    </style>
</head>
<body>

    <h2>Admin Dashboard</h2>
    <button class="logout" onclick="window.location.href='logout.php'">Logout</button>

    <div>
        <button class="tab-header active" onclick="openTab('profile')">🔐 Profile</button>
        <button class="tab-header" onclick="openTab('overview')">📊 Overview</button>
        <button class="tab-header" onclick="openTab('books')">📚 Books</button>
        <button class="tab-header" onclick="openTab('users')">👥 Users</button>
        <button class="tab-header" onclick="openTab('intruders')">🚨 Intruders</button>
    </div>

    <div id="profile" class="tab active">
        <h3 class="section-title">Admin Profile</h3>
        <form method="post" style="max-width: 400px;">
            <input type="hidden" name="update_admin" value="1">
            <label>Username</label>
            <input type="text" name="username" value="<?= htmlspecialchars($admin['username']) ?>" required>
            <label>New Password</label>
            <input type="password" name="password" required>
            <button class="button" type="submit">Update Profile</button>
        </form>
    </div>

    <div id="overview" class="tab">
        <h3 class="section-title">Site Overview</h3>
        <canvas id="siteChart" width="700" height="300"></canvas>
    </div>

    <div id="books" class="tab">
        <h3 class="section-title">Books Management</h3>
        <form method="post" class="form-inline" style="max-width: 700px;">
            <input type="text" name="title" placeholder="Book Title" required>
            <input type="text" name="author" placeholder="Author" required>
            <button name="add_book" class="button" type="submit">➕ Add Book</button>
        </form>

        <form method="post" style="max-width: 700px; margin-top: 10px;">
            <button type="submit" name="generate_report" class="button" style="background-color:#ffc107; color:black;">📄 Generate CSV Report</button>
        </form>

        <table>
            <thead>
                <tr><th>ID</th><th>Title</th><th>Author</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book): ?>
                    <tr>
                        <td><?= $book['id'] ?></td>
                        <td><?= htmlspecialchars($book['title']) ?></td>
                        <td><?= htmlspecialchars($book['author']) ?></td>
                        <td>
                            <form method="post" class="form-inline" style="margin-bottom: 5px;">
                                <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                <input type="text" name="title" value="<?= htmlspecialchars($book['title']) ?>" required>
                                <input type="text" name="author" value="<?= htmlspecialchars($book['author']) ?>" required>
                                <button type="submit" name="edit_book" class="button" style="background-color:#17a2b8;">✏️ Edit</button>
                            </form>
                            <form method="post" onsubmit="return confirm('Delete this book?');">
                                <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                <button type="submit" name="delete_book" class="button" style="background-color:#dc3545;">🗑️ Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div id="users" class="tab">
        <h3 class="section-title">Users List</h3>
        <table>
            <thead>
                <tr><th>ID</th><th>Username</th><th>Phone</th><th>Email</th><th>Borrowed Book</th></tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['phone']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['borrowed_title'] ?? 'None') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div id="intruders" class="tab">
        <h3 class="section-title">Failed Login Attempts (Intruders)</h3>
        <table>
            <thead>
                <tr><th>ID</th><th>Username</th><th>Phone</th><th>IP Address</th><th>Status</th><th>Attempt Time</th></tr>
            </thead>
            <tbody>
                <?php foreach ($intruders as $attempt): ?>
                    <tr>
                        <td><?= $attempt['id'] ?></td>
                        <td><?= htmlspecialchars($attempt['username']) ?></td>
                        <td><?= htmlspecialchars($attempt['phone']) ?></td>
                        <td><?= htmlspecialchars($attempt['ip_address']) ?></td>
                        <td><?= htmlspecialchars($attempt['status']) ?></td>
                        <td><?= htmlspecialchars($attempt['attempt_time']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<script>
    function openTab(tabName) {
        document.querySelectorAll('.tab').forEach(tab => {
            tab.classList.remove('active');
        });
        document.querySelectorAll('.tab-header').forEach(button => {
            button.classList.remove('active');
        });
        document.getElementById(tabName).classList.add('active');
        document.querySelector('.tab-header[onclick="openTab(\''+tabName+'\')"]').classList.add('active');
    }

    const ctx = document.getElementById('siteChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Books', 'Users', 'Borrowed Books', 'Failed Logins'],
            datasets: [{
                label: 'Site Statistics',
                data: [<?= $totalBooks ?>, <?= $totalUsers ?>, <?= $totalBorrowed ?>, <?= $totalLogins ?>],
                backgroundColor: [
                    '#007bff',
                    '#28a745',
                    '#ffc107',
                    '#dc3545'
                ],
                borderColor: [
                    '#0056b3',
                    '#218838',
                    '#d39e00',
                    '#bd2130'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>

</body>
</html>
