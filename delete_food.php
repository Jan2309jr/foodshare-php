<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'donor') {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? null;
$donor_id = $_SESSION['user_id'];

if ($id) {
    // Fetch image URL to delete file
    $stmt = $pdo->prepare("SELECT image_url FROM food_listings WHERE id = ? AND donor_id = ?");
    $stmt->execute([$id, $donor_id]);
    $listing = $stmt->fetch();

    if ($listing) {
        if ($listing['image_url'] && file_exists($listing['image_url'])) {
            unlink($listing['image_url']);
        }
        
        $stmt = $pdo->prepare("DELETE FROM food_listings WHERE id = ? AND donor_id = ?");
        $stmt->execute([$id, $donor_id]);
    }
}

header("Location: dashboard.php");
exit();
?>
