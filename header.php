<?php

if (session_status() == PHP_SESSION_NONE) { 
    session_start();
}

$is_logged_in = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$current_user_type = $_SESSION['user_type'] ?? null; 
$current_page = basename($_SERVER['PHP_SELF']);

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Do Goodness'; ?> | Личный кабинет</title>
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
            min-height: 100vh; 
            padding-top: 80px; 
        }
        @media (max-width: 991.98px) { 
            body {
                padding-top: 60px; 
            }
        }
        main { 
            flex-grow: 1;
        }
        footer {
            width: 100%;
            flex-shrink: 0;
        }
        .content-wrapper {
            flex-grow: 1;
            padding-bottom: 20px;
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

        .background-container {
            position: relative;
            height: 40vh;
            overflow: hidden;
        }
        .background-layer {
            position: absolute;
            top: -10%;
            left: -10%;
            width: 120%;
            height: 120%;
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
</head>
<body>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

<header id="header" class="fixed-top d-flex justify-content-center bg-black">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid gap-5">
            <a href="index.php" class="navbar-brand text-center mx-auto"> <span style="font-size: 1.5rem;">Do</span><br>
                <span style="font-size: 1.2rem;">Goodness</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent" aria-controls="navContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navContent">
                <ul class="navbar-nav me-auto mb-3 mb-lg-0 w-100 d-flex justify-content-center gap-3">
                    <li class="nav-item"><a href="index.php" class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Главная</a></li>
                    <li class="nav-item"><a href="about.php" class="nav-link <?php echo ($current_page == 'about.php') ? 'active' : ''; ?>">О нас</a></li>
                    <li class="nav-item"><a href="projects.php" class="nav-link <?php echo ($current_page == 'projects.php') ? 'active' : ''; ?>">Проекты</a></li>

                    <?php if ($is_logged_in): ?>
                        <?php if ($current_user_type === 'volunteer'): ?>
                            <li class="nav-item"><a href="lk_volunteer.php" class="nav-link <?php echo ($current_page == 'lk_volunteer.php') ? 'active' : ''; ?>">Личный кабинет (Волонтер)</a></li>
                        <?php elseif ($current_user_type === 'organization'): ?>
                            <li class="nav-item"><a href="lk_organization.php" class="nav-link <?php echo ($current_page == 'lk_organization.php') ? 'active' : ''; ?>">Личный кабинет (Организация)</a></li>
                        <?php endif; ?>
                        <li class="nav-item"><a href="logout.php" class="nav-link">Выйти</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a href="join.php" class="nav-link <?php echo ($current_page == 'join.php') ? 'active' : ''; ?>">Стать волонтером</a></li>
                        <li class="nav-item"><a href="login.php" class="nav-link <?php echo ($current_page == 'login.php') ? 'active' : ''; ?>">Войти</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>

<script src="/js/bootstrap.bundle.min.js"></script>