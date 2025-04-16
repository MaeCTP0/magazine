<?php
session_start();

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Подключение к БД
$host = 'MySQL-5.5';
$db_name = 'emagazine';
$user = 'root';
$password = '';

try {
    $connection = new PDO(
        "mysql:host=$host;dbname=$db_name;charset=utf8mb4", 
        $user, 
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Запрос данных пользователя по ID из сессии
    $stmt = $connection->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$userData) {
        throw new Exception("Пользователь не найден");
    }

    // Преобразуем пол в текст
    $genderText = ($userData['sex'] == 1) ? 'Мужской' : 'Женский';

} catch (PDOException $e) {
    die("Ошибка базы данных: " . $e->getMessage());
} catch (Exception $e) {
    die($e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="../css/styleacc.css">
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
                <li><a href="../pages/set_of_products.php">Корзина</a></li>
                <li><a href="zakaz.php">Заказы</a></li>
                <li><a href="#about">О нас</a></li>
                <li><a href="#contacts">Контакты</a></li>
            </ul>
        </nav>
        </div>
        </div>
    </header>

    <!-- Основной контент -->
    <div class="profile-container">
        <h1>Личный кабинет</h1>
        <div class="profile-content">
            <!-- Аватарка -->
            <div class="profile-avatar">
            <?php
            $uploadDir = '../scripts/uploads/';
            $imagePath = !empty($userData['picture']) ? $uploadDir . basename($userData['picture']) : '../images/default-avatar.jpg';
            ?>
            <img src="<?= htmlspecialchars($imagePath) ?>" 
                onerror="this.src='../images/default-avatar.jpg'" 
                alt="Аватарка профиля">
            </div>

            <!-- Информация о пользователе -->
            <div class="profile-info">
                <p><strong>Имя:</strong> <span id="profile-first-name"><?= htmlspecialchars($userData['name']) ?></span></p>
                <p><strong>Фамилия:</strong> <span id="profile-last-name"><?= htmlspecialchars($userData['surname']) ?></span></p>
                <p><strong>E-mail:</strong> <span id="profile-email"><?= htmlspecialchars($userData['email']) ?></span></p>
                <p><strong>Возраст:</strong> <span id="profile-age"><?= htmlspecialchars($userData['age']) ?></span></p>
                <p><strong>Пол:</strong> <span id="profile-gender"><?= $genderText ?></span></p>
                <p>
                    <button id="logout-button" onclick="location.href='../scripts/logout.php'">Выйти</button>
                </p>
            </div>
        </div>
    </div>
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