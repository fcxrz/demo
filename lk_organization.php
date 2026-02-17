<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user_type'] !== 'organization') {
    header('Location: login.php?error=access_denied');
    exit();
}

require_once 'config.php';

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['email'];
$organization_id = $_SESSION['user_id']; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['accept_volunteer']) || isset($_POST['reject_volunteer']))) {
    $application_id = $_POST['application_id'] ?? null;
    $new_status = isset($_POST['accept_volunteer']) ? 'approved' : 'rejected'; 
    
    $organization_message = trim($_POST['organization_message'] ?? ''); 

    if ($application_id) {
        try {
            $stmtCheck = $pdo->prepare("
                SELECT vpa.id
                FROM volunteer_project_applications vpa
                JOIN projects p ON vpa.project_id = p.id
                WHERE vpa.id = ? AND p.organization_id = ?
            ");
            $stmtCheck->execute([$application_id, $organization_id]);

            if ($stmtCheck->fetch()) {
                $stmtUpdate = $pdo->prepare("UPDATE volunteer_project_applications SET status = ?, organization_message = ? WHERE id = ?");
                $stmtUpdate->execute([$new_status, $organization_message, $application_id]);

                if ($stmtUpdate->rowCount() > 0) {
                    $_SESSION['message'] = ['type' => 'success', 'text' => 'Статус заявки успешно обновлен' . (!empty($organization_message) ? ' и сообщение отправлено.' : '.')];
                } else {
                    $_SESSION['message'] = ['type' => 'warning', 'text' => 'Статус заявки не изменился или заявка не найдена.'];
                }
            } else {
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'Ошибка: Заявка не принадлежит вашим проектам или не существует.'];
            }
        } catch (PDOException $e) {
            error_log("Database error updating volunteer application status: " . $e->getMessage());
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Ошибка при обновлении статуса: ' . $e->getMessage()];
        }
    } else {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Ошибка: ID заявки не передан.'];
    }
    header('Location: lk_organization.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_project'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $image_url = trim($_POST['image_url']); 
    $modal_img_url = trim($_POST['modal_img_url']);
    $goal = trim($_POST['goal']);
    $tasks = trim($_POST['tasks']);
    $how_to_join = trim($_POST['how_to_join']);

    if (empty($title) || empty($description) || empty($category)) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Пожалуйста, заполните все обязательные поля проекта (Название, Описание, Категория).'];
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO projects (organization_id, title, description, category, image_url, modal_img_url, goal, tasks, how_to_join) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $title, $description, $category, $image_url, $modal_img_url, $goal, $tasks, $how_to_join]);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Проект успешно добавлен!'];
        } catch (PDOException $e) {
            error_log("Database error adding project: " . $e->getMessage());
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Ошибка при добавлении проекта: ' . $e->getMessage()];
        }
    }
    header('Location: lk_organization.php'); 
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_project'])) {
    $project_id_to_delete = $_POST['project_id_to_delete'] ?? null;

    if ($project_id_to_delete) {
        try {
            $stmtCheckOwnership = $pdo->prepare("SELECT id FROM projects WHERE id = ? AND organization_id = ?");
            $stmtCheckOwnership->execute([$project_id_to_delete, $organization_id]);

            if ($stmtCheckOwnership->fetch()) {
                $stmtDeleteProject = $pdo->prepare("DELETE FROM projects WHERE id = ?");
                $stmtDeleteProject->execute([$project_id_to_delete]);

                if ($stmtDeleteProject->rowCount() > 0) {
                    $_SESSION['message'] = ['type' => 'success', 'text' => 'Проект успешно удален.'];
                } else {
                    $_SESSION['message'] = ['type' => 'warning', 'text' => 'Не удалось удалить проект или он не найден.'];
                }
            } else {
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'Ошибка: Проект не найден или не принадлежит вашей организации.'];
            }
        } catch (PDOException $e) {
            error_log("Database error deleting project: " . $e->getMessage());
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Ошибка при удалении проекта: ' . $e->getMessage()];
        }
    } else {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Ошибка: ID проекта не передан.'];
    }
    header('Location: lk_organization.php');
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT organization_name, contact_person, phone, description FROM organizations WHERE id = ?");
    $stmt->execute([$user_id]);
    $organizationData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$organizationData) {
        session_destroy();
        header('Location: login.php?error=data_not_found');
        exit();
    }
} catch (PDOException $e) {
    error_log("Database error retrieving organization data: " . $e->getMessage());
    session_destroy();
    header('Location: login.php?error=server_error');
    exit();
}

