<?php
include_once "../include/config.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    // Хешуємо пароль для безпеки (важливо!)
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, password, role) VALUES ('$user', '$pass', 'user')";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?success=registered");
    } else {
        echo "Помилка реєстрації: " . mysqli_error($conn);
    }
}
?>