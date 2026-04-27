<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'receiver') {
    header("Location: login.php");
    exit();
}

$food_id = $_GET['id'] ?? null;
$receiver_id = $_SESSION['user_id'];

if ($food_id) {
    // Check if already requested
    $stmt = $pdo->prepare("SELECT id FROM requests WHERE food_id = ? AND receiver_id = ?");
    $stmt->execute([$food_id, $receiver_id]);
    
    if (!$stmt->fetch()) {
        // Log the request in DB
        $stmt = $pdo->prepare("INSERT INTO requests (food_id, receiver_id, status) VALUES (?, ?, 'pending')");
        $stmt->execute([$food_id, $receiver_id]);
    }
}

// Redirect back to dashboard to see the request status
header("Location: dashboard.php");
exit();
?>