$organizationProjects = [];
try {
    $stmt = $pdo->prepare("SELECT id, title, description, category FROM projects WHERE organization_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $organizationProjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error retrieving organization's projects: " . $e->getMessage());
}

$organizationProjects = [];
try {
    $stmtProjects = $pdo->prepare("
        SELECT
            p.id AS project_id,
            p.title AS project_title,
            p.description AS project_description,
            p.category AS project_category,
            vpa.id AS application_id,
            vpa.status AS application_status,
            vpa.application_date,
            a.id AS volunteer_id,
            a.name AS volunteer_name,
            a.email AS volunteer_email,
            a.phone AS volunteer_phone,
            a.age AS volunteer_age,
            a.city AS volunteer_city,
            a.direction AS volunteer_direction,
            a.message AS volunteer_message
        FROM projects p
        LEFT JOIN volunteer_project_applications vpa ON p.id = vpa.project_id
        LEFT JOIN applications a ON vpa.volunteer_id = a.id
        WHERE p.organization_id = ?
        ORDER BY p.created_at DESC, vpa.application_date DESC
    ");
    $stmtProjects->execute([$organization_id]);
    $rawProjectsData = $stmtProjects->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rawProjectsData as $row) {
        $projectId = $row['project_id'];
        if (!isset($organizationProjects[$projectId])) {
            $organizationProjects[$projectId] = [
                'id' => $row['project_id'],
                'title' => $row['project_title'],
                'description' => $row['project_description'],
                'category' => $row['project_category'],
                'applications' => [] 
            ];
        }

        if ($row['application_id']) {
            $organizationProjects[$projectId]['applications'][] = [
                'application_id' => $row['application_id'],
                'status' => $row['application_status'],
                'application_date' => $row['application_date'],
                'volunteer' => [
                    'id' => $row['volunteer_id'],
                    'name' => $row['volunteer_name'],
                    'email' => $row['volunteer_email'],
                    'phone' => $row['volunteer_phone'],
                    'age' => $row['volunteer_age'],
                    'city' => $row['volunteer_city'],
                    'direction' => $row['volunteer_direction'],
                    'message' => $row['volunteer_message']
                ]
            ];
        }
    }
} catch (PDOException $e) {
    error_log("Database error retrieving organization projects and applications: " . $e->getMessage());
    $organizationProjects = []; 
}

