<?php
session_start();
require_once "include/config.php";

if (isset($_POST['rate']) && isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $gid = (int)$_POST['game_id'];
    $val = (int)$_POST['rate'];

    // Твій елегантний запит
    $res = mysqli_query($conn, "INSERT INTO ratings (user_id, game_id, rating) VALUES ($uid, $gid, $val) 
                                ON DUPLICATE KEY UPDATE rating = $val");
    
    if ($res) {
        // Передаємо статус успішної оцінки
        header("Location: post.php?id=" . $gid . "&status=rate_success");
        exit();
    }
}
header("Location: post.php?id=" . ($_POST['game_id'] ?? ''));