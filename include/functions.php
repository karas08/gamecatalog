<?php
include_once "config.php";


function e($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Функція для отримання всіх ігор
function get_all_games() {
    global $conn;
    $sql = "SELECT * FROM games ORDER BY id DESC";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Функція для отримання однієї гри за ID
$id = $_GET['id'];
$query = "SELECT * FROM games WHERE id = $id";
$result = mysqli_query($conn, $query);
$game = mysqli_fetch_assoc($result);