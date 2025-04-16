<?php
require_once '../scripts/db.php';
session_start();

$searchQuery = isset($_GET['search_query']) ? trim($_GET['search_query']) : '';
$products = [];

if (!empty($searchQuery)) {
    // Разбиваем запрос на отдельные слова
    $keywords = explode(' ', $searchQuery);
    $keywords = array_filter($keywords); // Удаляем пустые элементы
    
    // Формируем условия для каждого слова
    $conditions = [];
    $params = [];
    
    foreach ($keywords as $i => $keyword) {
        $conditions[] = "(title LIKE :keyword$i OR description LIKE :keyword$i)";
        $params[":keyword$i"] = "%$keyword%";
    }
    
    // Собираем полный запрос
    $sql = "SELECT * FROM products";
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }
    
    // Выполняем запрос
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Если ничего не найдено
    if (empty($products)) {
        $noResultsMessage = "По запросу \"".htmlspecialchars($searchQuery)."\" ничего не найдено";
    }
} else {
    // Если нет поискового запроса, показываем все товары (или ограниченное количество)
    try{
    $stmt = $pdo->query("SELECT * FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Ошибка при загрузке товаров: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BystroKorzin - Магазин видеокарт</title>
    <link rel="stylesheet" href="../css/styleprod.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                    <li><a href="registration.html">Регистрация</a></li>
                    <?php endif; ?>
                <li><a href="index.php">Главная</a></li>
                <li><a href="#about">О нас</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="set_of_products.php">Корзина</a></li>
                    <li><a href="zakaz.php">Заказы</a></li>
                    <li><a href="account.php">Личный кабинет</a></li>
                    <?php endif; ?>
                <li><a href="#contacts">Контакты</a></li>
            </ul>
        </nav>
                <div class="search-container">
                    <form method="GET" action="" class="search-form">
                        <input type="text" name="search_query" placeholder="Поиск видеокарт..." 
                            value="<?= isset($_GET['search_query']) ? htmlspecialchars($_GET['search_query']) : '' ?>"
                            class="search-input">
                        <button type="submit" class="search-button">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
    </div>
    </header>

    <!-- Основной контент -->
    <main>
        
                    <!-- Секция отображения поиска -->
        <section id="products" class="products-section">
    <?php if (!empty($searchQuery)): ?>
        <h1>Результаты поиска: "<?= htmlspecialchars($searchQuery) ?>"</h1>
        <?php if (isset($noResultsMessage)): ?>
            <p class="no-results"><?= $noResultsMessage ?></p>
            <a href="products.php" class="back-link">← Вернуться ко всем товарам</a>
        <?php endif; ?>
    <?php else: ?>
        <h1>Видеокарты в наличии</h1>
    <?php endif; ?>
    
    <div class="products-grid">
        <?php foreach ($products as $product): ?>
            <a href="product.php?id=<?= $product['id'] ?>" class="product-link">
                <div class="product-card">
                    <?php
                        // Проверяем наличие изображения
                        $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/' . $product['image'];
                        $imageUrl = file_exists($imagePath) ? "/{$product['image']}" : "/images/default.jpg";
                    ?>
                    <img src="<?= $imageUrl ?>" alt="<?= htmlspecialchars($product['title']) ?>">
                    <h3><?= htmlspecialchars($product['title']) ?></h3>
                    <p class="price">₽<?= number_format($product['price'], 0, '', ' ') ?></p>
                    
                    <form class="add-to-cart-form" data-product-id="<?= $product['id'] ?>">
                        <button type="button" class="add-to-cart">В корзину</button>
                    </form>
                </div>
            </a>
        <?php endforeach; ?>
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
        </div>
    </footer>
</body>
</html>