<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'donor') {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? null;
$donor_id = $_SESSION['user_id'];

// Fetch the listing
$stmt = $pdo->prepare("SELECT * FROM food_listings WHERE id = ? AND donor_id = ?");
$stmt->execute([$id, $donor_id]);
$listing = $stmt->fetch();

if (!$listing) {
    die("Listing not found or access denied.");
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $food_name = trim($_POST['food_name']);
    $quantity = trim($_POST['quantity']);
    $location = trim($_POST['location']);
    $expiry = $_POST['expiry'];
    $image_url = $listing['image_url'];

    // Handle Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $target_dir = "uploads/";
        $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $file_name = time() . '_' . uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Delete old image if exists
            if ($image_url && file_exists($image_url)) {
                unlink($image_url);
            }
            $image_url = $target_file;
        }
    }

    if (empty($food_name) || empty($quantity) || empty($location) || empty($expiry)) {
        $error = "All fields are required.";
    } else {
        $stmt = $pdo->prepare("UPDATE food_listings SET food_name = ?, quantity = ?, location = ?, expiry = ?, image_url = ? WHERE id = ? AND donor_id = ?");
        try {
            $stmt->execute([$food_name, $quantity, $location, $expiry, $image_url, $id, $donor_id]);
            header("Location: dashboard.php");
            exit();
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

include 'views/layout/header.php';
?>

<div style="margin-bottom: 2rem;">
    <a href="dashboard.php" class="btn">&larr; Back to Dashboard</a>
</div>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h2>Edit Food Listing</h2>
    <?php if ($error): ?>
        <p style="color: red; margin-bottom: 1rem;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form action="edit_food.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
        <div>
            <label>Food Item Name</label>
            <input type="text" name="food_name" value="<?php echo htmlspecialchars($listing['food_name']); ?>" required>
        </div>
        <div>
            <label>Quantity</label>
            <input type="text" name="quantity" value="<?php echo htmlspecialchars($listing['quantity']); ?>" required>
        </div>
        <div>
            <label>Pickup Location</label>
            <input type="text" name="location" value="<?php echo htmlspecialchars($listing['location']); ?>" required>
        </div>
        <div>
            <label>Expiry Date & Time</label>
            <input type="datetime-local" name="expiry" value="<?php echo date('Y-m-d\TH:i', strtotime($listing['expiry'])); ?>" required>
        </div>
        <div>
            <label>Current Image</label><br>
            <?php if ($listing['image_url']): ?>
                <img src="<?php echo $listing['image_url']; ?>" style="max-width: 100px; margin-bottom: 1rem; border: 1px solid #ccc;">
            <?php else: ?>
                <p>No image uploaded.</p>
            <?php endif; ?>
            <label>Upload New Image (Optional)</label>
            <input type="file" name="image" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">Update Listing</button>
    </form>
</div>

<?php include 'views/layout/footer.php'; ?>
