<?php
session_start();
if (empty($_SESSION['authenticated'])) {
    header('Location: login.php');
    exit;
}
$username = isset($_SESSION['username']) ? (string)$_SESSION['username'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        :root {
            --bg: #f8fafc;
            --surface: #ffffff;
            --text: #0f172a;
            --muted: #64748b;
            --border: #e2e8f0;
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --success: #10b981;
        }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, "Noto Sans", "Apple Color Emoji", "Segoe UI Emoji"; margin: 0; color: var(--text); background: var(--bg); }
        nav { background-color: var(--surface); border-bottom: 1px solid var(--border); box-shadow: 0 2px 8px rgba(0,0,0,0.04); position: sticky; top: 0; z-index: 10; }
        nav ul { list-style-type: none; margin: 0; padding: 0 12px; display: flex; flex-direction: column; gap: 4px; }
        nav ul li a { display: block; color: var(--text); text-align: left; padding: 12px 16px; text-decoration: none; border-radius: 8px; }
        nav ul li a:hover { background-color: #f1f5f9; color: var(--primary); }
        @media (min-width: 600px) { nav ul { flex-direction: row; gap: 8px; } nav ul li a { text-align: center; padding: 14px 16px; } }
        .container { max-width: 800px; margin: 24px auto; padding: 24px; background: var(--surface); border-radius: 12px; box-shadow: 0 6px 24px rgba(15,23,42,0.06); }
        .welcome { color: var(--success); font-weight: 600; }
        a { color: var(--primary); text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <nav>
        <ul>
            <li><a href="home.html">Home</a></li>
            <li><a href="attendance.html">Attendance List</a></li>
            <li><a href="add.html">Add Student</a></li>
            <li><a href="reports.html">Reports</a></li>
            <li><a href="#">Logout</a></li>
        </ul>
    </nav>
    <div class="container">
        <h1>Dashboard</h1>
        <p class="welcome">Welcome, <?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?></p>
        <p><a href="list_students.php">Manage students</a></p>
    </div>
</body>
</html>
