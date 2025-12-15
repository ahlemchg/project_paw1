<?php
require __DIR__ . '/auth.php';
// list_students.php
// List all students from the `students` table.

require __DIR__ . '/db_connect.php';

try {
    $pdo = get_db_connection();
    $stmt = $pdo->query('SELECT id, fullname, matricule, group_id FROM students ORDER BY id DESC');
    $students = $stmt->fetchAll();
} catch (Throwable $e) {
    $students = [];
    $dbError = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Students List</title>
    <style>
        :root {
            --bg: #f8fafc;
            --surface: #ffffff;
            --text: #0f172a;
            --muted: #64748b;
            --border: #e2e8f0;
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, "Noto Sans";
            margin: 0;
            color: var(--text);
            background: var(--bg);
        }
        nav {
            background-color: var(--surface);
            border-bottom: 1px solid var(--border);
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            position: sticky;
            top: 0;
            z-index: 10;
        }
        nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0 12px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        nav ul li a {
            display: block;
            color: var(--text);
            text-align: left;
            padding: 12px 16px;
            text-decoration: none;
            border-radius: 8px;
        }
        nav ul li a:hover {
            background-color: #f1f5f9;
            color: var(--primary);
        }
        @media (min-width: 600px) {
            nav ul { flex-direction: row; gap: 8px; }
            nav ul li a { text-align: center; padding: 14px 16px; }
        }
        .container {
            max-width: 900px;
            margin: 24px auto;
            padding: 24px;
            background: var(--surface);
            border-radius: 12px;
            box-shadow: 0 6px 24px rgba(15,23,42,0.06);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }
        th, td {
            border: 1px solid var(--border);
            padding: 8px 10px;
            text-align: left;
        }
        th {
            background-color: #f1f5f9;
            font-weight: 600;
        }
        a.action {
            color: var(--primary);
            text-decoration: none;
            margin-right: 8px;
        }
        a.action:hover { text-decoration: underline; }
        .error { color:#dc2626; }
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
    <h1>Students (Database)</h1>

    <?php if (!empty($dbError)): ?>
        <p class="error">Could not load students. Please check the database connection.</p>
    <?php elseif (empty($students)): ?>
        <p>No students found.</p>
    <?php else: ?>
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Full name</th>
                <th>Matricule</th>
                <th>Group ID</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($students as $s): ?>
                <tr>
                    <td><?php echo htmlspecialchars($s['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($s['fullname'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($s['matricule'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($s['group_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                        <a class="action" href="update_student.php?id=<?php echo urlencode($s['id']); ?>">Edit</a>
                        <a class="action" href="delete_student.php?id=<?php echo urlencode($s['id']); ?>"
                           onclick="return confirm('Delete this student?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>


