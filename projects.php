<?php
session_start(); 
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_for_project'])) {
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user_type'] !== 'volunteer') {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Для отклика на проект необходимо войти как волонтер.'];
        header('Location: login.php');
        exit();
    }

    $project_id = $_POST['project_id'];
    $volunteer_id = $_SESSION['user_id'];

    try {
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM volunteer_project_applications WHERE volunteer_id = ? AND project_id = ?");
        $stmtCheck->execute([$volunteer_id, $project_id]);
        if ($stmtCheck->fetchColumn() > 0) {
            $_SESSION['message'] = ['type' => 'warning', 'text' => 'Вы уже откликнулись на этот проект.'];
        } else {
            $stmtInsert = $pdo->prepare("INSERT INTO volunteer_project_applications (volunteer_id, project_id, status) VALUES (?, ?, 'pending')");
            $stmtInsert->execute([$volunteer_id, $project_id]);

            $_SESSION['message'] = ['type' => 'success', 'text' => 'Ваш отклик на проект успешно отправлен!'];
        }
    } catch (PDOException $e) {
        error_log("Database error applying for project: " . $e->getMessage());
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Ошибка при отправке отклика: ' . $e->getMessage()];
    }

    header('Location: projects.php');
    exit();
}

try {
    $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error retrieving projects: " . $e->getMessage());
    $projects = [];
}

$categories = [
    'all' => 'Все проекты',
    'eco' => 'Экология',
    'children' => 'Дети',
    'charity' => 'Благотворительность',
    'education' => 'Образование',
    'medicine' => 'Медицина', 
    'other' => 'Другое' 
];

$is_logged_in = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$current_user_type = $_SESSION['user_type'] ?? null; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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

    .background-container,
    section.py-4,
    section.py-5,
    section:last-of-type 
    {
        flex-grow: 1;
    }

    footer {
        width: 100%;
        flex-shrink: 0;
    }

    .background-container {
        position: relative;
        height: 30vh;
        overflow: hidden;
    }
    .background-layer {
        position: absolute;
        top: -10%;
        left: -10%;
        width: 120%;
        height: 120%;
        background: 
            linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
            url('/img/project.jpg');
        background-size: cover;
        background-position: center;
        transition: transform 0.3s ease;
    }
    .centered-text {
        position: absolute;
        bottom: 35%;
        left: 50%;
        transform: translateX(-50%);
        font-size: 2.5rem;
        color: white;
        text-align: center;
        max-width: 800px;
    }

    .filter-btn {
        border-radius: 30px;
        padding: 8px 20px;
    }
    .filter-btn.active {
        background-color: #0d6efd;
        color: white;
    }

    .project-card img {
        height: 250px;
        object-fit: cover;
        border-radius: 8px;
    }

    @media (max-width: 768px) {
        .centered-text {
            bottom: 30%;
            font-size: 2rem;
        }
        .filter-group {
            flex-wrap: wrap;
            justify-content: center;
        }
    }


        .background-container {
            position: relative;
            height: 50vh;
            overflow: hidden;
        }
        .background-layer {
            position: absolute;
            top: -10%;
            left: -10%;
            width: 120%;
            height: 120%;
            background: 
                linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
                url('/img/project.jpg');
            background-size: cover;
            background-position: center;
            transition: transform 0.3s ease;
        }
        .centered-text {
            position: absolute;
            bottom: 35%;
            left: 50%;
            transform: translateX(-50%);
            font-size: 2.5rem;
            color: white;
            text-align: center;
            max-width: 800px;
        }

        .filter-btn {
            border-radius: 30px;
            padding: 8px 20px;
        }
        .filter-btn.active {
            background-color: #0d6efd;
            color: white;
        }

        .project-card img {
            height: 250px;
            object-fit: cover;
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .centered-text {
                bottom: 30%;
                font-size: 2rem;
            }
            .filter-group {
                flex-wrap: wrap;
                justify-content: center;
            }
        }
    </style>
    <title>Наши проекты | Do Goodness</title>
