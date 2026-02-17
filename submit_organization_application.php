<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config.php';
    $organizationName = $_POST['organization_name'] ?? '';
    $contactPerson = $_POST['contact_person'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $description = $_POST['description'] ?? '';

    if (empty(trim($organizationName)) || empty(trim($contactPerson)) || empty(trim($email)) || empty(trim($password))) {
        http_response_code(400);
        die("Пожалуйста, заполните все обязательные поля: Название организации, Контактное лицо, Email и Пароль.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        die("Введен некорректный формат email.");
    }

    if (strlen($password) < 8) {
        http_response_code(400);
        die("Пароль должен содержать не менее 8 символов.");
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO organizations (organization_name, contact_person, email, password_hash, phone, description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$organizationName, $contactPerson, $email, $passwordHash, $phone, $description]);

        header("Location: success.html");
        exit();

    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { 
            http_response_code(409); 
            die("Ошибка регистрации: Организация с таким email уже зарегистрирована.");
        } else {
            http_response_code(500);
            die("Произошла ошибка при регистрации организации. Пожалуйста, попробуйте еще раз. Ошибка: " . $e->getMessage());
        }
    }

} else {
    http_response_code(405);
    die("Ошибка: недопустимый метод запроса.");
}