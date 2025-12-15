<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['authenticated'])) {
    $msg = urlencode('you have to be logged in and have the right to access the page');
    header('Location: login.php?msg=' . $msg);
    exit;
}
