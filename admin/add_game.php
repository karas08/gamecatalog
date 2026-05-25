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
ob_start(); 
require_once "../include/config.php";
include_once "../include/header.php";

if (!isset($_SESSION['user_id']) || (!isset($_SESSION['is_admin']) && !isset($_SESSION['role']))) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $developer = mysqli_real_escape_string($conn, trim($_POST['developer']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $genre = mysqli_real_escape_string($conn, trim($_POST['genre']));
    $release_year = (int)$_POST['release_year'];
    $age_rating = (int)$_POST['age_rating'];
    $steam_link = mysqli_real_escape_string($conn, trim($_POST['steam_link']));
    $trailer_url = mysqli_real_escape_string($conn, trim($_POST['trailer_url']));
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    $image = "default.jpg";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $new_name = "game_" . time() . "." . $file_ext;
        if (move_uploaded_file($_FILES['image']['tmp_name'], "../img/" . $new_name)) {
            $image = $new_name;
        }
    }

    $query = "INSERT INTO games (title, developer, description, genre, image, is_featured, trailer_url, release_year, age_rating, steam_link) 
              VALUES ('$title', '$developer', '$description', '$genre', '$image', $is_featured, '$trailer_url', $release_year, $age_rating, '$steam_link')";

    if (mysqli_query($conn, $query)) {
        header("Location: index.php?status=added");
        exit();
    } else {
        $error = "Помилка: " . mysqli_error($conn);
    }
}
?>

<div class="container mt-5 text-white">
    <div class="glass-card p-4 mx-auto" style="max-width: 600px;">
        <h2 class="text-info mb-4">➕ Додати нову гру</h2>
        <?php if(isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        <form action="add_game.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3"><label class="form-label">Назва гри</label><input type="text" name="title" class="form-control bg-dark text-white border-secondary" required></div>
            <div class="mb-3"><label class="form-label">Розробник</label><input type="text" name="developer" class="form-control bg-dark text-white border-secondary" required></div>
            <div class="mb-3 form-check form-switch p-3 rounded" style="background: rgba(13, 202, 240, 0.1);"><input class="form-check-input ms-0 me-2" type="checkbox" name="is_featured" id="feat"><label class="form-check-label fw-bold text-info" for="feat">🔥 Додати в слайдер</label></div>
            <div class="mb-3"><label class="form-label">Жанр</label><input type="text" name="genre" class="form-control bg-dark text-white border-secondary" required></div>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Рік випуску</label><input type="number" name="release_year" class="form-control bg-dark text-white border-secondary" required></div>
                <div class="col-md-6 mb-3"><label class="form-label">Вік (+)</label><input type="number" name="age_rating" class="form-control bg-dark text-white border-secondary" required></div>
            </div>
            <div class="mb-3"><label class="form-label">Посилання на Steam</label><input type="url" name="steam_link" class="form-control bg-dark text-white border-secondary"></div>
            <div class="mb-3"><label class="form-label">Посилання на трейлер</label><input type="url" name="trailer_url" class="form-control bg-dark text-white border-secondary"></div>
            <div class="mb-3"><label class="form-label">Опис гри</label><textarea name="description" rows="4" class="form-control bg-dark text-white border-secondary" required></textarea></div>
            <div class="mb-3"><label class="form-label">Обкладинка</label><input type="file" name="image" accept="image/*" class="form-control bg-dark text-white border-secondary"></div>
            <button type="submit" class="btn btn-info w-100 fw-bold">Зберегти гру</button>
        </form>
    </div>
</div>
<?php include_once "../include/footer.php"; ?>