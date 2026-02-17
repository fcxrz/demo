<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user_type'] !== 'organization') {
    $_SESSION['message'] = ['type' => 'danger', 'text' => 'Доступ запрещен. Только организации могут добавлять проекты.'];
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_project'])) {

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $goal = trim($_POST['goal'] ?? '');
    $tasks = trim($_POST['tasks'] ?? '');
    $how_to_join = trim($_POST['how_to_join'] ?? '');
    $organization_id = $_SESSION['user_id'];

    $image_url = null; 
    $modal_img_url = null; 

    if (empty($title) || empty($description) || empty($category)) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Пожалуйста, заполните все обязательные поля (Название, Описание, Категория).'];
        header('Location: lk_organization.php'); 
        exit();
    }

    function handleFileUpload($fileInputName, $pdo_conn) {
        if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'img/'; 

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true); 
            }

            $file_tmp_name = $_FILES[$fileInputName]['tmp_name'];
            $file_name_original = basename($_FILES[$fileInputName]['name']);
            $file_ext = strtolower(pathinfo($file_name_original, PATHINFO_EXTENSION));

            $new_file_name = uniqid('project_', true) . '.' . $file_ext;
            $target_file_path = $upload_dir . $new_file_name;
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array($file_ext, $allowed_extensions)) {
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'Ошибка: Только JPG, JPEG, PNG и GIF файлы разрешены для загрузки.'];
                return false; 
            }
            if ($_FILES[$fileInputName]['size'] > 5 * 1024 * 1024) { 
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'Ошибка: Размер файла не должен превышать 5MB.'];
                return false;
            }
            if (move_uploaded_file($file_tmp_name, $target_file_path)) {
                return '/' . $target_file_path;
            } else {
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'Ошибка при загрузке файла ' . $fileInputName . '.'];
                return false;
            }
        }
        return null; 
    }

    $image_url = handleFileUpload('image_file', $pdo);
    $modal_img_url = handleFileUpload('modal_img_file', $pdo);

    if ($image_url === false || $modal_img_url === false) {
        header('Location: lk_organization.php');
        exit();
    }
    try {
        $stmt = $pdo->prepare("INSERT INTO projects (title, description, category, image_url, modal_img_url, goal, tasks, how_to_join, organization_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $title,
            $description,
            $category,
            $image_url,
            $modal_img_url,
            $goal,
            $tasks,
            $how_to_join,
            $organization_id
        ]);

        $_SESSION['message'] = ['type' => 'success', 'text' => 'Проект "' . htmlspecialchars($title) . '" успешно добавлен!'];
        header('Location: lk_organization.php');
        exit();

    } catch (PDOException $e) {
        error_log("Database error adding project: " . $e->getMessage());
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Ошибка при добавлении проекта: ' . $e->getMessage()];
        header('Location: lk_organization.php');
        exit();
    }

} else {
    $_SESSION['message'] = ['type' => 'danger', 'text' => 'Неверный запрос.'];
    header('Location: lk_organization.php');
    exit();
}
?>