<?php
session_start();
require_once "include/config.php";

if (!isset($_SESSION['user_id']) || !isset($_GET['game_id'])) {
    header("Location: login/index.php");
    exit();
}

$uid = $_SESSION['user_id'];
$gid = (int)$_GET['game_id'];

$check = mysqli_query($conn, "SELECT id FROM favorites WHERE user_id = $uid AND game_id = $gid");

if (mysqli_num_rows($check) > 0) {
    mysqli_query($conn, "DELETE FROM favorites WHERE user_id = $uid AND game_id = $gid");
    $status = "fav_removed";
} else {
    mysqli_query($conn, "INSERT INTO favorites (user_id, game_id) VALUES ($uid, $gid)");
    $status = "fav_added";
}

// Повертаємо на сторінку гри з параметром статусу
header("Location: post.php?id=" . $gid . "&status=" . $status);
exit();