<?php
// Підключаємо конфігурацію бази даних для роботи з $conn
require_once "include/config.php";

// Запит до БД: вибираємо ID однієї випадкової гри
$query = "SELECT id FROM games ORDER BY RAND() LIMIT 1";
$res = mysqli_query($conn, $query);

// Перевіряємо, чи в базі взагалі є хоча б одна гра
if ($res && mysqli_num_rows($res) > 0) {
    $game = mysqli_fetch_assoc($res);
    // Якщо гру знайдено, перекидаємо користувача на її сторінку
    header("Location: post.php?id=" . $game['id']);
    exit();
} else {
    // Якщо база порожня, повертаємо на головну сторінку
    header("Location: index.php");
    exit();
}
?>