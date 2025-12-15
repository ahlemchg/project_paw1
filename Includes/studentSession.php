<?php
session_start();

if (!isset($_SESSION['userId']) || !isset($_SESSION['studentId']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'Student') {
    echo "<script type = \"text/javascript\">
    window.location = (\"../index.php\");
    </script>";
    exit();
}
?>

