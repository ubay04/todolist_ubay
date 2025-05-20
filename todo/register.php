<?php
require 'config.php';

// Redirect jika sudah login
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$username, $email, $password])) {
            $_SESSION['success'] = "Registrasi berhasil! Silahkan login";
            header("Location: login.php");
            exit();
        }
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) {
            $error = "Username atau email sudah terdaftar!";
        } else {
            $error = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}

include 'header.php'; 
?>

<div class="dashboard-container">
    <div class="card register-card">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0"><i class="bi bi-person-plus me-2"></i>Registrasi</h2>
        </div>
        
        <div class="card-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" class="register-form">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-person"></i>
                        </span>
                        <input type="text" name="username" class="form-control form-control-lg" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input type="email" name="email" class="form-control form-control-lg" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input type="password" name="password" class="form-control form-control-lg" required>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-person-plus me-2"></i>Daftar Sekarang
                    </button>
                    <a href="login.php" class="btn btn-outline-secondary">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Sudah Punya Akun? Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>