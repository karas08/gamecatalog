<?php
require_once '../include/config.php';
require_once "include/auth_check.php";

$db_connection = null;
if (isset($link)) { $db_connection = $link; }
elseif (isset($connect)) { $db_connection = $connect; }
elseif (isset($conn)) { $db_connection = $conn; }
elseif (isset($db)) { $db_connection = $db; }

if (!$db_connection) {
    die("Помилка: Не знайдено змінну підключення до бази даних.");
}

$table_name = "games";
$check_table = mysqli_query($db_connection, "SHOW TABLES LIKE 'games'");
if (mysqli_num_rows($check_table) == 0) {
    $check_table_alt = mysqli_query($db_connection, "SHOW TABLES LIKE 'game'");
    if (mysqli_num_rows($check_table_alt) > 0) { $table_name = "game"; }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($db_connection, trim($_POST['title']));
    $genre = mysqli_real_escape_string($db_connection, trim($_POST['genre']));
    $release_year = isset($_POST['release_year']) ? (int)$_POST['release_year'] : date('Y');
    $age_rating = isset($_POST['age_rating']) ? (int)$_POST['age_rating'] : 0;
    $steam_link = isset($_POST['steam_link']) ? mysqli_real_escape_string($db_connection, trim($_POST['steam_link'])) : '';
    $trailer_url = isset($_POST['trailer_url']) ? mysqli_real_escape_string($db_connection, trim($_POST['trailer_url'])) : '';
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $description = mysqli_real_escape_string($db_connection, trim($_POST['description']));

    if (empty($title)) {
        die("Назва гри обов'язкова!");
    }

    $image_filename = "default.jpg";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = $_FILES['image']['name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $image_filename = "game_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $file_ext;
        
        if (!move_uploaded_file($file_tmp, "../img/" . $image_filename)) {
            die("Помилка при завантаженні файлу зображення.");
        }
    }

    $query = "INSERT INTO $table_name (title, genre, description, image, is_featured, trailer_url, release_year, age_rating, steam_link) 
              VALUES ('$title', '$genre', '$description', '$image_filename', $is_featured, '$trailer_url', $release_year, $age_rating, '$steam_link')";

    if (mysqli_query($db_connection, $query)) {
        header("Location: index.php?status=added");
        exit();
    } else {
        die("Помилка додавання гри в базу: " . mysqli_error($db_connection));
    }
} else {
    header("Location: index.php");
    exit();
}
?>