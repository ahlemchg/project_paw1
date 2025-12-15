<?php
require __DIR__ . '/auth.php';
// update_student.php
// Edit an existing student in the `students` table.

require __DIR__ . '/db_connect.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(400);
    echo 'Invalid student ID.';
    exit;
}

try {
    $pdo = get_db_connection();
} catch (Throwable $e) {
    http_response_code(500);
    echo 'Database connection error.';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname  = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
    $matricule = isset($_POST['matricule']) ? trim($_POST['matricule']) : '';
    $groupId   = isset($_POST['group_id']) ? trim($_POST['group_id']) : '';

    $errors = [];
    if ($fullname === '') $errors[] = 'Full name is required.';
    if ($matricule === '') $errors[] = 'Matricule is required.';
    if ($groupId === '') $errors[] = 'Group ID is required.';

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare(
                'UPDATE students SET fullname = :fullname, matricule = :matricule, group_id = :group_id WHERE id = :id'
            );
            $stmt->execute([
                ':fullname'  => $fullname,
                ':matricule' => $matricule,
                ':group_id'  => $groupId,
                ':id'        => $id,
            ]);
            header('Location: list_students.php');
            exit;
        } catch (Throwable $e) {
            $errors[] = 'Database error while updating student.';
        }
    }
} else {
    // Load current data
    $stmt = $pdo->prepare('SELECT id, fullname, matricule, group_id FROM students WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $student = $stmt->fetch();
    if (!$student) {
        http_response_code(404);
        echo 'Student not found.';
        exit;
    }

    $fullname  = $student['fullname'];
    $matricule = $student['matricule'];
    $groupId   = $student['group_id'];
    $errors    = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Student</title>
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
        .error { color: var(--danger); font-size: 14px; margin-top: 4px; }
        ul.error { list-style: none; padding: 0; }
        ul.error li { margin: 4px 0; }
        a { color: var(--primary); text-decoration: none; margin-left: 12px; }
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
        <h1>Update Student #<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?></h1>

        <?php if (!empty($errors)): ?>
            <ul class="error">
                <?php foreach ($errors as $e): ?>
                    <li><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="fullname">Full name</label>
                <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($fullname, ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="form-group">
                <label for="matricule">Matricule</label>
                <input type="text" id="matricule" name="matricule" value="<?php echo htmlspecialchars($matricule, ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="form-group">
                <label for="group_id">Group ID</label>
                <input type="text" id="group_id" name="group_id" value="<?php echo htmlspecialchars($groupId, ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <button type="submit">Save</button>
            <a href="list_students.php">Cancel</a>
        </form>
    </div>
</body>
</html>


