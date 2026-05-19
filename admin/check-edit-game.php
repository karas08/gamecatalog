<?php
require_once '../include/config.php';

$db_connection = null;
if (isset($link)) { $db_connection = $link; }
elseif (isset($connect)) { $db_connection = $connect; }
elseif (isset($conn)) { $db_connection = $conn; }
elseif (isset($db)) { $db_connection = $db; }

$table_name = "games";
$check_table = mysqli_query($db_connection, "SHOW TABLES LIKE 'games'");
if (mysqli_num_rows($check_table) == 0) {
    $check_table_alt = mysqli_query($db_connection, "SHOW TABLES LIKE 'game'");
    if (mysqli_num_rows($check_table_alt) > 0) {
        $table_name = "game";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $game_id = intval($_POST['id']);
    
    // Приймаємо та очищаємо нові текстові поля під твою БД
    $title = mysqli_real_escape_string($db_connection, trim($_POST['title']));
    $genre = mysqli_real_escape_string($db_connection, trim($_POST['genre']));
    $year = !empty($_POST['year']) ? intval($_POST['year']) : "NULL";
    $link_url = mysqli_real_escape_string($db_connection, trim($_POST['link']));
    $description = mysqli_real_escape_string($db_connection, trim($_POST['description']));

    if (empty($title)) {
        die("Назва гри є обов'язковою для заповнення!");
    }

    // Дізнаємося поточну картинку на випадок перезапису
    $select_query = "SELECT image FROM $table_name WHERE id = $game_id LIMIT 1";
    $select_result = mysqli_query($db_connection, $select_query);
    $game_data = mysqli_fetch_assoc($select_result);
    $current_image = $game_data['image'];

    $image_filename = $current_image;

    // Обробка завантаження картинки
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = $_FILES['image']['name'];
        
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $new_image_name = time() . "_" . bin2hex(random_bytes(8)) . "." . $file_ext;
        $upload_dir = "../img/";

        if (move_uploaded_file($file_tmp, $upload_dir . $new_image_name)) {
            $image_filename = $new_image_name;

            if (!empty($current_image) && file_exists($upload_dir . $current_image)) {
                unlink($upload_dir . $current_image);
            }
        }
    }

    // Формуємо гнучкий SQL-запит на оновлення всіх полів
    // Враховуємо, чи є рік числом чи NULL
    $year_value = ($year === "NULL") ? "NULL" : "'$year'";
    
    $update_query = "UPDATE $table_name SET 
                        title = '$title', 
                        genre = '$genre', 
                        year = $year_value, 
                        link = '$link_url', 
                        description = '$description', 
                        image = '$image_filename' 
                     WHERE id = $game_id";

    if (mysqli_query($db_connection, $update_query)) {
        header("Location: index.php?status=updated");
        exit();
    } else {
        die("Помилка оновлення даних в БД: " . mysqli_error($db_connection));
    }
} else {
    header("Location: index.php");
    exit();
}
?>