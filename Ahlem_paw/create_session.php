<?php
require __DIR__ . '/auth.php';
// create_session.php
// Creates a new attendance session in the `attendance_sessions` table
// and returns the new session ID.

require __DIR__ . '/db_connect.php';

header('Content-Type: text/html; charset=utf-8');

// If GET: show a small test form
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Create Attendance Session</title>
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
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, "Noto Sans", "Apple Color Emoji", "Segoe UI Emoji";
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
            nav ul li { float: none; }
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
                max-width: 800px;
                margin: 24px auto;
                padding: 24px;
                background: var(--surface);
                border-radius: 12px;
                box-shadow: 0 6px 24px rgba(15,23,42,0.06);
            }
            form .form-group { margin-bottom: 12px; }
            label {
                display: block;
                margin-bottom: 6px;
                color: var(--muted);
            }
            input[type="text"] {
                width: 100%;
                padding: 10px 12px;
                box-sizing: border-box;
                font-size: 16px;
                border: 1px solid var(--border);
                border-radius: 8px;
                background: #f8fafc;
            }
            input[type="text"]:focus {
                outline: none;
                border-color: var(--primary);
                box-shadow: 0 0 0 3px rgba(37,99,235,0.15);
            }
            button {
                background-color: var(--primary);
                color: #fff;
                border: none;
                padding: 10px 16px;
                cursor: pointer;
                font-size: 16px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(37,99,235,0.25);
                transition: background-color .2s ease, transform .08s ease;
            }
            button:hover { background-color: var(--primary-hover); }
            button:active { transform: translateY(1px); }
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
            <h1>Create Attendance Session</h1>
            <form method="post" action="create_session.php">
                <div class="form-group">
                    <label for="course_id">Course ID</label>
                    <input type="text" id="course_id" name="course_id" required>
                </div>
                <div class="form-group">
                    <label for="group_id">Group ID</label>
                    <input type="text" id="group_id" name="group_id" required>
                </div>
                <div class="form-group">
                    <label for="opened_by">Professor ID</label>
                    <input type="text" id="opened_by" name="opened_by" required>
                </div>
                <button type="submit">Create session</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// POST: create the session
$courseId   = isset($_POST['course_id']) ? trim($_POST['course_id']) : '';
$groupId    = isset($_POST['group_id']) ? trim($_POST['group_id']) : '';
$openedBy   = isset($_POST['opened_by']) ? trim($_POST['opened_by']) : '';
$sessionDate = date('Y-m-d'); // today

$errors = [];
if ($courseId === '') {
    $errors[] = 'Course ID is required.';
}
if ($groupId === '') {
    $errors[] = 'Group ID is required.';
}
if ($openedBy === '') {
    $errors[] = 'Professor ID (opened_by) is required.';
}

if (!empty($errors)) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Create Attendance Session - Error</title>
        <style>
            :root {
                --bg: #f8fafc;
                --surface: #ffffff;
                --text: #0f172a;
                --muted: #64748b;
                --border: #e2e8f0;
                --primary: #2563eb;
                --primary-hover: #1d4ed8;
                --danger: #ef4444;
            }
            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, "Noto Sans", "Apple Color Emoji", "Segoe UI Emoji";
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
            nav ul li { float: none; }
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
                max-width: 800px;
                margin: 24px auto;
                padding: 24px;
                background: var(--surface);
                border-radius: 12px;
                box-shadow: 0 6px 24px rgba(15,23,42,0.06);
            }
            .error { color: var(--danger); }
            ul { list-style: none; padding: 0; }
            ul li { margin: 4px 0; }
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
            <h1>Errors</h1>
            <ul class="error">
                <?php foreach ($errors as $e): ?>
                    <li><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
            <p><a href="javascript:history.back()">Back</a></p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

try {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare(
        'INSERT INTO attendance_sessions (course_id, group_id, date, opened_by, status)
         VALUES (:course_id, :group_id, :date, :opened_by, :status)'
    );
    $stmt->execute([
        ':course_id' => $courseId,
        ':group_id'  => $groupId,
        ':date'      => $sessionDate,
        ':opened_by' => $openedBy,
        ':status'    => 'open',
    ]);

    $sessionId = $pdo->lastInsertId();
} catch (Throwable $e) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Create Attendance Session - Error</title>
        <style>
            :root {
                --bg: #f8fafc;
                --surface: #ffffff;
                --text: #0f172a;
                --muted: #64748b;
                --border: #e2e8f0;
                --primary: #2563eb;
                --primary-hover: #1d4ed8;
                --danger: #ef4444;
            }
            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, "Noto Sans", "Apple Color Emoji", "Segoe UI Emoji";
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
            nav ul li { float: none; }
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
                max-width: 800px;
                margin: 24px auto;
                padding: 24px;
                background: var(--surface);
                border-radius: 12px;
                box-shadow: 0 6px 24px rgba(15,23,42,0.06);
            }
            .error { color: var(--danger); }
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
            <h1>Database error</h1>
            <p class="error">Could not create session. Please check the database or logs.</p>
            <p><a href="create_session.php">Try again</a></p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Attendance Session - Success</title>
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
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, "Noto Sans", "Apple Color Emoji", "Segoe UI Emoji";
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
        nav ul li { float: none; }
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
            max-width: 800px;
            margin: 24px auto;
            padding: 24px;
            background: var(--surface);
            border-radius: 12px;
            box-shadow: 0 6px 24px rgba(15,23,42,0.06);
        }
        .success { color: var(--success); }
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
        <h1>Session created successfully</h1>
        <p class="success">New session ID: <strong><?php echo htmlspecialchars($sessionId, ENT_QUOTES, 'UTF-8'); ?></strong></p>
        <p>
            <a href="create_session.php">Create another session</a>
        </p>
    </div>
</body>
</html>


