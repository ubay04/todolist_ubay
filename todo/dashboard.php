<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$tasks = [];
try {
    // Handle form tambah task
    if (isset($_POST['add_task'])) {
        $task = $_POST['task'];
        $deadline = $_POST['deadline'];
        
        $stmt = $pdo->prepare("INSERT INTO tasks (user_id, task, deadline) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $task, $deadline]);
        
        $_SESSION['success'] = "Task berhasil ditambahkan!";
        header("Location: dashboard.php");
        exit();
    }

    // Handle hapus task
    if (isset($_GET['delete'])) {
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
        $stmt->execute([$_GET['delete'], $_SESSION['user_id']]);
    }

    // Handle toggle status
    if (isset($_GET['toggle'])) {
        $stmt = $pdo->prepare("UPDATE tasks SET status = IF(status='completed','pending','completed') WHERE id = ? AND user_id = ?");
        $stmt->execute([$_GET['toggle'], $_SESSION['user_id']]);
    }

    // Ambil data tasks
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY deadline ASC");
    $stmt->execute([$_SESSION['user_id']]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

include 'header.php';
?>

<div class="dashboard-container">
    <!-- Notifikasi -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success mb-4"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger mb-4"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Form Tambah Task -->
    <div class="card task-form-card mb-4">
        <div class="card-body">
            <form method="post" class="row g-3 align-items-center">
                <div class="col-md-6">
                    <input type="text" name="task" class="form-control" 
                           placeholder="Task baru..." required>
                </div>
                <div class="col-md-4">
                    <input type="date" name="deadline" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="add_task" 
                            class="btn btn-primary w-100">
                        <i class="bi bi-plus-lg"></i> Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Daftar Task -->
    <div class="card task-list-card">
        <div class="card-body">
            <ul class="list-group">
                <?php if (!empty($tasks)): ?>
                    <?php foreach ($tasks as $task): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto task-content-wrapper">
                            <div class="d-flex align-items-center mb-2">
                                <span class="status-indicator <?= $task['status'] == 'completed' ? 
                                    'status-completed' : 'status-pending' ?> me-3"></span>
                                
                                <!-- Form Update -->
                                <form method="post" action="edit_task.php" class="d-inline-flex gap-2">
                                    <input type="hidden" name="id" value="<?= $task['id'] ?>">
                                    <input type="text" name="task" 
                                           value="<?= htmlspecialchars($task['task']) ?>"
                                           class="form-control form-control-sm">
                                    <input type="date" name="deadline" 
                                           value="<?= $task['deadline'] ?>"
                                           class="form-control form-control-sm">
                                    <button type="submit" 
                                            class="btn btn-sm btn-outline-primary">
                                        Update
                                    </button>
                                </form>
                            </div>
                            
                            <?php if (date('Y-m-d') > $task['deadline'] && $task['status'] != 'completed'): ?>
                                <span class="badge bg-danger">Terlambat</span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="btn-group">
                            <a href="?toggle=<?= $task['id'] ?>" 
                               class="btn btn-sm <?= $task['status'] == 'completed' ? 
                               'btn-success' : 'btn-outline-secondary' ?>">
                                <i class="bi bi-check2"></i>
                            </a>
                            <a href="?delete=<?= $task['id'] ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Yakin hapus?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="list-group-item text-center text-muted py-4">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        Tidak ada task yang tersedia
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>