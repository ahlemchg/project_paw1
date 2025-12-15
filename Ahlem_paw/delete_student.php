<?php
require __DIR__ . '/auth.php';
// delete_student.php
// Delete a student from the `students` table.

require __DIR__ . '/db_connect.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(400);
    echo 'Invalid student ID.';
    exit;
}

try {
    $pdo = get_db_connection();
    $stmt = $pdo->prepare('DELETE FROM students WHERE id = :id');
    $stmt->execute([':id' => $id]);
} catch (Throwable $e) {
    http_response_code(500);
    echo 'Database error while deleting student.';
    exit;
}

header('Location: list_students.php');
exit;


