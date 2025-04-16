<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../scripts/db.php';

// Проверяем, была ли нажата кнопка добавления
if (!isset($_POST['add-to-cart'])) {
    $_SESSION['error'] = 'Форма не отправлена';
    header('Location: ' . $_SERVER['HTTP_REFERER'] ?? '../pages/products.php');
    exit();
}

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    $_SESSION['return_url'] = '../pages/set_of_products.php'; // Перенаправить в корзину после входа
    header('Location: ../pages/login.php');
    exit();
}

// Обработка данных
$product_id = (int)($_POST['product_id'] ?? 0);
$amount = (int)($_POST['amount'] ?? 1);

if ($product_id <= 0 || $amount <= 0) {
    $_SESSION['error'] = 'Некорректные данные товара';
    header('Location: ' . $_SERVER['HTTP_REFERER'] ?? '../pages/products.php');
    exit();
}

try {
    // Проверка существования товара
    $stmt = $pdo->prepare("SELECT id FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    
    if (!$stmt->fetch()) {
        $_SESSION['error'] = 'Товар не найден';
        header('Location: ' . $_SERVER['HTTP_REFERER'] ?? '../pages/products.php');
        exit();
    }

    // Добавление в корзину
    $stmt = $pdo->prepare("
        INSERT INTO cart (user_id, product_id, amount) 
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE amount = amount + ?
    ");
    $stmt->execute([$_SESSION['user_id'], $product_id, $amount, $amount]);

    // Успешное перенаправление в корзину
    header('Location: ../pages/set_of_products.php');
    exit();

} catch (PDOException $e) {
    $_SESSION['error'] = 'Ошибка: ' . $e->getMessage();
    header('Location: ' . $_SERVER['HTTP_REFERER'] ?? '../pages/products.php');
    exit();
}