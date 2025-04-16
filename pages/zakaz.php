<?php
session_start();
require_once '../scripts/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

try {
   // Получаем заказы с расчетом суммы на лету
$orders_stmt = $pdo->prepare("
SELECT o.id, o.made_date, o.status,
       (
           SELECT SUM(oi.amount * p.price)
           FROM order_items oi
           JOIN products p ON oi.product_id = p.id
           WHERE oi.order_id = o.id
       ) as total
FROM `order` o
WHERE o.user_id = ?
ORDER BY o.made_date DESC
");
$orders_stmt->execute([$user_id]);
$orders = $orders_stmt->fetchAll(PDO::FETCH_ASSOC);

// Получаем товары для каждого заказа
foreach ($orders as &$order) {
$items_stmt = $pdo->prepare("
    SELECT oi.product_id, oi.amount, 
           p.title, p.image, p.price
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$items_stmt->execute([$order['id']]);
$order['items'] = $items_stmt->fetchAll(PDO::FETCH_ASSOC);

// Пересчитываем total на случай, если подзапрос не сработал
$order['total'] = array_sum(array_map(
    fn($item) => $item['price'] * $item['amount'],
    $order['items']
));
}
unset($order);
}
catch (PDOException $e) {
    die("Ошибка загрузки заказов: " . $e->getMessage());
}

function getStatusText($status) {
    switch ($status) {
        case 1: return 'В обработке';
        case 2: return 'Выполнен';
        case 3: return 'Отменён';
        default: return 'Неизвестно';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои заказы | BystroKorzin</title>
    <link rel="stylesheet" href="../css/stylezak.css">
</head>
<body>
    <!-- Шапка -->
    <header>
    <div class="header-container">
            <div class="logo-nav-wrapper">
                <div class="logo">
                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none">
                        <path d="M20 5L10 20H25L15 35" stroke="#6C5B7B" stroke-width="3"/>
                        <circle cx="28" cy="28" r="6" fill="#81C784" stroke="#6C5B7B"/>
                    </svg>
                </div>
        <nav>
            <ul class="nav-menu">
                <li><a href="index.php">Главная</a></li>
                <li><a href="products.php">Товары</a></li>               
                <li><a href="set_of_products.php">Корзина</a></li>
                <li><a href="account.php">Личный кабинет</a></li>
                <li><a href="#contacts">Контакты</a></li>
            </ul>
        </nav>
</div>
</div>
    </header>

    <!-- Основной контент -->
    <main class="orders-container">
        <h1>Мои заказы</h1>
        
        <!-- Фильтры -->
        <div class="orders-filters">
            <select id="order-filter">
                <option value="all">Все заказы</option>
                <option value="month">За последний месяц</option>
                <option value="year">За последний год</option>
            </select>
        </div>

        <!-- Список заказов -->
        <div class="orders-list">
            <?php if (empty($orders)): ?>
                <p class="empty-cart-message">У вас пока нет заказов</p>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <span class="order-number">Заказ #<?= $order['id'] ?></span>
                            <span class="order-date"><?= date('d.m.Y', strtotime($order['made_date'])) ?></span>
                            <span class="order-status status-<?= $order['status'] ?>">
                                <?= getStatusText($order['status']) ?>
                            </span>
                            <span class="order-total"><?= number_format($order['total'], 0, '', ' ') ?> ₽</span>
                        </div>
                        
                        <div class="order-products">
                        <?php foreach ($order['items'] as $item): ?>
                            <div class="product-item">
                                <img src="/<?= ($item['image']) ?>" 
                                    alt="<?= htmlspecialchars($item['title']) ?>">
                                <div class="product-info">
                                    <h3><?= htmlspecialchars($item['title']) ?></h3>
                                    <p><?= $item['amount'] ?> × <?= number_format($item['price'], 0, '', ' ') ?> ₽</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                        
                        <div class="order-actions">
                            <button class="repeat-order" data-order-id="<?= $order['id'] ?>">Повторить заказ</button>
                            <button class="order-details">Подробнее</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>


    <!-- Подвал с контактами -->
    <footer id="contacts" class="footer-container">
        <h2>Контакты</h2>
        <div class="footer-content">
            <p>Телефон: +7 (999) 123-45-67</p>
            <p>E-mail: info@emagazine.ru</p>
            <p>Адрес: г. Москва, ул. Примерная, д. 123</p>
        </div>
    </footer>
    <script>
    // Фильтрация заказов
    document.getElementById('order-filter').addEventListener('change', function() {
        const filter = this.value;
        const now = new Date();
        const orders = document.querySelectorAll('.order-card');
        
        orders.forEach(order => {
            const orderDate = new Date(order.dataset.orderDate);
            let show = true;
            
            if (filter === 'month') {
                const monthAgo = new Date();
                monthAgo.setMonth(monthAgo.getMonth() - 1);
                show = orderDate >= monthAgo;
            } else if (filter === 'year') {
                const yearAgo = new Date();
                yearAgo.setFullYear(yearAgo.getFullYear() - 1);
                show = orderDate >= yearAgo;
            }
            
            order.style.display = show ? 'block' : 'none';
        });
    });
    
    // Обработка кнопки "Повторить заказ"
    document.querySelectorAll('.repeat-order').forEach(btn => {
        btn.addEventListener('click', function() {
            const orderId = this.dataset.orderId;
            alert('Повтор заказа #' + orderId);
        });
    });
    </script>
</body>
</html>