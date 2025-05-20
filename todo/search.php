<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$tasks = [];

if (!empty($search)) {
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? AND task LIKE ? ORDER BY deadline ASC");
    $stmt->execute([$_SESSION['user_id'], "%$search%"]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

include 'header.php';
?>

<div class="dashboard-container">
    <!-- Form Pencarian -->
    <div class="card task-form-card mb-4">
        <div class="card-body">
            <h2 class="card-title mb-4">Cari Task</h2>
            <form method="get" class="row g-3 align-items-center">
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Masukkan kata kunci..." 
                           value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Hasil Pencarian -->
    <?php if (!empty($search)): ?>
    <div class="card task-list-card">
        <div class="card-body">
            <?php if (count($tasks) > 0): ?>
                <ul class="list-group">
                    <?php foreach ($tasks as $task): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto task-content-wrapper">
                            <div class="d-flex align-items-center mb-2">
                                <span class="status-indicator <?= $task['status'] == 'completed' ? 
                                    'status-completed' : 'status-pending' ?> me-3"></span>
                                
                                <div class="task-details">
                                    <div class="task-text"><?= htmlspecialchars($task['task']) ?></div>
                                    <div class="task-deadline text-muted small mt-1">
                                        Deadline: <?= date('d/m/Y', strtotime($task['deadline'])) ?>
                                    </div>
                                </div>
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
                </ul>
            <?php else: ?>
                <div class="alert alert-info text-center py-4">
                    <i class="bi bi-exclamation-circle fs-1 d-block mb-2"></i>
                    Tidak ditemukan task dengan kata kunci "<?= htmlspecialchars($search) ?>"
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>