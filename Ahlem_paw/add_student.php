<?php
require __DIR__ . '/auth.php';
// add_student.php
// CRUD: create a new student record in the `students` table (id, fullname, matricule, group_id)

require __DIR__ . '/db_connect.php';

// Handle GET: show the form
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Add Student</title>
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
            .error { color: #dc2626; margin-bottom: 8px; }
            .success { color: #16a34a; margin-bottom: 8px; }
        </style>
    </head>
    <body>
        <nav>
            <ul>
                <li><a href="home.html">Home</a></li>
                <li><a href="attendance.html">Attendance List</a></li>
                <li><a href="add.html">Add Student (Local)</a></li>
                <li><a href="add_student.php">Add Student (DB)</a></li>
                <li><a href="list_students.php">List Students (DB)</a></li>
            </ul>
        </nav>
        <div class="container">
            <h1>Add Student (Database)</h1>
            <form action="add_student.php" method="post">
                <div class="form-group">
                    <label for="fullname">Full name</label>
                    <input type="text" id="fullname" name="fullname" required>
                </div>
                <div class="form-group">
                    <label for="matricule">Matricule</label>
                    <input type="text" id="matricule" name="matricule" required>
                </div>
                <div class="form-group">
                    <label for="group_id">Group ID</label>
                    <input type="text" id="group_id" name="group_id" required>
                </div>
                <button type="submit">Save</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Handle POST: insert into DB
$fullname  = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
$matricule = isset($_POST['matricule']) ? trim($_POST['matricule']) : '';
$groupId   = isset($_POST['group_id']) ? trim($_POST['group_id']) : '';

$errors = [];
if ($fullname === '') {
    $errors[] = 'Full name is required.';
}
if ($matricule === '') {
    $errors[] = 'Matricule is required.';
}
if ($groupId === '') {
    $errors[] = 'Group ID is required.';
}

if (!empty($errors)) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Add Student - Error</title>
    </head>
    <body>
        <h1>There were errors in your submission</h1>
        <ul>
            <?php foreach ($errors as $e): ?>
                <li><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></li>
            <?php endforeach; ?>
        </ul>
        <p><a href="javascript:history.back()">Go back</a></p>
    </body>
    </html>
    <?php
    exit;
}

try {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare(
        'INSERT INTO students (fullname, matricule, group_id) VALUES (:fullname, :matricule, :group_id)'
    );
    $stmt->execute([
        ':fullname'  => $fullname,
        ':matricule' => $matricule,
        ':group_id'  => $groupId,
    ]);
} catch (Throwable $e) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Add Student - Error</title>
    </head>
    <body>
        <h1>Database error</h1>
        <p>Could not save the student. Please check the logs or contact the administrator.</p>
        <p><a href="add_student.php">Back to form</a></p>
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
    <title>Add Student - Success</title>
</head>
<body>
    <h1>Student added successfully</h1>
    <p><strong>Full name:</strong> <?php echo htmlspecialchars($fullname, ENT_QUOTES, 'UTF-8'); ?></p>
    <p><strong>Matricule:</strong> <?php echo htmlspecialchars($matricule, ENT_QUOTES, 'UTF-8'); ?></p>
    <p><strong>Group ID:</strong> <?php echo htmlspecialchars($groupId, ENT_QUOTES, 'UTF-8'); ?></p>
    <p>
        <a href="add_student.php">Add another student</a> |
        <a href="list_students.php">View all students</a>
    </p>
</body>
</html>
