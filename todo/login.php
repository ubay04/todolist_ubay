<?php
require 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}

include 'header.php';
?>

<div class="dashboard-container">
    <div class="card login-card">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0"><i class="bi bi-box-arrow-in-right me-2"></i>Login</h2>
        </div>
        
        <div class="card-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="post" class="login-form">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control form-control-lg" required>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control form-control-lg" required>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                    </button>
                    <a href="register.php" class="btn btn-outline-secondary">
                        <i class="bi bi-person-plus me-2"></i>Daftar Akun Baru
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>