<?php
session_start();
require_once 'config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        $error = "All fields are required.";
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email already registered.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
            try {
                $stmt->execute([$name, $email, $phone, $hashedPassword, $role]);
                $success = "Registration successful! You can now <a href='login.php'>login</a>.";
            } catch (PDOException $e) {
                $error = "Error: " . $e->getMessage();
            }
        }
    }
}

include 'views/layout/header.php';
?>

<div class="card" style="max-width: 500px; margin: 2rem auto;">
    <h2>Join FoodShare</h2>
    <?php if ($error): ?>
        <p style="color: red; margin-bottom: 1rem;"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color: green; margin-bottom: 1rem;"><?php echo $success; ?></p>
    <?php endif; ?>

    <form action="register.php" method="POST">
        <div>
            <label>Full Name</label>
            <input type="text" name="name" required>
        </div>
        <div>
            <label>Email Address</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <label>Phone Number (for WhatsApp)</label>
            <input type="text" name="phone" placeholder="e.g. 919876543210" required>
        </div>
        <div>
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <div>
            <label>I want to:</label>
            <select name="role">
                <option value="receiver">Receive Food</option>
                <option value="donor">Donate Food</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">Create Account</button>
    </form>
    <p style="margin-top: 1rem; text-align: center;">Already have an account? <a href="login.php">Login here</a></p>
</div>

<?php include 'views/layout/footer.php'; ?>
