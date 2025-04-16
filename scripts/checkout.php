<?php
session_start();
require_once '../scripts/db.php';

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'Требуется авторизация']));
}

$user_id = $_SESSION['user_id'];

try {
    $pdo->beginTransaction();
    
    // 1. Создаем запись о заказе
    $order_stmt = $pdo->prepare("
        INSERT INTO `order` (user_id, made_date, status)
        VALUES (?, CURDATE(), 1)
    ");
    $order_stmt->execute([$user_id]);
    $order_id = $pdo->lastInsertId();
    
    // 2. Переносим товары из корзины в order_items (используем amount)
    $move_items = $pdo->prepare("
        INSERT INTO order_items (order_id, product_id, amount)
        SELECT ?, product_id, amount 
        FROM cart 
        WHERE user_id = ?
    ");
    $move_items->execute([$order_id, $user_id]);
    
    // 3. Очищаем корзину
    $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$user_id]);
    
    // 4. Рассчитываем сумму на лету (без сохранения в БД)
    $total = $pdo->prepare("
        SELECT SUM(oi.amount * p.price)
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ")->fetchColumn();
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'total' => number_format($total, 0, '', ' ')
    ]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['error' => $e->getMessage()]);
}