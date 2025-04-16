<?php
session_start();
require_once '../scripts/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Неверный метод запроса');
}

if (!isset($_SESSION['user_id'])) {
    die('Необходима авторизация');
}

$cart_id = (int)$_POST['cart_id'];

try {
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$cart_id, $_SESSION['user_id']]);
    header('Location: ../pages/set_of_products.php');
    exit();
} catch (PDOException $e) {
    die("Ошибка удаления: " . $e->getMessage());
}