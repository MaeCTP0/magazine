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
<head>
    <meta charset="UTF-8">
    <title>Вход в систему</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 400px; margin: 0 auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="email"], input[type="password"] { width: 100%; padding: 8px; box-sizing: border-box; }
        button { padding: 10px 15px; background: #4CAF50; color: white; border: none; cursor: pointer; }
        .error { color: red; margin-top: 10px; }
    </style>
</head>
<body>
    <h1>Вход в систему</h1>
    
    <?php if (isset($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit">Войти</button>
    </form>
    
    <p>Ещё нет аккаунта? <a href="registration.html">Зарегистрируйтесь</a></p>
</body>
</html>