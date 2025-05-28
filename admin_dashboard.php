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
    if (isset($_POST['verify_intruder'])) {
    $stmt = $pdo->prepare("UPDATE failed_logins SET status = 'Verified' WHERE id = ?");
    $stmt->execute([$_POST['intruder_id']]);
}

if (isset($_POST['delete_intruder'])) {
    $stmt = $pdo->prepare("DELETE FROM failed_logins WHERE id = ?");
    $stmt->execute([$_POST['intruder_id']]);
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
$users = $pdo->query("SELECT users.id, users.username, users.phone, users.email, books.title AS borrowed_title, borrowed_books.due_date FROM users LEFT JOIN borrowed_books ON users.id = borrowed_books.user_id LEFT JOIN books ON borrowed_books.book_id = books.id")->fetchAll();
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
}

.tab {
    display: none;
    max-width: 1000px;
    margin: 0 auto 20px;
    background: #ffffff;
    padding: 30px;
    border: 2px solid #f1c40f; /* yellow border */
    border-radius: 4px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

.active {
    display: block;
}

.tab-header {
    display: inline-block;
    margin: 5px;
    padding: 12px 24px;
    background: #8e44ad; /* purple */
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

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background: white;
    border: 2px solid #f1c40f; /* yellow border */
}

th, td {
    border: 1px solid #f1c40f;
    padding: 12px;
    text-align: left;
}

th {
    background-color: #8e44ad; /* purple */
    color: white;
    font-weight: 500;
}

tr:nth-child(even) {
    background-color: #f9f9f9;
}

.button {
    background-color: #8e44ad; /* purple */
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.2s;
}

.button:hover {
    background-color: #7d3c98;
}

input[type="text"], input[type="password"], input[type="email"] {
    width: 100%;
    padding: 12px;
    margin: 8px 0;
    border: 1px solid #f1c40f;
    border-radius: 4px;
    box-sizing: border-box;
    font-size: 14px;
    transition: border-color 0.2s, box-shadow 0.2s;
    background-color: #fafafa;
}

input[type="text"]:focus, input[type="password"]:focus, input[type="email"]:focus {
    border-color: #8e44ad;
    outline: none;
    box-shadow: 0 0 0 2px rgba(142, 68, 173, 0.1);
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
    position: absolute;
    top: 30px;
    right: 30px;
    text-decoration: none;
}

.logout:hover {
    background-color: #c0392b;
}

.form-inline {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items: center;
    margin-top: 15px;
}

.form-inline input[type="text"] {
    flex-grow: 1;
    margin: 0;
}

.section-title {
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f1c40f;
    color: #2c3e50;
    font-size: 20px;
    font-weight: 500;
    text-align: center;
}

#profile form {
    max-width: 400px;
    margin: 0 auto;
}

.chart-container {
    background: white;
    padding: 20px;
    border-radius: 4px;
    border: 2px solid #f1c40f;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    margin-top: 20px;
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

    </style>
</head>
<body>

    <h2>Admin Dashboard</h2>
    <a href="logout.php" class="logout">Logout</a>

    <div class="tab-container">
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
        <tr><th>ID</th><th>Username</th><th>Phone</th><th>Email</th><th>Borrowed Book</th><th>Status</th></tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['phone']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['borrowed_title'] ?? 'None') ?></td>
                <td>
                    <?php
                    if (!empty($user['due_date'])) {
                        $dueDate = strtotime($user['due_date']);
                        $today = strtotime(date('Y-m-d'));
                        if ($dueDate < $today) {
                            echo "<span style='color: red; font-weight: bold;'>Overdue</span>";
                        } else {
                            echo "On Time";
                        }
                    } else {
                        echo "N/A";
                    }
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
    </div>

    <div id="intruders" class="tab">
        <h3 class="section-title">Failed Login Attempts (Intruders)</h3>
        <table>
            <thead>
<tr><th>ID</th><th>Username</th><th>Phone</th><th>IP Address</th><th>Status</th><th>Attempt Time</th><th>Actions</th></tr>
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
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="intruder_id" value="<?= $attempt['id'] ?>">
                    <button name="verify_intruder" class="button" style="background-color:#3498db;">✅ Verify</button>
                </form>
                <form method="post" style="display:inline;" onsubmit="return confirm('Delete this attempt?');">
                    <input type="hidden" name="intruder_id" value="<?= $attempt['id'] ?>">
                    <button name="delete_intruder" class="button" style="background-color:#e74c3c;">🗑️ Delete</button>
                </form>
            </td>
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