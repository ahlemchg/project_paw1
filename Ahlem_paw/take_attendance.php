<?php
require __DIR__ . '/auth.php';
// take_attendance.php

// ---------- Configuration ----------
$studentsFile    = __DIR__ . '/students.json';
$today           = date('Y-m-d');
$attendanceFile  = __DIR__ . "/attendance_{$today}.json";

// ---------- Helpers ----------
function load_students_attendance($filePath)
{
    if (!file_exists($filePath)) {
        return [];
    }

    $json = file_get_contents($filePath);
    if ($json === false || trim($json) === '') {
        return [];
    }

    $data = json_decode($json, true);
    if (!is_array($data)) {
        return [];
    }

    return $data;
}

function save_attendance_file($filePath, array $attendance)
{
    $json = json_encode($attendance, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        throw new RuntimeException('Failed to encode attendance data.');
    }

    if (file_put_contents($filePath, $json) === false) {
        throw new RuntimeException('Failed to write attendance file.');
    }
}

// ---------- Handle POST (submit attendance) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // If file for today already exists, do NOT overwrite
    if (file_exists($attendanceFile)) {
        $alreadyTaken = true;
    } else {
        $alreadyTaken = false;
        $attendance   = [];

        // Expecting: $_POST['status'][student_id] = 'present'|'absent'
        $statuses = isset($_POST['status']) && is_array($_POST['status']) ? $_POST['status'] : [];

        foreach ($statuses as $studentId => $status) {
            $status = ($status === 'present') ? 'present' : 'absent';
            $attendance[] = [
                'student_id' => (string)$studentId,
                'status'     => $status,
            ];
        }

        try {
            save_attendance_file($attendanceFile, $attendance);
        } catch (RuntimeException $e) {
            $errorMsg = $e->getMessage();
        }
    }

    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Take Attendance - Result</title>
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
            .message-ok { color: var(--success); }
            .message-error { color: var(--danger); }
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
            <h1>Attendance for <?php echo htmlspecialchars($today, ENT_QUOTES, 'UTF-8'); ?></h1>

            <?php if (!empty($errorMsg)): ?>
                <p class="message-error"><?php echo htmlspecialchars($errorMsg, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php elseif (!empty($alreadyTaken) && $alreadyTaken): ?>
                <p class="message-error">Attendance for today has already been taken.</p>
            <?php else: ?>
                <p class="message-ok">Attendance saved successfully.</p>
            <?php endif; ?>

            <p><a href="take_attendance.php">Back to attendance form</a></p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// ---------- Handle GET (show form) ----------
$students = load_students_attendance($studentsFile);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Take Attendance</title>
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
        .no-students {
            color: var(--muted);
        }
        button {
            margin-top: 16px;
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
    <h1>Take Attendance (<?php echo htmlspecialchars($today, ENT_QUOTES, 'UTF-8'); ?>)</h1>

    <?php if (empty($students)): ?>
        <p class="no-students">No students found in students.json.</p>
    <?php else: ?>
        <form method="post" action="take_attendance.php">
            <table>
                <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Group</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($students as $s): ?>
                    <?php
                    $sid   = isset($s['student_id']) ? $s['student_id'] : '';
                    $name  = isset($s['name']) ? $s['name'] : '';
                    $group = isset($s['group']) ? $s['group'] : '';
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($sid, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($group, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <label>
                                <input type="radio"
                                       name="status[<?php echo htmlspecialchars($sid, ENT_QUOTES, 'UTF-8'); ?>]"
                                       value="present"
                                       checked>
                                Present
                            </label>
                            <label style="margin-left:8px;">
                                <input type="radio"
                                       name="status[<?php echo htmlspecialchars($sid, ENT_QUOTES, 'UTF-8'); ?>]"
                                       value="absent">
                                Absent
                            </label>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <button type="submit">Save Attendance</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>


