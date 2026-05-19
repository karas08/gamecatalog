<?php
// 1. Підключаємо налаштування бази даних
require_once '../include/config.php';
require_once "include/auth_check.php";

// 2. Автоматично шукаємо змінну підключення
$db_connection = null;
if (isset($link)) { $db_connection = $link; }
elseif (isset($connect)) { $db_connection = $connect; }
elseif (isset($conn)) { $db_connection = $conn; }
elseif (isset($db)) { $db_connection = $db; }

if (!$db_connection) {
    die("Помилка: Не вдалося знайти змінну підключення до БД.");
}

// 3. Автовизначення назви таблиці
$table_name = "games";
$check_table = mysqli_query($db_connection, "SHOW TABLES LIKE 'games'");
if (mysqli_num_rows($check_table) == 0) {
    $check_table_alt = mysqli_query($db_connection, "SHOW TABLES LIKE 'game'");
    if (mysqli_num_rows($check_table_alt) > 0) {
        $table_name = "game";
    }
}

// 4. Перевіряємо, чи передано ID гри і чи воно є числом
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    
    $game_id = intval($_GET['id']);

    // КРОК 1: Видаляємо фізичний файл картинки з сервера, щоб не забивати пам'ять
    $select_query = "SELECT image FROM $table_name WHERE id = $game_id LIMIT 1";
    $select_result = mysqli_query($db_connection, $select_query);
    
    if ($select_result && mysqli_num_rows($select_result) > 0) {
        $game_data = mysqli_fetch_assoc($select_result);
        $image_name = $game_data['image'];
        
        // З папки admin/ виходимо на один рівень назад в img/
        if (!empty($image_name) && file_exists("../img/" . $image_name)) {
            unlink("../img/" . $image_name);
        }
    }

    // КРОК 2: Видаляємо сам запис із таблиці бази даних
    $delete_query = "DELETE FROM $table_name WHERE id = $game_id";

    if (mysqli_query($db_connection, $delete_query)) {
        // Повертаємося на головну сторінку адмінки з прапорцем успіху
        header("Location: index.php?status=deleted");
        exit();
    } else {
        die("Помилка при видаленні запису з бази даних: " . mysqli_error($db_connection));
    }

} else {
    // Якщо ID не передано, просто скидаємо в адмінку
    header("Location: index.php");
    exit();
}
?>