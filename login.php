<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход | Do Goodness</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f8f9fa; 
        }
        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        .login-header {
            margin-bottom: 30px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="login-header">Вход в личный кабинет</h2>
        <?php
        if (isset($_GET['error'])) {
            $error_message = '';
            if ($_GET['error'] == 'invalid_credentials') {
                $error_message = 'Неверный email или пароль.';
            } elseif ($_GET['error'] == 'access_denied') {
                $error_message = 'Пожалуйста, войдите, чтобы получить доступ к этой странице.';
            } elseif ($_GET['error'] == 'type_mismatch') {
                $error_message = 'Вы пытаетесь войти как волонтер, но ваш email принадлежит организации.';
            }
            echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($error_message) . '</div>';
        }
        ?>
        <form action="auth.php" method="POST" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Ваш email" required>
                <div class="invalid-feedback">Пожалуйста, введите корректный email.</div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Пароль</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Ваш пароль" required>
                <div class="invalid-feedback">Пожалуйста, введите ваш пароль.</div>
            </div>
            <div class="mb-3">
                <label for="userType" class="form-label">Войти как:</label>
                <select class="form-select" id="userType" name="user_type" required>
                    <option value="" disabled selected>Выберите тип пользователя</option>
                    <option value="volunteer">Волонтер</option>
                    <option value="organization">Организация</option>
                </select>
                <div class="invalid-feedback">Пожалуйста, выберите тип пользователя.</div>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">Войти</button>
                <a href="join.php" class="btn btn-outline-secondary">Зарегистрироваться</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            'use strict'
            const form = document.querySelector('.needs-validation')
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })()
    </script>
</body>
</html>