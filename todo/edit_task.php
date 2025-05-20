<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $task_id = $_POST['id'];
    $new_task = $_POST['task'];
    $new_deadline = $_POST['deadline'];

    try {
        // Validasi input
        if (empty($new_task) || empty($new_deadline)) {
            throw new Exception("Semua field harus diisi");
        }

        // Update task
        $stmt = $pdo->prepare("UPDATE tasks 
                              SET task = ?, deadline = ?
                              WHERE id = ? AND user_id = ?");
        $stmt->execute([
            $new_task,
            $new_deadline,
            $task_id,
            $_SESSION['user_id']
        ]);

        // Redirect dengan pesan sukses
        $_SESSION['success'] = "Task berhasil diupdate!";
        header("Location: dashboard.php");
        exit();

    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: dashboard.php");
        exit();
    }
} else {
    header("Location: dashboard.php");
    exit();
}
?>