<?php
function checkUser(){
    $host = 'MySQL-5.5';
    $db_name = 'emagazine';
    $user = 'root';
    $password = '';
    $connection = mysqli_connect($host, $user, $password, $db_name);

    $email = mysqli_real_escape_string($connection, $_POST['email']);

    $query = "SELECT * FROM `users` WHERE `users`.email='$email'";
    $result = mysqli_query($connection, $query);
    if (mysqli_num_rows( $result ) > 0) {
        mysqli_close($connection);
        return true;
    } else {
        mysqli_close($connection);
        return false;
    }
}
function Reg() {
    // Настройки подключения к БД
    $host = 'MySQL-5.5';
    $db_name = 'emagazine';
    $user = 'root';
    $password = '';
    
    // Настройки загрузки изображений
    $uploadDir = __DIR__ . '../uploads/';
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 2 * 1024 * 1024; // 2MB

    try {
        // Подключение к БД
        $connection = new PDO(
            "mysql:host=$host;dbname=$db_name;charset=utf8mb4", 
            $user, 
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 30
            ]
        );
         // Получаем ID нового пользователя
         $userId = $connection->lastInsertId();

         // Сохраняем только ID в сессии (остальные данные получим из БД)
         $_SESSION['user_id'] = $userId;

        // Получение данных формы
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $name = htmlspecialchars($_POST['first-name']);
        $surname = htmlspecialchars($_POST['last-name']);
        $age = intval($_POST['age']);
        $sex = ($_POST['gender'] === 'male') ? 1 : 0;
        $roles = 1;
        $picturePath = null;

        // Обработка изображения
        if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['picture'];
            
            // Проверка типа файла
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($mime, $allowedTypes)) {
                throw new Exception('Допустимы только JPG, PNG или GIF');
            }
            
            if ($file['size'] > $maxSize) {
                throw new Exception('Максимальный размер файла 2MB');
            }
            
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid('img_') . '.' . $extension;
            $destination = $uploadDir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $picturePath = 'uploads/' . $filename;
            } else {
                throw new Exception('Ошибка сохранения файла');
            }
        }

        // SQL-запрос
        $sql = "INSERT INTO users (email, password, name, surname, sex, age, picture, roles)
                VALUES (:email, :password, :name, :surname, :sex, :age, :picture, :roles)";
        
        $stmt = $connection->prepare($sql);
        $stmt->execute([
            ':email' => $email,
            ':password' => $password,
            ':name' => $name,
            ':surname' => $surname,
            ':sex' => $sex,
            ':age' => $age,
            ':picture' => $picturePath,
            ':roles' => $roles
        ]);

        header('Location: ../pages/account.php');
        exit();

    } catch (PDOException $e) {
        // Удаление загруженного файла при ошибке
        if (isset($destination) && file_exists($destination)) {
            unlink($destination);
        }
        
        // Правильное логирование ошибки
        $errorDetails = [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'errorInfo' => $e->errorInfo
        ];
        
        error_log(print_r($errorDetails, true));
        file_put_contents('registration_errors.log', print_r($errorDetails, true), FILE_APPEND);
        
        die('Произошла ошибка при регистрации. Пожалуйста, попробуйте позже.');
        
    } catch (Exception $e) {
        error_log('General error: ' . $e->getMessage());
        die($e->getMessage());
    }
}