$projectCategories = [
    'eco' => 'Экология',
    'children' => 'Дети',
    'charity' => 'Благотворительность',
    'education' => 'Образование',
    'medicine' => 'Медицина',
    'other' => 'Другое'
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет организации | Do Goodness</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles/style.css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        body {
            display: flex;
            flex-direction: column;
        }
        .content-wrapper { 
            flex-grow: 1;
            padding-top: 20px; 
            padding-bottom: 20px; 
        }
        .lk-header {
            background-color: #28a745; 
            color: white;
            padding: 20px 0;
            margin-bottom: 30px;
            text-align: center;
        }
        .profile-card, .project-management-card {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px; 
        }
        footer {
            width: 100%;
            flex-shrink: 0;
        }
        .project-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .project-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
<?php
require_once 'header.php'
?>
    <div class="content-wrapper">
        <div class="container">
            <?php

            if (isset($_SESSION['message'])) {
                echo '<div class="alert alert-' . $_SESSION['message']['type'] . ' alert-dismissible fade show" role="alert">';
                echo htmlspecialchars($_SESSION['message']['text']);
                echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                echo '</div>';
                unset($_SESSION['message']);
            }
            ?>

            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="profile-card">
                        <h3 class="mb-4">Информация о вашей организации</h3>
                        <p><strong>Название организации:</strong> <?php echo htmlspecialchars($organizationData['organization_name']); ?></p>
                        <p><strong>Контактное лицо:</strong> <?php echo htmlspecialchars($organizationData['contact_person']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user_email); ?></p>
                        <p><strong>Телефон:</strong> <?php echo htmlspecialchars($organizationData['phone']); ?></p>
                        <p><strong>Описание:</strong> <?php echo nl2br(htmlspecialchars($organizationData['description'])); ?></p>
                    </div>

                    <div class="project-management-card">
                        <h3 class="mb-4">Добавить новый проект</h3>
                       <form action="create_project.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="add_project" value="1">
                        <div class="mb-3">
                            <label for="title" class="form-label">Название проекта <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Краткое описание (для карточки, до 80 символов) <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="3" required maxlength="80"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Категория <span class="text-danger">*</span></label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Выберите категорию</option>
                                <?php foreach ($projectCategories as $key => $value): ?>
                                    <option value="<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars($value); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="image_file" class="form-label">Изображение для карточки проекта</label>
                            <input type="file" class="form-control" id="image_file" name="image_file" accept="image/*">
                            <div class="form-text">Выберите файл изображения (JPG, PNG, GIF).</div>
                        </div>
                        <div class="mb-3">
                            <label for="modal_img_file" class="form-label">Изображение для модального окна</label>
                            <input type="file" class="form-control" id="modal_img_file" name="modal_img_file" accept="image/*">
                            <div class="form-text">Выберите файл изображения (JPG, PNG, GIF), которое будет видно при нажатии "Подробнее".</div>
                        </div>
                        <div class="mb-3">
                            <label for="goal" class="form-label">Цель проекта</label>
                            <textarea class="form-control" id="goal" name="goal" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="tasks" class="form-label">Задачи проекта (каждая задача с новой строки)</label>
                            <textarea class="form-control" id="tasks" name="tasks" rows="5"></textarea>
                            <div class="form-text">Введите каждую задачу с новой строки.</div>
                        </div>
                        <div class="mb-3">
                            <label for="how_to_join" class="form-label">Как присоединиться к проекту</label>
                            <textarea class="form-control" id="how_to_join" name="how_to_join" rows="3" placeholder="Ваш канал в соц. сети, email или любой другой вид связи"></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">Добавить проект</button>
                        </div>
                    </form>
                    </div>


                   <div class="row">
            <div class="col-md-12">
                <h3 class="mb-4">Ваши проекты и отклики волонтеров</h3>
                 <?php foreach ($organizationProjects as $project): ?>
                            <div class="card mb-4 shadow-sm">
                                <div class="card-header bg-dark text-white">
                                    <h4 class="mb-0"><?php echo htmlspecialchars($project['title']); ?></h4>
                                    <small>Категория: <?php echo htmlspecialchars($project['category']); ?></small>
                                </div>
                                <div class="card-body">
                                    <p class="card-text"><?php echo htmlspecialchars($project['description']); ?></p>

                                    <div class="d-flex justify-content-end align-items-center">
                                        <form action="lk_organization.php" method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить проект «<?php echo htmlspecialchars($project['title']); ?>»? Это действие необратимо и удалит все связанные отклики волонтеров!');">
                                            <input type="hidden" name="delete_project" value="1">
                                            <input type="hidden" name="project_id_to_delete" value="<?php echo $project['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Удалить проект</button>
                                        </form>
                                    </div>
                                    <hr> <h5>Отклики волонтеров (<?php echo count($project['applications']); ?>)</h5>
                                <?php if (!empty($project['applications'])): ?>
                                    <ul class="list-group list-group-flush">
                                        <?php foreach ($project['applications'] as $application): ?>
                                            <?php $volunteer = $application['volunteer']; ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                <div>
                                                    <h6><?php echo htmlspecialchars($volunteer['name'] ?? 'Волонтер'); ?></h6>
                                                    <p class="mb-1"><small>Email: <?php echo htmlspecialchars($volunteer['email'] ?? 'N/A'); ?></small></p>
                                                    <p class="mb-1"><small>Телефон: <?php echo htmlspecialchars($volunteer['phone'] ?? 'N/A'); ?></small></p>
                                                    <p class="mb-1"><small>Город: <?php echo htmlspecialchars($volunteer['city'] ?? 'N/A'); ?></small></p>
                                                    <p class="mb-1"><small>Возраст: <?php echo htmlspecialchars($volunteer['age'] ?? 'N/A'); ?></small></p>
                                                    <p class="mb-1"><small>Направление: <?php echo htmlspecialchars($volunteer['direction'] ?? 'N/A'); ?></small></p>
                                                    <?php if (!empty($volunteer['message'])): ?>
                                                        <p class="mb-1"><small>Сообщение: <?php echo htmlspecialchars($volunteer['message']); ?></small></p>
                                                    <?php endif; ?>
                                                    <small class="text-muted">Откликнут: <?php echo date('d.m.Y H:i', strtotime($application['application_date'])); ?></small>
                                                </div>
                                                <div class="mt-2 mt-md-0">
                                                    <?php
                                                    $statusClass = '';
                                                    switch ($application['status']) {
                                                        case 'pending': $statusClass = 'badge bg-warning text-dark'; break;
                                                        case 'approved': $statusClass = 'badge bg-success'; break;
                                                        case 'rejected': $statusClass = 'badge bg-danger'; break;
                                                        default: $statusClass = 'badge bg-secondary'; break;
                                                    }
                                                    ?>
                                                    <span class="<?php echo $statusClass; ?> me-2"><?php echo htmlspecialchars(ucfirst($application['status'])); ?></span>

                                                    <?php if ($application['status'] === 'pending' || $application['status'] === 'rejected'): ?>
                                                    <button type="button" class="btn btn-success btn-sm me-1"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#acceptModal<?php echo $application['application_id']; ?>">
                                                        Принять
                                                    </button>
                                                <?php endif; ?>

                                                <div class="modal fade" id="acceptModal<?php echo $application['application_id']; ?>" tabindex="-1" aria-labelledby="acceptModalLabel<?php echo $application['application_id']; ?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form action="lk_organization.php" method="POST">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="acceptModalLabel<?php echo $application['application_id']; ?>">Подтверждение заявки для <?php echo htmlspecialchars($volunteer['name'] ?? 'Волонтера'); ?></h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>Вы собираетесь принять заявку от <b><?php echo htmlspecialchars($volunteer['name'] ?? 'Волонтера'); ?></b> на проект <b><?php echo htmlspecialchars($project['title']); ?></b>.</p>
                                                                    <div class="mb-3">
                                                                        <label for="organizationMessage<?php echo $application['application_id']; ?>" class="form-label">Сообщение волонтеру</label>
                                                                        <textarea class="form-control" id="organizationMessage<?php echo $application['application_id']; ?>" name="organization_message" rows="3" placeholder="Здесь вы можете указать ваши данные для связи с волонтером"></textarea>
                                                                    </div>
                                                                    <input type="hidden" name="application_id" value="<?php echo $application['application_id']; ?>">
                                                                    <input type="hidden" name="accept_volunteer" value="1">
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                                                                    <button type="submit" class="btn btn-success">Подтвердить и отправить сообщение</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                    <?php if ($application['status'] === 'pending' || $application['status'] === 'approved'): ?>
                                                        <form action="lk_organization.php" method="POST" class="d-inline-block">
                                                            <input type="hidden" name="application_id" value="<?php echo $application['application_id']; ?>">
                                                            <button type="submit" name="reject_volunteer" class="btn btn-danger btn-sm">Отклонить</button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="text-muted">На этот проект пока нет откликов.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="alert alert-info" role="alert">
                        Вы пока не создали ни одного проекта. <a href="#" class="alert-link" data-bs-toggle="modal" data-bs-target="#addProjectModal">Создайте новый проект!</a>
                    </div>
            </div>
        </div>
                    
                    <div class="d-grid mt-4">
                        <a href="logout.php" class="btn btn-danger btn-lg">Выйти</a>
                    </div>
                </div>
            </div>
        </div>
    </div> <footer class="bg-black text-white text-center py-4">
        <div class="container">
            <div class="soc-link m-3">
                <a href="#" class="text-white mx-2"><i class="bi bi-facebook fs-2"></i></a>
                <a href="#" class="text-white mx-2"><i class="bi bi-instagram fs-2"></i></a>
                <a href="#" class="text-white mx-2"><i class="bi bi-twitter fs-2"></i></a>
            </div>
            <p>&copy; 2025 Do Goodness. Все права защищены</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>