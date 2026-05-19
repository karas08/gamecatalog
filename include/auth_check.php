<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Якщо в сесії немає user_id, відправляємо на сторінку логіну
if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit();
}
?>