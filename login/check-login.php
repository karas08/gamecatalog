<?php
session_start();
include_once "../include/config.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$user'";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $userData = mysqli_fetch_assoc($result);

        if (password_verify($pass, $userData['password'])) {
            session_start();
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['username'] = $userData['username'];
            // Якщо в базі роль порожня, ставимо 'user' за замовчуванням
            $_SESSION['role'] = !empty($userData['role']) ? $userData['role'] : 'user'; 

            header("Location: ../index.php");
            exit();
        }else {
            die("Пароль невірний! <a href='index.php'>Назад</a>");
        }
    } else {
        die("Користувача не знайдено! <a href='index.php'>Назад</a>");
    }
}