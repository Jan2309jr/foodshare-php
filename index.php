<?php
session_start();
require_once 'config/db.php';

// Fetch available food listings
$stmt = $pdo->prepare("
    SELECT f.*, u.name as donor_name 
    FROM food_listings f
    JOIN users u ON f.donor_id = u.id
    WHERE f.status = 'available' AND f.expiry > NOW()
    ORDER BY f.created_at DESC
    LIMIT 6
");
$stmt->execute();
$available_food = $stmt->fetchAll();

include 'views/layout/header.php';
?>

<div style="text-align: center; padding: 4rem 0;">
    <h1 style="font-size: 4rem; margin-bottom: 1rem; color: var(--primary);">Feed Your Community, <br> Not the Landfill.</h1>
    <p style="font-size: 1.5rem; max-width: 800px; margin: 0 auto 3rem;">
        FoodShare connects event organizers with local communities to share surplus food. 
        Simple, direct, and impactful.
    </p>

    <div style="display: flex; gap: 2rem; justify-content: center;">
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="register.php" class="btn btn-primary" style="padding: 1.5rem 3rem; font-size: 1.2rem;">Get Started</a>
            <a href="login.php" class="btn" style="padding: 1.5rem 3rem; font-size: 1.2rem;">Login</a>
        <?php else: ?>
            <a href="dashboard.php" class="btn btn-primary" style="padding: 1.5rem 3rem; font-size: 1.2rem;">Go to Dashboard</a>
        <?php endif; ?>
    </div>
</div>

<!-- Public Listings Section -->
<section style="margin-bottom: 4rem;">
    <h2 style="font-size: 2.5rem; text-align: center; margin-bottom: 2rem;">Current Food Availability</h2>
    <?php if (empty($available_food)): ?>
        <p style="text-align: center;">No food listings available right now. Check back later!</p>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 2rem;">
            <?php foreach ($available_food as $item): ?>
                <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <?php if ($item['image_url']): ?>
                            <img src="<?php echo $item['image_url']; ?>" style="width: 100%; height: 200px; object-fit: cover; border-bottom: 2px solid var(--border); margin-bottom: 1rem;">
                        <?php endif; ?>
                        <h3><?php echo htmlspecialchars($item['food_name']); ?></h3>
                        <p style="color: #666; font-size: 0.9rem;">Listed by <?php echo htmlspecialchars($item['donor_name']); ?></p>
                        <p style="margin: 1rem 0;"><strong>Location:</strong> <?php echo htmlspecialchars($item['location']); ?></p>
                        <p><strong>Quantity:</strong> <?php echo htmlspecialchars($item['quantity']); ?></p>
                    </div>
                    
                    <div style="margin-top: 1.5rem;">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if ($_SESSION['user_role'] === 'receiver'): ?>
                                <a href="request_food.php?id=<?php echo $item['id']; ?>" class="btn btn-primary" style="width: 100%; text-align: center;">Avail Now</a>
                            <?php else: ?>
                                <button class="btn" disabled style="width: 100%; opacity: 0.5;">Donors cannot avail</button>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-primary" style="width: 100%; text-align: center;">Login to Avail</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align: center; margin-top: 2rem;">
            <a href="register.php" style="font-weight: 700; color: var(--primary);">View all listings &rarr;</a>
        </div>
    <?php endif; ?>
</section>

<section style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 2rem; margin-top: 4rem;">
    <div class="card" style="text-align: center;">
        <h3 style="font-size: 1.5rem; margin-bottom: 1rem;">1. List Surplus</h3>
        <p>Donors list surplus food from weddings, corporate events, or parties.</p>
    </div>
    <div class="card" style="text-align: center;">
        <h3 style="font-size: 1.5rem; margin-bottom: 1rem;">2. Communities Request</h3>
        <p>Verified receivers browse and request food items based on need.</p>
    </div>
    <div class="card" style="text-align: center;">
        <h3 style="font-size: 1.5rem; margin-bottom: 1rem;">3. Direct Pickup</h3>
        <p>Once accepted, the receiver picks up the food from the specified location.</p>
    </div>
</section>

<?php include 'views/layout/footer.php'; ?>
