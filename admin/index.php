<?php
// 1. Підключаємо конфігурацію бази даних
require_once '../include/config.php';
// 2. Автоматично шукаємо змінну підключення, яка створена у твоєму config.php
$db_connection = null;
if (isset($link)) { $db_connection = $link; }
elseif (isset($connect)) { $db_connection = $connect; }
elseif (isset($conn)) { $db_connection = $conn; }
elseif (isset($db)) { $db_connection = $db; }

if (!$db_connection) {
    die("<div style='color:red; font-family:Arial; padding:20px;'>
            <h3>Помилка конфігурації!</h3>
            <p>Не вдалося знайти змінну підключення до БД. Перевірте, як вона названа у файлі include/config.php</p>
         </div>");
}

// 3. Автовизначення назви таблиці ігор (перевіряємо 'games' або 'game')
$table_name = "games";
$check_table = mysqli_query($db_connection, "SHOW TABLES LIKE 'games'");
if (mysqli_num_rows($check_table) == 0) {
    $check_table_alt = mysqli_query($db_connection, "SHOW TABLES LIKE 'game'");
    if (mysqli_num_rows($check_table_alt) > 0) {
        $table_name = "game";
    }
}

// 4. Отримуємо всі ігри з бази даних
$query = "SELECT * FROM $table_name ORDER BY id DESC";
$result = mysqli_query($db_connection, $query);

if (!$result) {
    die("<div style='color:red; font-family:Arial; padding:20px;'>
            <h3>Помилка в SQL-запиті!</h3>
            <p><strong>Текст помилки:</strong> " . mysqli_error($db_connection) . "</p>
         </div>");
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Панель адміністратора - Каталог ігор</title>
</head>
<body style="background-color: #f4f6f9; font-family: Arial, sans-serif; margin: 0; padding: 20px;">
    <div style="max-width: 1000px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h1 style="color: #333; margin-top: 0;">Управління каталогом ігор</h1>
        <p><a href="add_game.php" style="text-decoration: none; background: #28a745; color: white; padding: 10px 15px; border-radius: 4px; font-weight: bold; display: inline-block; margin-bottom: 20px;">➕ Додати нову гру</a></p>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 12px; margin-bottom: 20px; border-radius: 4px; border: 1px solid #c3e6cb;">
                🎮 Гру успішно видалено з каталогу та її файл зображення стерто!
            </div>
        <?php endif; ?>

        <table border="0" cellpadding="12" cellspacing="0" style="width: 100%; border-collapse: collapse; background: white; border-radius: 6px; overflow: hidden;">
            <thead>
                <tr style="background-color: #343a40; color: white; text-align: left;">
                    <th style="padding: 12px;">ID</th>
                    <th>Зображення</th>
                    <th>Назва гри</th>
                    <th style="text-align: center;">Дії</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (mysqli_num_rows($result) > 0) {
                    while ($game = mysqli_fetch_assoc($result)) {
                        ?>
                        <tr style="border-bottom: 1px solid #e9ecef;">
                            <td style="padding: 12px; color: #666;"><?php echo $game['id']; ?></td>
                            <td>
                                <?php if (!empty($game['image'])): ?>
                                    <img src="../img/<?php echo htmlspecialchars($game['image']); ?>" alt="Poster" width="60" style="border-radius: 4px; display: block;">
                                <?php else: ?>
                                    <span style="color: #999; font-size: 13px;">Немає</span>
                                <?php endif; ?>
                            </td>
                            <td><strong style="color: #495057; font-size: 16px;"><?php echo htmlspecialchars($game['title']); ?></strong></td>
                            <td style="text-align: center;">
                                <a href="edit_game.php?id=<?php echo $game['id']; ?>" style="color: #007bff; text-decoration: none; margin-right: 15px; font-weight: bold;">✏️ Редагувати</a>
                                
                                <a href="delete_game.php?id=<?php echo $game['id']; ?>" 
                                   onclick="return confirm('Ви впевнені, що хочете остаточно видалити гру «<?php echo htmlspecialchars($game['title'], ENT_QUOTES); ?>»?');" 
                                   style="color: #dc3545; text-decoration: none; font-weight: bold;">❌ Видалити</a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='4' style='text-align:center; padding: 30px; color: #999;'>Ігор у каталозі поки немає.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <br><br>
        <p><a href="../index.php" style="color: #007bff; text-decoration: none;">🏠 На головну сторінку сайту</a></p>
    </div>
</body>
</html>