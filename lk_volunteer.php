<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user_type'] !== 'volunteer') {
    header('Location: login.php?error=access_denied');
    exit();
}

require_once 'config.php';

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_application'])) {
    $project_id_to_cancel = $_POST['project_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM volunteer_project_applications WHERE volunteer_id = ? AND project_id = ? AND status = 'pending'"); // Добавил проверку статуса, чтобы нельзя было отменить уже одобренные/отклоненные
        $stmt->execute([$user_id, $project_id_to_cancel]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Ваш отклик на проект успешно отменен.'];
        } else {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Не удалось отменить отклик или он не найден (возможно, его статус уже изменился).'];
        }
    } catch (PDOException $e) {
        error_log("Database error cancelling application: " . $e->getMessage());
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Ошибка при отмене отклика: ' . $e->getMessage()];
    }
    header('Location: lk_volunteer.php');
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT id, name, email, phone, age, city, message, direction FROM applications WHERE id = ?");
    $stmt->execute([$user_id]); 
    $volunteerData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$volunteerData) {
        session_destroy();
        header('Location: login.php?error=data_not_found');
        exit();
    }
} catch (PDOException $e) {
    error_log("Database error retrieving volunteer data: " . $e->getMessage());
    session_destroy();
    header('Location: login.php?error=server_error');
    exit();
}

