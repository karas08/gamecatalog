<?php
session_start();
require_once "include/config.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['avatar']) && isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $file = $_FILES['avatar'];
    
    // Створюємо папку, якщо її немає
    if (!is_dir('img/avatars')) { mkdir('img/avatars', 0777, true); }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = "avatar_" . $uid . "_" . time() . "." . $ext;
    
    if (move_uploaded_file($file['tmp_name'], "img/avatars/" . $filename)) {
        mysqli_query($conn, "UPDATE users SET avatar = '$filename' WHERE id = $uid");
    }
}
header("Location: profile.php");