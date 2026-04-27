<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'donor') {
    header("Location: login.php");
    exit();
}

$request_id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;
$donor_id = $_SESSION['user_id'];

if ($request_id && $action) {
    // Verify that the request is for a listing owned by this donor
    $stmt = $pdo->prepare("
        SELECT r.* FROM requests r
        JOIN food_listings f ON r.food_id = f.id
        WHERE r.id = ? AND f.donor_id = ?
    ");
    $stmt->execute([$request_id, $donor_id]);
    $request = $stmt->fetch();

    if ($request) {
        $new_status = ($action === 'accept') ? 'accepted' : 'rejected';
        $stmt = $pdo->prepare("UPDATE requests SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $request_id]);

        // If accepted, we might want to mark the food as completed/unavailable
        if ($action === 'accept') {
            $stmt = $pdo->prepare("UPDATE food_listings SET status = 'completed' WHERE id = ?");
            $stmt->execute([$request['food_id']]);
        }
    }
}

header("Location: dashboard.php");
exit();
?>
