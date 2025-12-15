<?php
require __DIR__ . '/auth.php';
// close_session.php
// Closes an existing attendance session by setting status = "closed".

require __DIR__ . '/db_connect.php';

header('Content-Type: text/html; charset=utf-8');

// Accept id via GET or POST for easy manual testing
$sessionId = 0;
if (isset($_POST['session_id'])) {
    $sessionId = (int)$_POST['session_id'];
} elseif (isset($_GET['session_id'])) {
    $sessionId = (int)$_GET['session_id'];
}

// If no ID and GET: show a small form to test
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $sessionId <= 0) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Close Attendance Session</title>
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
            input[type="number"] {
                width: 100%;
                padding: 10px 12px;
                box-sizing: border-box;
                font-size: 16px;
                border: 1px solid var(--border);
                border-radius: 8px;
                background: #f8fafc;
            }
            input[type="number"]:focus {
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
            <h1>Close Attendance Session</h1>
            <form method="post" action="close_session.php">
                <div class="form-group">
                    <label for="session_id">Session ID</label>
                    <input type="number" id="session_id" name="session_id" required>
                </div>
                <button type="submit">Close session</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

if ($sessionId <= 0) {
    http_response_code(400);
    echo 'Invalid session ID.';
    exit;
}

try {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare('UPDATE attendance_sessions SET status = :status WHERE id = :id');
    $stmt->execute([
        ':status' => 'closed',
        ':id'     => $sessionId,
    ]);
} catch (Throwable $e) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Close Attendance Session - Error</title>
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
            <p class="error">Could not close the session. Please check the database or logs.</p>
            <p><a href="close_session.php">Try again</a></p>
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
    <title>Close Attendance Session - Success</title>
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
        <h1>Session closed successfully</h1>
        <p class="success">Session ID: <strong><?php echo htmlspecialchars($sessionId, ENT_QUOTES, 'UTF-8'); ?></strong></p>
        <p><a href="close_session.php">Close another session</a></p>
    </div>
</body>
</html>