</head>
<body>
<?php
require_once 'header.php'
?>
    <section>
        <div class="background-container">
            <div class="background-layer"></div>
            <div class="centered-text">
                Наши ключевые проекты
            </div>
        </div>
    </section>

    <section class="py-4 bg-light">
        <div class="container">
            <div class="d-flex filter-group gap-2 justify-content-center">
                <button class="btn filter-btn active" data-filter="all">Все проекты</button>
                <?php foreach ($categories as $key => $value): ?>
                    <?php if ($key !== 'all'): ?>
                        <button class="btn filter-btn" data-filter="<?php echo htmlspecialchars($key); ?>">
                            <?php echo htmlspecialchars($value); ?>
                        </button>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row g-4 project-grid">
                <?php if (!empty($projects)): ?>
                    <?php foreach ($projects as $project): ?>
                        <div class="col-md-4 project-card" data-category="<?php echo htmlspecialchars($project['category']); ?>">
                            <div class="card shadow-sm h-100">
                                <img src="<?php echo htmlspecialchars($project['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($project['title']); ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($project['title']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars($project['description']); ?></p>
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#projectModal<?php echo $project['id']; ?>">Подробнее</button>

                                    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && $_SESSION['user_type'] === 'volunteer'): ?>
                                        <?php
                                        $hasApplied = false;
                                        if (isset($pdo)) { 
                                            try {
                                                $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM volunteer_project_applications WHERE volunteer_id = ? AND project_id = ?");
                                                $stmtCheck->execute([$_SESSION['user_id'], $project['id']]);
                                                $hasApplied = ($stmtCheck->fetchColumn() > 0);
                                            } catch (PDOException $e) {
                                                error_log("Error checking application status: " . $e->getMessage());
                                            }
                                        }
                                        ?>
                                        <form action="projects.php" method="POST" class="d-inline-block ms-2">
                                            <input type="hidden" name="apply_for_project" value="1">
                                            <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                            <?php if ($hasApplied): ?>
                                                <button type="submit" class="btn btn-secondary btn-sm" disabled>Откликнулись</button>
                                            <?php else: ?>
                                                <button type="submit" class="btn btn-success btn-sm">Откликнуться</button>
                                            <?php endif; ?>
                                        </form>
                                    <?php elseif (!isset($_SESSION['loggedin']) || $_SESSION['user_type'] !== 'organization'): ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p class="lead">На данный момент проектов нет. Заходите позже!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section>
        <?php foreach ($projects as $project): ?>
            <div class="modal fade" id="projectModal<?php echo $project['id']; ?>" tabindex="-1" aria-labelledby="projectModal<?php echo $project['id']; ?>Label" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="projectModal<?php echo $project['id']; ?>Label"><?php echo htmlspecialchars($project['title']); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                        </div>
                        <div class="modal-body">
                            <?php if (!empty($project['modal_img_url'])): ?>
                                <img src="<?php echo htmlspecialchars($project['modal_img_url']); ?>" class="img-fluid mb-3" alt="Фото проекта">
                            <?php endif; ?>
                            <?php if (!empty($project['goal'])): ?>
                                <h6><i class="bi bi-bullseye text-primary me-2"></i>Цель проекта:</h6>
                                <p><?php echo nl2br(htmlspecialchars($project['goal'])); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($project['tasks'])): ?>
                                <h6><i class="bi bi-list-check text-primary me-2"></i>Задачи:</h6>
                                <ul>
                                    <?php
                                    $tasks_array = explode("\n", $project['tasks']);
                                    foreach ($tasks_array as $task) {
                                        if (!empty(trim($task))) {
                                            echo '<li>' . htmlspecialchars(trim($task)) . '</li>';
                                        }
                                    }
                                    ?>
                                </ul>
                            <?php endif; ?>
                            <?php if (!empty($project['how_to_join'])): ?>
                                <h6><i class="bi bi-person-check text-primary me-2"></i>Как присоединиться:</h6>
                                <p><?php echo nl2br(htmlspecialchars($project['how_to_join'])); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="modal-footer">
                            <a href="join.php" class="btn btn-primary">Стать волонтером</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </section>

<?php
require_once 'footer.php'
?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>

        document.querySelector('.background-container').addEventListener('mousemove', function(e) {
            const bgLayer = this.querySelector('.background-layer');
            const rect = this.getBoundingClientRect();
            
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const maxX = rect.width * 0.1;
            const maxY = rect.height * 0.1;
            
            const moveX = Math.max(-maxX, Math.min(maxX, (x / rect.width - 0.5) * 20));
            const moveY = Math.max(-maxY, Math.min(maxY, (y / rect.height - 0.5) * 20));

            bgLayer.style.transform = `translate(${moveX}px, ${moveY}px)`;
        });

        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                const filter = btn.getAttribute('data-filter');
                const projects = document.querySelectorAll('.project-card');

                projects.forEach(project => {
                    if (filter === 'all' || project.getAttribute('data-category') === filter) {
                        project.style.display = 'block';
                    } else {
                        project.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>