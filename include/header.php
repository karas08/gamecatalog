<?php 
ob_start(); 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

include_once "track_online.php";

$root = "/"; 
$pages = [
    'home' => $root . "index.php",
    'login' => $root . "login/index.php",
    'logout' => $root . "login/logout.php",
    'reg' => $root . "login/register.php",
    'admin' => $root . "admin/index.php",
    'profile' => $root . "profile.php",
    'random' => $root . "random.php"
];
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameCat — Твій ігровий простір</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --site-bg: linear-gradient(to right, #24243e, #302b63, #0f0c29);
            --site-text: #ffffff;
            --card-glass: rgba(255, 255, 255, 0.05);
            --card-border: rgba(255, 255, 255, 0.1);
        }
        body { background: var(--site-bg); color: var(--site-text); font-family: 'Inter', sans-serif; min-height: 100vh; }
        .navbar { background: rgba(0, 0, 0, 0.5) !important; backdrop-filter: blur(15px); border-bottom: 1px solid rgba(255,255,255,0.1); }
        .logo { font-family: 'Orbitron', sans-serif; color: #00d2ff !important; text-decoration: none; font-size: 1.5rem; font-weight: bold; }
        .btn-custom { background: #00d2ff; color: #fff; border-radius: 10px; border: none; padding: 6px 18px; }
        
        /* Адаптація мобільного меню */
        @media (max-width: 991px) {
            .navbar-collapse {
                background: rgba(0, 0, 0, 0.9);
                padding: 15px;
                border-radius: 10px;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
  <div class="container">
    <a class="logo" href="<?= $pages['home'] ?>">🐱 GAMECAT</a>
    
    <!-- Кнопка бургер (стандарт Bootstrap) -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
        <!-- Пошук -->
        <form action="<?= $pages['home'] ?>" method="GET" class="d-flex mx-lg-auto my-2 my-lg-0" style="max-width: 350px; width: 100%;">
            <div class="input-group input-group-sm">
                <input type="text" name="search" id="searchInput" class="form-control bg-dark text-white border-secondary" placeholder="Пошук ігор..." autocomplete="off">
                <button class="btn btn-outline-info" type="submit"><i class="fas fa-search"></i></button>
            </div>
        </form>
        
        <!-- Меню -->
        <div class="navbar-nav align-items-lg-center gap-2">
            <a class="nav-link text-warning fw-bold" href="<?= $pages['random'] ?>">🎲 Мені пощастить</a>
            
            <button class="btn btn-sm btn-outline-secondary border-0 text-white" onclick="toggleTheme()">🌙</button>

            <?php if(isset($_SESSION['username'])): ?>
                <a class="btn btn-outline-info btn-sm" href="<?= $pages['profile'] ?>"><i class="fas fa-user"></i> Профіль</a>
                <?php if((isset($_SESSION['role']) && $_SESSION['role'] === 'admin') || (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1)): ?>
                    <a class="btn btn-outline-warning btn-sm" href="<?= $pages['admin'] ?>"><i class="fas fa-tools"></i> Admin</a>
                <?php endif; ?>
                <a class="btn btn-outline-danger btn-sm" href="<?= $pages['logout'] ?>"><i class="fas fa-sign-out-alt"></i></a>
            <?php else: ?>
                <a class="nav-link" href="<?= $pages['login'] ?>">Увійти</a>
                <a class="btn btn-custom btn-sm" href="<?= $pages['reg'] ?>">Реєстрація</a>
            <?php endif; ?> 
        </div>
    </div>
  </div>
</nav>

<!-- Підключення JS для роботи бургера (Bootstrap bundle) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function toggleTheme() { /* Ваша функція зміни теми */ }
</script>
</body>
</html>