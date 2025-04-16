<?php
// Подключаемся к БД
require_once '../scripts/db.php';
session_start();
// Проверяем, что id передан и это число
if (!isset($_GET['id'])) {
    die("Ошибка: ID товара не указан.");
}

$product_id = (int)$_GET['id']; // Приводим к числу для безопасности

// Загружаем товар из БД
try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Если товара нет — выдаем ошибку
    if (!$product) {
        die("Товар не найден.");
    }
} catch (PDOException $e) {
    die("Ошибка загрузки товара: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Описание товара</title>
    <link rel="stylesheet" href="../css/stylepr.css">
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
                    <?php if (!isset($_SESSION['user_id'])): ?>
                    <li><a href="#auth">Авторизация</a></li>
                    <li><a href="registration.html">Регистрация</a></li>
                    <?php endif; ?>
                <li><a href="index.php">Главная</a></li>
                <li><a href="products.php">Товары</a></li>
                <li><a href="#about">О нас</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="set_of_products.php">Корзина</a></li>
                    <li><a href="zakaz.php">Заказы</a></li>
                    <li><a href="account.php">Личный кабинет</a></li>
                    <?php endif; ?>
                <li><a href="#contacts">Контакты</a></li>
            </ul>
        </nav>
        </div>
</div>
    </header>

    <!-- Основной контент -->
    <div class="container">
        <!-- Раздел "Товар" -->
        <section id="product" class="product-container">
            <h1></h1>
            <div class="product-content">
                <!-- Картинка товара -->
                <div class="product-image">
                    <img src="/<?= $product['image'] ?>" alt="<?= $product['title'] ?>">
                </div>

               <!-- Информация о товаре -->
                <div class="product-info">
                    <h1><?= $product['title'] ?></h1>
                    <p><strong>Цена:</strong> ₽<?= number_format($product['price'], 0, '', ' ') ?></p>
                    <p><strong>Описание:</strong> <?= $product['description'] ?? 'Нет описания' ?></p>
                    <p><strong>Количество на складе:</strong> <?= $product['amount'] ?> </p>

                    <!-- Поле для выбора количества -->
                    <div class="form-group">
                        <label for="product-amount">Количество:</label>
                    </div>

                    <!-- Кнопка "Добавить в корзину" -->
                    <form action="../scripts/add_to_cart.php" method="post" id="add-to-cart-form">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <input type="number" name="amount" id="product-amount" value="1" min="1">
                        <button type="submit" name="add-to-cart">Добавить в корзину</button>
                    </form>
                </div>
            </div>
        </section>

        <!-- Раздел "О нас" -->
        <section id="about" class="about-container">
    <h2>О нас</h2>
    <div class="about-content">
        <p>
            Добро пожаловать в <strong>BystroKorzin</strong> — ваш надежный партнёр в мире высокопроизводительных видеокарт! 
            Мы специализируемся на предоставлении самых современных и мощных графических решений для геймеров, дизайнеров и энтузиастов.
        </p>
        <p>
            Наша миссия — сделать покупку видеокарт быстрой, удобной и приятной. 
            В <strong>BystroKorzin</strong> вы найдёте только проверенные бренды, такие как NVIDIA, AMD и другие.
        <p>
            Мы гордимся тем, что предлагаем товары по доступным ценам, оперативную доставку и отличный сервис. 
            Ваше удовлетворение — наш главный приоритет!
        </p>
    </div>
</section>

    <?php if (!isset($_SESSION['user_id'])): ?>
        <!-- Раздел "Авторизация" (только для неавторизованных) -->
        <section id="auth" class="auth-container">
            <h2>Авторизация</h2>
            <div class="auth-content">
                <form action="../pages/login.php" method="post" id="auth-form">
                    <div class="form-group">
                        <label for="email">E-mail:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Пароль:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <a href="#forgot-password" class="forgot-password">Забыли пароль?</a>
                    </div>
                    <button type="submit">Войти</button>
                </form>
            </div>
        </section>
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
</body>
</html>