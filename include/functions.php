<?php
include_once "config.php";

// Функція для отримання всіх ігор
function get_all_games() {
    global $conn;
    $sql = "SELECT * FROM games ORDER BY id DESC";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Функція для отримання однієї гри за ID
function get_game_by_id($id) {
    global $conn;
    $id = mysqli_real_escape_string($conn, $id);
    $sql = "SELECT * FROM games WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result);
}
?>