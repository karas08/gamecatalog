<?php
$servername = "sql101.infinityfree.com";
$username = "if0_41972489";
$password = "rJ2BRmNfBv";
$dbname = "if0_41972489_game_db";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Помилка підключення: " . mysqli_connect_error());
}
?>