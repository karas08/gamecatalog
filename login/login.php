<?php
require_once "include/config.php";
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password']; // Пароль не екрануємо, бо будемо перевіряти через password_verify

    $res = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    $user = mysqli_fetch_assoc($res);

    // Перевірка пароля (припускаємо, що ти зберігав пароль через password_hash)
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['username'];
        $_SESSION['is_admin'] = $user['is_admin']; // Якщо є таке поле
        header("Location: index.php");
        exit();
    } else {
        $error = "Невірний логін або пароль!";
    }
}
?>

<form method="POST">
    <input type="email" name="email" required placeholder="Email">
    <input type="password" name="password" required placeholder="Пароль">
    <button type="submit">Увійти</button>
</form>