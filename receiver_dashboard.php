<?php
// receiver_dashboard.php
require_once 'config/db.php';

$receiver_id = $_SESSION['user_id'];

// Fetch available food listings
$stmt = $pdo->prepare("
    SELECT f.*, u.name as donor_name 
    FROM food_listings f
    JOIN users u ON f.donor_id = u.id
    WHERE f.status = 'available' AND f.expiry > NOW()
    ORDER BY f.created_at DESC
");
$stmt->execute();
$available_food = $stmt->fetchAll();

// Fetch my requests
$stmt = $pdo->prepare("
    SELECT r.*, f.food_name, f.location, u.name as donor_name
    FROM requests r
    JOIN food_listings f ON r.food_id = f.id
    JOIN users u ON f.donor_id = u.id
    WHERE r.receiver_id = ?
    ORDER BY r.created_at DESC
");
$stmt->execute([$receiver_id]);
$my_requests = $stmt->fetchAll();

include 'views/layout/header.php';
?>

<div style="margin-bottom: 2rem;">
    <h1>Receiver Dashboard</h1>
    <p>Find surplus food from events near you.</p>
</div>

<div class="grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
    <!-- Available Food -->
    <section>
        <h2>Available Food</h2>
        <?php if (empty($available_food)): ?>
            <div class="card">
                <p>No food available at the moment. Check back soon!</p>
            </div>
        <?php else: ?>
            <?php foreach ($available_food as $item): ?>
                <div class="card">
                    <?php if ($item['image_url']): ?>
                        <img src="<?php echo $item['image_url']; ?>" style="width: 100%; height: 150px; object-fit: cover; margin-bottom: 1rem; border: 1px solid var(--border);">
                    <?php endif; ?>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <h3><?php echo htmlspecialchars($item['food_name']); ?></h3>
                            <p style="font-size: 0.9rem; color: #666;">Listed by <?php echo htmlspecialchars($item['donor_name']); ?></p>
                        </div>
                        <span class="status-badge" style="background: var(--accent);"><?php echo htmlspecialchars($item['quantity']); ?></span>
                    </div>
                    <p style="margin: 1rem 0;"><strong>Location:</strong> <?php echo htmlspecialchars($item['location']); ?></p>
                    
                    <?php
                    // Check if already requested
                    $check = $pdo->prepare("SELECT id FROM requests WHERE food_id = ? AND receiver_id = ?");
                    $check->execute([$item['id'], $receiver_id]);
                    if ($check->fetch()): ?>
                        <button class="btn" disabled style="opacity: 0.5; width: 100%;">Already Requested</button>
                    <?php else: ?>
                        <a href="request_food.php?id=<?php echo $item['id']; ?>" class="btn btn-primary" style="width: 100%; text-align: center;">Request This Food</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

    <!-- My Requests -->
    <section>
        <h2>Your Request Status</h2>
        <?php if (empty($my_requests)): ?>
            <div class="card">
                <p>You haven't requested any food yet.</p>
            </div>
        <?php else: ?>
            <?php foreach ($my_requests as $req): ?>
                <div class="card">
                    <p>Request for <strong><?php echo htmlspecialchars($req['food_name']); ?></strong></p>
                    <p style="font-size: 0.85rem; margin-bottom: 1rem;">From: <?php echo htmlspecialchars($req['donor_name']); ?> @ <?php echo htmlspecialchars($req['location']); ?></p>
                    <span class="status-badge status-<?php echo $req['status']; ?>">
                        <?php echo $req['status']; ?>
                    </span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</div>

<?php include 'views/layout/footer.php'; ?>