$appliedProjects = [];
try {
    $stmt = $pdo->prepare("SELECT p.id, p.title, p.description, p.category, vpa.application_date, vpa.status
                            FROM volunteer_project_applications vpa
                            JOIN projects p ON vpa.project_id = p.id
                            WHERE vpa.volunteer_id = ?
                            ORDER BY vpa.application_date DESC");
    $stmt->execute([$user_id]); // Используем $user_id
    $appliedProjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error retrieving volunteer's applied projects: " . $e->getMessage());
}

$volunteerApplications = [];
try {
    $stmtApplications = $pdo->prepare("
        SELECT
            vpa.id AS application_id,
            vpa.status AS application_status,
            vpa.application_date,
            vpa.organization_message,
            p.title AS project_title,
            p.description AS project_description,
            o.organization_name,
            p.id AS project_id_for_cancel
        FROM volunteer_project_applications vpa
        JOIN projects p ON vpa.project_id = p.id
        JOIN organizations o ON p.organization_id = o.id
        WHERE vpa.volunteer_id = ?
        ORDER BY vpa.application_date DESC
    ");
    $stmtApplications->execute([$user_id]); 
    $volunteerApplications = $stmtApplications->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error retrieving volunteer applications: " . $e->getMessage());
    $volunteerApplications = [];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет волонтера | Do Goodness</title>
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
        ::-webkit-scrollbar {
            width: 0;
        }
        .content-wrapper {
            flex-grow: 1;
            padding-bottom: 20px;
        }
        .mt-header-compensation {
            margin-top: 30px; 
        }
        .lk-header {
            background-color: #0d6efd; 
            color: white;
            padding: 20px 0;
            margin-bottom: 30px;
            text-align: center;
        }
        .profile-card, .applied-projects-card {
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
        .status-badge {
            font-size: 0.85em;
            padding: 0.4em 0.8em;
            border-radius: 0.25rem;
        }
        .status-pending { background-color: #ffc107; color: #343a40; } 
        .status-approved { background-color: #28a745; color: white; } 
        .status-rejected { background-color: #dc3545; color: white; } 
    </style>
</head>
<body>
<?php
require_once 'header.php'
?>
    <div class="content-wrapper mt-header-compensation">
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
                        <h3 class="mb-4">Ваши данные</h3>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user_email); ?></p>
                        <p><strong>Имя:</strong> <?php echo htmlspecialchars($volunteerData['name'] ?? 'Не указано'); ?></p>
                        <p><strong>Телефон:</strong> <?php echo htmlspecialchars($volunteerData['phone'] ?? 'Не указан'); ?></p>
                        <p><strong>Возраст:</strong> <?php echo htmlspecialchars($volunteerData['age'] ?? 'Не указан'); ?></p>
                        <p><strong>Город:</strong> <?php echo htmlspecialchars($volunteerData['city'] ?? 'Не указан'); ?></p>
                        <p><strong>Выбранное направление:</strong> <?php echo htmlspecialchars($volunteerData['direction'] ?? 'Не указано'); ?></p>
                        <p><strong>Дополнительная информация:</strong> <?php echo htmlspecialchars($volunteerData['message'] ?? 'Не указана'); ?></p>
                    </div>

                    <div class="applied-projects-card">
                        <h3 class="mb-4">Проекты, на которые вы откликнулись (<?php echo count($appliedProjects); ?>)</h3>
                        <?php if (!empty($appliedProjects)): ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($appliedProjects as $project): ?>
                                    <li class="list-group-item project-item">
                                        <div>
                                            <h5><?php echo htmlspecialchars($project['title']); ?></h5>
                                            <p class="text-muted mb-1">Категория: <?php echo htmlspecialchars($project['category']); ?></p>
                                            <p class="text-muted mb-0">Отклик: <?php echo date('d.m.Y H:i', strtotime($project['application_date'])); ?></p>
                                        </div>
                                        <div>
                                            <?php
                                            $statusClass = '';
                                            switch ($project['status']) {
                                                case 'pending': $statusClass = 'status-pending'; break;
                                                case 'approved': $statusClass = 'status-approved'; break;
                                                case 'rejected': $statusClass = 'status-rejected'; break;
                                                default: $statusClass = 'status-pending'; break;
                                            }
                                            ?>
                                            <span class="status-badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($project['status']); ?></span>
                                            
                                            <?php if ($project['status'] === 'pending'): ?>
                                                <form action="lk_volunteer.php" method="POST" class="d-inline-block ms-2" onsubmit="return confirm('Вы уверены, что хотите отменить отклик на этот проект?');">
                                                    <input type="hidden" name="cancel_application" value="1">
                                                    <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                                    <button type="submit" class="btn btn-warning btn-sm">Отменить отклик</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="alert alert-info" role="alert">
                                Вы еще не откликнулись ни на один проект. <a href="projects.php" class="alert-link">Посмотрите доступные проекты!</a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="applied-projects-card mt-4">
                        <h3 class="mb-4">Детальные отклики и сообщения от организаций</h3>
                        <?php if (!empty($volunteerApplications)): ?>
                            <?php foreach ($volunteerApplications as $application): ?>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($application['project_title']); ?></h5>
                                        <p class="card-text">Организация: <?php echo htmlspecialchars($application['organization_name']); ?></p>
                                        <p class="card-text">Статус заявки:
                                            <?php
                                            $statusClass = '';
                                            switch ($application['application_status']) {
                                                case 'pending': $statusClass = 'badge bg-warning text-dark'; break;
                                                case 'approved': $statusClass = 'badge bg-success'; break;
                                                case 'rejected': $statusClass = 'badge bg-danger'; break;
                                                default: $statusClass = 'badge bg-secondary'; break;
                                            }
                                            ?>
                                            <span class="<?php echo $statusClass; ?>"><?php echo htmlspecialchars(ucfirst($application['application_status'])); ?></span>
                                        </p>
                                        <p class="card-text"><small class="text-muted">Отправлено: <?php echo date('d.m.Y H:i', strtotime($application['application_date'])); ?></small></p>

                                        <?php if (!empty($application['organization_message'])): ?>
                                            <div class="alert alert-info mt-3" role="alert">
                                                <h6 class="alert-heading">Сообщение от организации:</h6>
                                                <p class="mb-0"><?php echo nl2br(htmlspecialchars($application['organization_message'])); ?></p>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($application['application_status'] === 'pending'): ?>
                                            <form action="lk_volunteer.php" method="POST" class="d-inline-block mt-2" onsubmit="return confirm('Вы уверены, что хотите отменить отклик на этот проект?');">
                                                <input type="hidden" name="cancel_application" value="1">
                                                <input type="hidden" name="project_id" value="<?php echo $application['project_id_for_cancel']; ?>">
                                                <button type="submit" class="btn btn-warning btn-sm">Отменить отклик</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-info" role="alert">
                                Здесь будут отображаться подробности ваших откликов и сообщения от организаций.
                            </div>
                        <?php endif; ?>
                    </div>


                    <div class="d-grid mt-4">
                        <a href="logout.php" class="btn btn-danger btn-lg">Выйти</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-black text-white text-center py-4">
        <div class="container conf">
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