<?php 
ob_start(); // Це виправить помилку з заголовками на всіх сторінках
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// ФІЧА 4: Підключаємо трекер онлайну (завжди на самому початку скрипта)
include_once "track_online.php";

// Автоматичне визначення кореня сайту
$root = "/gamekatalog/"; 

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
        /* Базові змінні для швидкої зміни теми на JS */
        :root {
            --site-bg: linear-gradient(to right, #24243e, #302b63, #0f0c29);
            --site-text: #ffffff;
            --card-glass: rgba(255, 255, 255, 0.05);
            --card-border: rgba(255, 255, 255, 0.1);
        }
        
        body { 
            background: var(--site-bg); 
            color: var(--site-text); 
            font-family: 'Inter', sans-serif; 
            min-height: 100vh; 
            transition: background 0.3s ease, color 0.3s ease;
        }
        .navbar { 
            background: rgba(0, 0, 0, 0.5) !important; 
            backdrop-filter: blur(15px); 
            border-bottom: 1px solid rgba(255,255,255,0.1); 
        }
        .logo { 
            font-family: 'Orbitron', sans-serif; 
            color: #00d2ff !important; 
            text-transform: uppercase; 
            letter-spacing: 2px; 
            text-decoration: none; 
            font-size: 1.5rem; 
            font-weight: bold; 
        }
        .glass-card { 
            background: var(--card-glass) !important; 
            backdrop-filter: blur(15px); 
            border: 1px solid var(--card-border) !important; 
            border-radius: 20px; 
            transition: background 0.3s ease;
        }
        .btn-custom { 
            background: #00d2ff; 
            color: #fff; 
            border-radius: 10px; 
            border: none; 
            font-weight: bold; 
            padding: 6px 18px; 
            text-decoration: none; 
        }
        .btn-custom:hover { 
            background: #00b8e6; 
            color: white; 
        }
        
        /* Стилі для елементів випадаючого списку Живого Пошуку */
        .live-search-item {
            transition: background 0.2s ease;
        }
        .live-search-item:hover {
            background: rgba(0, 210, 255, 0.15) !important;
        }
        
        /* Анімація пульсації для рулетки */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        .animate-pulse {
            animation: pulse 2s infinite ease-in-out;
        }
    </style>
    <script>
        // Ранній запуск перевірки теми, щоб уникнути білого блимання екрану
        (function() {
            const savedTheme = localStorage.getItem('site-theme') || 'dark';
            if (savedTheme === 'light') {
                document.documentElement.style.setProperty('--site-bg', '#fafafa');
                document.documentElement.style.setProperty('--site-text', '#121212');
                document.documentElement.style.setProperty('--card-glass', 'rgba(0, 0, 0, 0.03)');
                document.documentElement.style.setProperty('--card-border', 'rgba(0, 0, 0, 0.08)');
            }
        })();
    </script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top mb-4">
  <div class="container">
    <a class="logo" href="<?= $pages['home'] ?>">🐱 GAMECAT</a>
    
    <form action="<?= $pages['home'] ?>" method="GET" class="d-flex ms-lg-4 my-2 my-lg-0 flex-grow-1 position-relative" style="max-width: 350px;">
        <div class="input-group input-group-sm">
            <input type="text" name="search" id="searchInput" class="form-control bg-dark text-white border-secondary" 
                   placeholder="Пошук ігор за назвою..." 
                   autocomplete="off"
                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <button class="btn btn-outline-info" type="submit">
                <i class="fas fa-search"></i>
            </button>
        </div>
        
        <div id="liveSearchResults" class="position-absolute w-100 rounded shadow-lg d-none" 
             style="top: 38px; left: 0; z-index: 9999; border: 1px solid rgba(255,255,255,0.1); max-height: 320px; overflow-y: auto;">
        </div>
    </form>
    
    <div class="navbar-nav ms-auto align-items-center gap-2">
        <a class="nav-link text-warning fw-bold animate-pulse me-2" href="<?= $pages['random'] ?>" title="Вибрати випадкову гру">
            🎲 Мені пощастить
        </a>

        <button id="themeToggleBtn" class="btn btn-sm btn-outline-secondary border-0 text-white fs-5 p-1 me-2" type="button" onclick="toggleTheme()">
            🌙
        </button>

        <?php if(isset($_SESSION['username'])): ?>
            <a class="btn btn-outline-info btn-sm" href="<?= $pages['profile'] ?>">
                <i class="fas fa-user"></i> Профіль
            </a>
            
            <?php if((isset($_SESSION['role']) && $_SESSION['role'] === 'admin') || (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1)): ?>
                <a class="btn btn-outline-warning btn-sm" href="<?= $pages['admin'] ?>">
                    <i class="fas fa-tools"></i> Admin
                </a>
            <?php endif; ?>
            
            <a class="btn btn-outline-danger btn-sm" href="<?= $pages['logout'] ?>">
                <i class="fas fa-sign-out-alt"></i>
            </a>
            
        <?php else: ?>
            <a class="nav-link" href="<?= $pages['login'] ?>">Увійти</a>
            <a class="btn btn-custom btn-sm" href="<?= $pages['reg'] ?>">Реєстрація</a>
        <?php endif; ?> 
    </div>
  </div>
</nav>