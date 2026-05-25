<?php
    if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

// Перевіряємо: якщо користувач НЕ увійшов АБО його роль НЕ 'admin'
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Скидаємо його на головну сторінку сайту
    header("Location: ../index.php");
    exit(); // Зупиняємо виконання скрипту
}
require_once "../include/config.php";

// 1. ПЕРЕВІРКА СЕСІЇ
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['user_id']) || (!isset($_SESSION['is_admin']) && !isset($_SESSION['role']))) {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Не вказано ID гри!");
}
$id = (int)$_GET['id'];

// Витягуємо дані
$res = mysqli_query($conn, "SELECT * FROM games WHERE id = $id");
$game = mysqli_fetch_assoc($res);

if (!$game) { die("Гру не знайдено!"); }

// 2. ОБРОБКА ФОРМИ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $developer = mysqli_real_escape_string($conn, trim($_POST['developer'])); // Отримуємо автора
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $genre = mysqli_real_escape_string($conn, trim($_POST['genre']));
    $release_year = (int)$_POST['release_year'];
    $age_rating = (int)$_POST['age_rating'];
    $steam_link = mysqli_real_escape_string($conn, trim($_POST['steam_link']));
    $trailer_url = mysqli_real_escape_string($conn, trim($_POST['trailer_url']));
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    $image = $game['image']; 
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $new_name = "game_" . time() . "." . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        if (move_uploaded_file($_FILES['image']['tmp_name'], "../img/" . $new_name)) {
            $image = $new_name; 
        }
    }

    // Оновлений SQL запит з полем developer
    $query = "UPDATE games SET 
                title = '$title', 
                developer = '$developer', 
                description = '$description', 
                genre = '$genre', 
                image = '$image', 
                is_featured = $is_featured, 
                trailer_url = '$trailer_url', 
                release_year = $release_year, 
                age_rating = $age_rating, 
                steam_link = '$steam_link' 
              WHERE id = $id";

    if (mysqli_query($conn, $query)) {
        header("Location: index.php?status=updated");
        exit();
    } else {
        $error = "Помилка оновлення: " . mysqli_error($conn);
    }
}

include_once "../include/header.php";
?>

<div class="container mt-5 text-white">
    <div class="glass-card p-4 mx-auto" style="max-width: 600px;">
        <h2 class="text-warning mb-4">📝 Редагувати: <?= htmlspecialchars($game['title']) ?></h2>
        
        <form action="edit_game.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Назва гри</label>
                <input type="text" name="title" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($game['title']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Розробник (Автор)</label>
                <input type="text" name="developer" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($game['developer'] ?? '') ?>" required>
            </div>

            <div class="mb-3 form-check form-switch p-3 rounded" style="background: rgba(255, 193, 7, 0.1);">
                <input class="form-check-input ms-0 me-2" type="checkbox" name="is_featured" id="flexSwitchFeatured" <?= $game['is_featured'] == 1 ? 'checked' : '' ?>>
                <label class="form-check-label fw-bold text-warning" for="flexSwitchFeatured">🔥 Додати в головний слайдер</label>
            </div>

            <div class="mb-3">
                <label class="form-label">Жанр</label>
                <input type="text" name="genre" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($game['genre'] ?? '') ?>" required>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Рік випуску</label>
                    <input type="number" name="release_year" class="form-control bg-dark text-white border-secondary" value="<?= (int)$game['release_year'] ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Вікове обмеження (+)</label>
                    <input type="number" name="age_rating" class="form-control bg-dark text-white border-secondary" value="<?= (int)$game['age_rating'] ?>" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Посилання на Steam</label>
                <input type="url" name="steam_link" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($game['steam_link'] ?? '') ?>">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Посилання на трейлер YouTube</label>
                <input type="url" name="trailer_url" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($game['trailer_url'] ?? '') ?>">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Опис гри</label>
                <textarea name="description" rows="4" class="form-control bg-dark text-white border-secondary" required><?= htmlspecialchars($game['description'] ?? '') ?></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Завантажити нову обкладинку</label>
                <input type="file" name="image" accept="image/*" class="form-control bg-dark text-white border-secondary">
            </div>
            
            <div class="d-flex gap-2">
                <a href="index.php" class="btn btn-outline-secondary w-50 fw-bold text-white">Скасувати</a>
                <button type="submit" class="btn btn-warning w-50 fw-bold text-dark">Зберегти зміни</button>
            </div>
        </form>
    </div>
</div>

<?php include_once "../include/footer.php"; ?>