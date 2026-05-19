<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . "/config.php";

$session_id = session_id();
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : "NULL";

// Записуємо або оновлюємо активність поточного користувача
$uid_value = ($user_id !== "NULL") ? $user_id : "NULL";
mysqli_query($conn, "INSERT INTO online_users (session_id, user_id, last_activity) 
                    VALUES ('$session_id', $uid_value, CURRENT_TIMESTAMP) 
                    ON DUPLICATE KEY UPDATE user_id = $uid_value, last_activity = CURRENT_TIMESTAMP");

// Видаляємо користувачів, які були неактивні понад 5 хвилин (300 секунд)
mysqli_query($conn, "DELETE FROM online_users WHERE last_activity < NOW() - INTERVAL 5 MINUTE");

// Рахуємо, скільки людей залишилось в таблиці
$count_res = mysqli_query($conn, "SELECT COUNT(*) as online_count FROM online_users");
$count_data = mysqli_fetch_assoc($count_res);
$users_online = $count_data['online_count'] ?? 1;
?>