<?php
session_start();
require_once '../scripts/db.php';

try {
    // Получаем товары для слайдера
    $products_stmt = $pdo->query("SELECT * FROM products");
    $products = $products_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Получаем отзывы с информацией о пользователях
    $reviews_stmt = $pdo->query("
        SELECT 
            feedback.id,
            feedback.text,
            feedback.date_added,
            users.id AS user_id,
            users.name AS user_name,
             users.surname AS user_surname,
            users.picture AS picture,
            products.id AS id,
            products.title AS title
        FROM feedback
        JOIN users ON feedback.user_id = users.id
        JOIN products ON feedback.product_id = products.id
        ORDER BY feedback.date_added DESC
    ");
    $reviews = $reviews_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Ошибка при загрузке данных: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная | BystroKorzin</title>
    <link rel="stylesheet" href="../css/styleind.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <li><a href="#auth">Авторизация</a></li>
                            <li><a href="registration.php">Регистрация</a></li>
                        <?php endif; ?>
                        <li><a href="products.php">Товары</a></li>
                        <li><a href="#reviews">Отзывы</a></li>
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

    <main>

        <!-- Слайдер товаров -->
            <section id="products" class="products-slider">
                <h2>Популярные видеокарты</h2>
                <div class="slider-container">
                    <button class="slider-nav slider-prev"><i class="fas fa-chevron-left"></i></button>
                    <div class="slider-track">
                        <?php foreach ($products as $product): ?>
                        <div class="slide">
                            <img src="/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['title']) ?>">
                            <div class="slide-content">
                                <h3><?= htmlspecialchars($product['title']) ?></h3>
                                <p><?= htmlspecialchars(substr($product['description'], 0, 100)) ?>...</p>
                                <a href="product.php?id=<?= $product['id'] ?>" class="details-btn">Подробнее</a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="slider-nav slider-next"><i class="fas fa-chevron-right"></i></button>
                </div>
            </section>

       <!-- Секция отзывов -->
        <section id="reviews" class="reviews-section">
            <h2>Отзывы наших клиентов</h2>
            <div class="reviews-slider">
                <div class="reviews-track">
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-card">
                            <div class="review-header">
                                <?php if ($review['picture']): ?>
                                    <img src="/scripts/<?= htmlspecialchars($review['picture']) ?>" 
                                        alt="<?= htmlspecialchars($review['title']) ?>" 
                                        class="review-avatar">
                                <?php else: ?>
                                    <div class="review-avatar default-avatar">
                                        <?= mb_substr($review['user_name'], 0, 1) ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <h4><?= htmlspecialchars($review['user_name'] . ' ' . htmlspecialchars($review['user_surname'])) ?></h4>
                                    <span class="review-date">
                                        <?= date('d.m.Y', strtotime($review['date_added'])) ?>
                                    </span>
                                </div>
                            </div>
                            <p class="review-text"><?= htmlspecialchars($review['text']) ?></p>
                            <a href="product.php?id=<?= $review['id'] ?>" class="review-product-link">
                                О товаре: <?= htmlspecialchars($review['title']) ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="slider-nav reviews-prev"><i class="fas fa-chevron-left"></i></button>
                <button class="slider-nav reviews-next"><i class="fas fa-chevron-right"></i></button>
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
    </main>

     <!-- Подвал с контактами -->
     <footer id="contacts" class="footer-container">
        <h2>Контакты</h2>
        <div class="footer-content">
            <p>Телефон: +7 (999) 123-45-67</p>
            <p>E-mail: info@emagazine.ru</p>
            <p>Адрес: г. Москва, ул. Примерная, д. 123</p>
            <p>
                <div class="logo">
  <svg width="40" height="40" viewBox="0 0 40 40" fill="none">
    <path d="M20 5L10 20H25L15 35" stroke="#6C5B7B" stroke-width="3"/>
    <circle cx="28" cy="28" r="6" fill="#81C784" stroke="#6C5B7B"/>
  </svg>
                </div>
            </p>
        </div>
    </footer>

    <script src="../scripts/slider.js"></script>
</body>
</html>