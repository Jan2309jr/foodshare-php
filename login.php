<?php
session_start();
require_once 'config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}

include 'views/layout/header.php';
?>

<div class="card" style="max-width: 500px; margin: 2rem auto;">
    <h2>Welcome Back</h2>
    <?php if ($error): ?>
        <p style="color: red; margin-bottom: 1rem;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div>
            <label>Email Address</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
    </form>
    <p style="margin-top: 1rem; text-align: center;">Don't have an account? <a href="register.php">Register here</a></p>
</div>

<?php include 'views/layout/footer.php'; ?>
