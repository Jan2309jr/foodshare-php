<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'donor') {
    header("Location: login.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $food_name = trim($_POST['food_name']);
    $quantity = trim($_POST['quantity']);
    $location = trim($_POST['location']);
    $expiry = $_POST['expiry'];
    $donor_id = $_SESSION['user_id'];
    $image_url = null;

    // Handle Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $target_dir = "uploads/";
        $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $file_name = time() . '_' . uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = $target_file;
        }
    }

    if (empty($food_name) || empty($quantity) || empty($location) || empty($expiry)) {
        $error = "All fields are required.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO food_listings (donor_id, food_name, quantity, location, expiry, image_url) VALUES (?, ?, ?, ?, ?, ?)");
        try {
            $stmt->execute([$donor_id, $food_name, $quantity, $location, $expiry, $image_url]);
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
    <h2>List New Surplus Food</h2>
    <?php if ($error): ?>
        <p style="color: red; margin-bottom: 1rem;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form action="add_food.php" method="POST" enctype="multipart/form-data">
        <div>
            <label>Food Item Name</label>
            <input type="text" name="food_name" placeholder="e.g. 5kg Biryani, 10 Packets Bread" required>
        </div>
        <div>
            <label>Quantity</label>
            <input type="text" name="quantity" placeholder="e.g. 10 portions" required>
        </div>
        <div>
            <label>Pickup Location</label>
            <input type="text" name="location" placeholder="e.g. Downtown Mall Entrance" required>
        </div>
        <div>
            <label>Expiry Date & Time</label>
            <input type="datetime-local" name="expiry" required>
        </div>
        <div>
            <label>Food Image</label>
            <input type="file" name="image" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">Post Listing</button>
    </form>
</div>

<?php include 'views/layout/footer.php'; ?>
