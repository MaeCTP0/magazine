<?php
session_start();
require_once '../scripts/db.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// Получаем товары в корзине
try {
    $stmt = $pdo->prepare("
        SELECT cart.id as cart_id, products.*, cart.amount 
        FROM cart 
        JOIN products ON cart.product_id = products.id 
        WHERE cart.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Ошибка загрузки корзины: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ru">
<link rel="stylesheet" href="../css/stylereg.css">
<head>
    <meta charset="UTF-8">
    <title>Корзина</title>
    <link rel="stylesheet" href="<?= $css_file ?>">
</head>
<body>
        <!-- Шапка с навигацией -->
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
                <li><a href="zakaz.php">Заказы</a></li>
                <li><a href="search.html">Поиск</a></li>
                <li><a href="account.php">Личный кабинет</a></li>
                <li><a href="#contacts">Контакты</a></li>
            </ul>
        </nav>
        </div>
</div>
    </header>
    <div class="cart-container">
        <h1 class="cart-title">Ваша корзина</h1>
        
        <?php if (empty($cart_items)): ?>
            <p class="empty-cart-message">Ваша корзина пуста</p>
        <?php else: ?>
            <div class="cart-items-grid">
                <?php $total = 0; ?>
                
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item">
                        <div class="cart-item-image-container">
                            <img src="/<?= htmlspecialchars($item['image']) ?>" 
                                alt="<?= htmlspecialchars($item['title']) ?> " width="420" 
                                class="cart-item-image">
                        </div>
                            
                        <div class="cart-item-info">
                            <h3><?= htmlspecialchars($item['title']) ?></h3>
                            <p class="cart-item-price">
                                ₽<?= number_format($item['price'], 0, '', ' ') ?> × <?= $item['amount'] ?>
                            </p>
                            
                            <form action="../scripts/update_cart.php" method="post" class="cart-item-form">
                                <div class="amount-control">
                                    <label>Количество:</label>
                                    <input type="number" name="amount" 
                                        min="1" max="99" 
                                        value="<?= $item['amount'] ?>">
                                </div>
                                
                                <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                <button type="submit" class="cart-btn btn-update">Обновить</button>
                            </form>
                            
                            <form action="../scripts/remove_from_cart.php" method="post">
                                <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                <button type="submit" class="cart-btn btn-remove">Удалить</button>
                            </form>
                        </div>
                    </div>
                    
                    <?php $total += $item['price'] * $item['amount']; ?>
                <?php endforeach; ?>
            </div>
            
            <div class="cart-summary">
                <div class="total-price">
                    Итого: ₽<?= number_format($total, 0, '', ' ') ?>
                </div>
                
                <form id="checkout-form" action="../scripts/checkout.php" method="post">
                <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
                <button type="submit" id="checkout-btn" class="btn-checkout">
                    Оформить заказ
                </button>

            </div>
        <?php endif; ?>
    </div>
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
        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const btn = document.getElementById('checkout-btn');
            btn.disabled = true;
            btn.textContent = 'Оформляем...';
            
            fetch(form.action, {
                method: 'POST',
                body: new FormData(form)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'zakaz.php'; // Перенаправляем на страницу заказов
                } else {
                    alert(data.error || 'Ошибка оформления заказа');
                    btn.disabled = false;
                    btn.textContent = 'Оформить заказ';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Произошла ошибка');
                btn.disabled = false;
                btn.textContent = 'Оформить заказ';
            });
        });
    </script>
</body>
</html>