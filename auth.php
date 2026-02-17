<?php
session_start();

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo '<script type="text/javascript">';
    echo 'window.location.href = "login.php?error=access_denied";';
    echo '</script>';
    exit();
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$userType = $_POST['user_type'] ?? '';

if (empty($email) || empty($password) || empty($userType)) {
    echo '<script type="text/javascript">';
    echo 'window.location.href = "login.php?error=invalid_credentials";';
    echo '</script>';
    exit();
}

$table = '';
$redirect_lk = '';

if ($userType === 'volunteer') {
    $table = 'applications';
    $redirect_lk = 'lk_volunteer.php';
} elseif ($userType === 'organization') {
    $table = 'organizations';
    $redirect_lk = 'lk_organization.php';
} else {
    echo '<script type="text/javascript">';
    echo 'window.location.href = "login.php?error=invalid_credentials";';
    echo '</script>';
    exit();
}

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT id, email, password_hash FROM `" . $table . "` WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password_hash'])) {
        echo '<script type="text/javascript">';
        echo 'window.location.href = "login.php?error=invalid_credentials";';
        echo '</script>';
        exit();
    }

    $_SESSION['loggedin'] = true;
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['user_type'] = $userType;

    echo '<script type="text/javascript">';
    echo 'window.location.href = "' . $redirect_lk . '";';
    echo '</script>';
    exit();

} catch (PDOException $e) {

    error_log("Database error during authentication for email " . $email . ": " . $e->getMessage());
    echo '<script type="text/javascript">';
    echo 'console.error("Database Error: An unexpected error occurred. Please try again later.");';
    echo 'window.location.href = "login.php?error=server_error";';
    echo '</script>';
    exit();

} catch (Exception $e) {

    error_log("General error during authentication for email " . $email . ": " . $e->getMessage());
    echo '<script type="text/javascript">';
    echo 'console.error("General Error: An unexpected error occurred. Please try again later.");';
    echo 'window.location.href = "login.php?error=server_error";';
    echo '</script>';
    exit();
}
?>