<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TodoList App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        .navbar-brand .apps-part {
            color:rgb(48, 117, 236); /* Warna biru Bootstrap */
            font-weight: 600;
        }
        .profile-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
            border: 2px solid #fff;
        }
        .profile-icon.bg-secondary {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">
            Todo<span class="apps-part">Apps</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="search.php">Cari Task</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="edit_profile.php">Edit Profil</a>
                    </li>
                <?php endif; ?>
            </ul>
            <span class="navbar-text">
                <?php if(isset($_SESSION['username'])): ?>
                    <div class="d-flex align-items-center">
                        <?php 
                        // Ambil data profil terbaru dari database
                        $stmt = $pdo->prepare("SELECT profile_image FROM users WHERE id = ?");
                        $stmt->execute([$_SESSION['user_id']]);
                        $user = $stmt->fetch();
                        ?>
                        
                        <?php if(!empty($user['profile_image'])): ?>
                            <img src="uploads/<?= $user['profile_image'] ?>" 
                                 class="profile-icon" 
                                 alt="Profile Picture"
                                 id="navbar-profile-image">
                        <?php else: ?>
                            <div class="profile-icon bg-secondary d-flex align-items-center justify-content-center">
                                <i class="bi bi-person fs-5 text-white"></i>
                            </div>
                        <?php endif; ?>
                        
                        <span class="me-2">
                            Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!
                        </span>
                        <a href="logout.php" class="btn btn-sm btn-danger">Logout</a>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="btn btn-sm btn-outline-secondary">Login</a>
                    <a href="register.php" class="btn btn-sm btn-primary">Daftar</a>
                <?php endif; ?>
            </span>
        </div>
    </div>
</nav>