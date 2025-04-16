<?php
session_start();

// Если пользователь уже авторизован, перенаправляем в профиль
if (isset($_SESSION['user_id'])) {
    header('Location: ../pages/account.php');
    exit();
}

// Обработка формы входа
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

        $email = $_POST['email'];
        $password = $_POST['password'];

        // Ищем пользователя по email
        $stmt = $connection->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Проверяем пароль
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: account.php');
            exit();
        } else {
            $error = "Неверный email или пароль";
        }

    } catch (PDOException $e) {
        $error = "Ошибка базы данных: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в BystroKorzin</title>
    <link rel="stylesheet" href="../css/stylelog.css">
<head>
    <body>
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
                        <li><a href="registration.html">Регистрация</a></li>
                        <li><a href="products.php">Товары</a></li>   
                        <li><a href="#contacts">Контакты</a></li> 
                    </ul>
                </nav>
</div>
</div>
            </header>
        <main class="auth-container">
                <h1>Вход в аккаунт</h1>
                
                <?php if (isset($error)): ?>
                    <p class="error-message"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>
                
                <form method="POST" class="auth-form">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Пароль:</label>
                        <input type="password" id="password" name="password" required>
                        <a href="forgot-password.html" class="forgot-password">Забыли пароль?</a>
                    </div>
                    
                    <button type="submit" class="auth-button">Войти</button>
                </form>
                
                <p class="auth-link">Ещё нет аккаунта? <a href="registration.html">Создайте его</a></p>
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