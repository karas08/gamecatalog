<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "game_db"; // Назва твоєї бази

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Помилка підключення: " . mysqli_connect_error());
}
?>