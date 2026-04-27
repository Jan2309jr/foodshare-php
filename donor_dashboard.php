<?php
// donor_dashboard.php
require_once 'config/db.php';

$donor_id = $_SESSION['user_id'];

// Fetch my listings
$stmt = $pdo->prepare("SELECT * FROM food_listings WHERE donor_id = ? ORDER BY created_at DESC");
$stmt->execute([$donor_id]);
$listings = $stmt->fetchAll();

// Fetch incoming requests for my food
$stmt = $pdo->prepare("
    SELECT r.*, f.food_name, u.name as receiver_name, u.email as receiver_email 
    FROM requests r
    JOIN food_listings f ON r.food_id = f.id
    JOIN users u ON r.receiver_id = u.id
    WHERE f.donor_id = ? AND r.status = 'pending'
    ORDER BY r.created_at DESC
");
$stmt->execute([$donor_id]);
$incoming_requests = $stmt->fetchAll();

include 'views/layout/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1>Donor Dashboard</h1>
    <a href="add_food.php" class="btn btn-primary">+ List New Food</a>
</div>

<div class="grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
    <!-- My Listings -->
    <section>
        <h2>Your Food Listings</h2>
        <?php if (empty($listings)): ?>
            <div class="card">
                <p>You haven't posted any food yet.</p>
            </div>
        <?php else: ?>
            <?php foreach ($listings as $item): ?>
                <div class="card">
                    <?php if ($item['image_url']): ?>
                        <img src="<?php echo $item['image_url']; ?>" style="width: 100%; height: 150px; object-fit: cover; margin-bottom: 1rem; border: 1px solid var(--border);">
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($item['food_name']); ?></h3>
                    <p><strong>Quantity:</strong> <?php echo htmlspecialchars($item['quantity']); ?></p>
                    <p><strong>Status:</strong> <span class="status-badge"><?php echo $item['status']; ?></span></p>
                    <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                        <a href="edit_food.php?id=<?php echo $item['id']; ?>" class="btn" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; background: var(--accent);">Edit</a>
                        <a href="delete_food.php?id=<?php echo $item['id']; ?>" class="btn" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; color: red; border-color: red;" onclick="return confirm('Are you sure you want to delete this listing?')">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

    <!-- Incoming Requests -->
    <section>
        <h2>Incoming Requests</h2>
        <?php if (empty($incoming_requests)): ?>
            <div class="card">
                <p>No pending requests at the moment.</p>
            </div>
        <?php else: ?>
            <?php foreach ($incoming_requests as $req): ?>
                <div class="card">
                    <p><strong><?php echo htmlspecialchars($req['receiver_name']); ?></strong> requested <strong><?php echo htmlspecialchars($req['food_name']); ?></strong></p>
                    <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                        <a href="handle_request.php?id=<?php echo $req['id']; ?>&action=accept" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; background: green; border-color: green;">Accept</a>
                        <a href="handle_request.php?id=<?php echo $req['id']; ?>&action=reject" class="btn" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; color: red; border-color: red;">Reject</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</div>

<?php include 'views/layout/footer.php'; ?>
