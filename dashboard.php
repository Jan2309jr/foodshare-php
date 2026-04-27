<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['user_role'];

if ($role === 'donor') {
    include 'donor_dashboard.php';
} elseif ($role === 'receiver') {
    include 'receiver_dashboard.php';
} else {
    echo "Unknown role.";
}
?>
