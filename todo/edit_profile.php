<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Inisialisasi variabel
$user_id = $_SESSION['user_id'];
$error = '';
$user = [];

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $profile_image = $_FILES['profile_image'];

        // Update email
        $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
        $stmt->execute([$email, $user_id]);

        // Update password jika diisi
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed_password, $user_id]);
        }

        // Upload gambar profil
        if ($profile_image['error'] == UPLOAD_ERR_OK) {
            $target_dir = "uploads/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $file_ext = pathinfo($profile_image['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $file_ext;
            $target_file = $target_dir . $filename;
            
            if (move_uploaded_file($profile_image['tmp_name'], $target_file)) {
                $stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
                $stmt->execute([$filename, $user_id]);
                // Update session dan cache
                $_SESSION['profile_image'] = $filename;
                header("Cache-Control: no-cache, must-revalidate");
            } else {
                $error = "Gagal mengupload gambar";
            }
        }

        // Redirect jika tidak ada error
        if (empty($error)) {
            $_SESSION['success'] = "Profil berhasil diperbarui!";
            header("Location: dashboard.php");
            exit();
        }
    }

    // Ambil data user terbaru
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Terjadi kesalahan database: " . $e->getMessage();
}

include 'header.php'; 
?>

<div class="dashboard-container">
    <div class="card edit-profile-card">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0"><i class="bi bi-person-circle me-2"></i>Edit Profil</h2>
        </div>
        
        <div class="card-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data" class="edit-profile-form">
                <div class="row g-3">
                    <!-- Email -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Alamat Email</label>
                            <input type="email" name="email" class="form-control" 
                                    value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-control" 
                                   placeholder="Kosongkan jika tidak ingin mengubah">
                            <small class="form-text text-muted">Minimal 8 karakter</small>
                        </div>
                    </div>

                    <!-- Foto Profil -->
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label">Foto Profil</label>
                            <div class="d-flex align-items-center gap-3">
                                <?php if (!empty($user['profile_image'])): ?>
                                    <img src="uploads/<?= htmlspecialchars($user['profile_image']) ?>" 
                                         class="profile-preview img-thumbnail rounded-circle" 
                                         width="100" 
                                         alt="Foto Profil">
                                <?php else: ?>
                                    <div class="no-photo-placeholder rounded-circle">
                                        <i class="bi bi-person fs-1"></i>
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="profile_image" 
                                       class="form-control w-auto" 
                                       accept="image/*">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>