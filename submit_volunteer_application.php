<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config.php';

    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? ''; 
    $direction = $_POST['direction'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $age = $_POST['age'] ?? '';
    $city = $_POST['city'] ?? '';
    $message = $_POST['message'] ?? '';

    if (empty(trim($name)) || empty(trim($email)) || empty(trim($password)) || empty(trim($direction))) {
        http_response_code(400); 
        die("Пожалуйста, заполните все обязательные поля: Имя, Email, Пароль и Направление.");
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400); 
        die("Некорректный email.");
    }

    if (!empty($age) && (!is_numeric($age) || $age < 14 || $age > 99)) {
        http_response_code(400); 
        die("Укажите корректный возраст (от 14 до 99).");
    }

    if (strlen($password) < 8) { 
        http_response_code(400); 
        die("Пароль должен содержать не менее 8 символов.");
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT); // хэшируем

    try {
        $stmt = $pdo->prepare("INSERT INTO applications (name, email, password_hash, direction, phone, age, city, message) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $passwordHash, $direction, $phone, $age, $city, $message]);

        header("Location: success.html");
        exit();
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { 
            http_response_code(409); 
            die("Ошибка регистрации: Пользователь с таким email уже зарегистрирован.");
        } else {
            error_log("Database error during volunteer registration: " . $e->getMessage()); 
            http_response_code(500);
            die("Произошла ошибка при регистрации. Пожалуйста, попробуйте еще раз.");
        }
    }
} else {
    http_response_code(405);
    die("Ошибка: недопустимый метод запроса.");
}
?>