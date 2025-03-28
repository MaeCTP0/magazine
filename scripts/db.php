<?php
$host = 'MySQL-5.5'; // Имя хоста
$dbname = 'emagazine'; // Имя базы данных
$username = 'root'; // Имя пользователя базы данных
$password = ''; // Пароль пользователя базы данных

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}
?>