<?php
session_start();

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Убрал ../scripts/ для единообразия
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
        <nav>
            <ul class="nav-menu">
                <li><a href="about.html">О нас</a></li>
                <li><a href="products.php">Товары</a></li>
                <li><a href="cart.html">Корзина</a></li>
                <li><a href="orders.html">Заказы</a></li>
                <li><a href="search.html">Поиск</a></li>
                <li><a href="contacts.html">Контакты</a></li>
            </ul>
        </nav>
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
                    <button id="logout-button" onclick="location.href='edit-profile.php'">Редактировать профиль</button>
                    <button id="logout-button" onclick="location.href='../scripts/logout.php'">Выйти</button>
                </p>
            </div>
        </div>
    </div>
</body>
</html>