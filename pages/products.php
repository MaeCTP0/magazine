<?php
require_once '../scripts/db.php';

try {
    $stmt = $pdo->query("SELECT * FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Ошибка при загрузке товаров: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BystroKorzin - Магазин видеокарт</title>
    <link rel="stylesheet" href="../css/styleprod.css">
</head>
<body>
    <!-- Шапка -->
    <header>
        <nav>
            <ul class="nav-menu">
                <li><a href="#about">О нас</a></li>
                <li><a href="#auth">Авторизация</a></li>
                <li><a href="registration.html">Регистрация</a></li>
                <li><a href="#contacts">Контакты</a></li>
            </ul>
        </nav>
    </header>

    <!-- Основной контент -->
    <main>
        <!-- Секция товаров -->
        <section id="products" class="products-section">
            <h1>Видеокарты в наличии</h1>
            <div class="products-grid">
                <!-- Вывод всех товаров -->
                <div class="products-grid">
                    <?php foreach ($products as $product): ?>
                        <!-- Формируем ссылку с id товара из БД -->
                            <a href="product.php?id=<?= $product['id'] ?>" class="product-link">
                        <div class="product-card">
                            <?php
                                // Проверяем, есть ли файл
                                $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/' . $product['image'];
                                $imageUrl = file_exists($imagePath) ? "/{$product['image']}" : "/uploads/default.jpg";
                            ?>
                             <img src="/scripts/<?= $product['image'] ?>" alt="<?= $product['image'] ?>">
                            <h3><?= htmlspecialchars($product['title']) ?></h3>
                            <p class="price">₽<?= number_format($product['price'], 0, '', ' ') ?></p>
                            <div class="product-actions">
                                <input type="number" min="1" max="5" value="1">
                                <button>В корзину</button>
                            </div>
                        </div>
                        </a>
                    <?php endforeach; ?>
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

        <!-- Раздел "Авторизация" -->
        <section id="auth" class="auth-container">
            <h2>Авторизация</h2>
            <div class="auth-content">
                <form id="auth-form">
                    <div class="form-group">
                        <label for="auth-email">E-mail:</label>
                        <input type="email" id="auth-email" name="auth-email" required>
                    </div>
                    <div class="form-group">
                        <label for="auth-password">Пароль:</label>
                        <input type="password" id="auth-password" name="auth-password" required>
                    </div>
                    <div class="form-group">
                        <a href="#forgot-password" class="forgot-password">Забыли пароль?</a>
                    </div>
                    <button type="submit">Войти</button>
                </form>
            </div>
        </section>
    
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
</body>
</html>