<?php
session_start();
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
            background: 
                linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
                url('/img/onas-behind.webp');
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
        .history-year {
            font-size: 3rem;
            color: #0d6efd;
            font-weight: bold;
        }
        .team-card img {
            height: 300px;
            object-fit: cover;
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .centered-text {
                bottom: 30%;
                font-size: 2rem;
            }
            .history-year {
                font-size: 2.5rem;
            }
        }
    </style>
    <title>О нас | Do Goodness</title>
</head>
<body>
<?php
require_once 'header.php'
?>
    <section>
        <div class="background-container">
            <div class="background-layer"></div>
            <div class="centered-text">
                Наша история и цели
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Как всё начиналось</h2>
            <div class="row align-items-center">
                <div class="col-md-6">
                    <img src="/img/onas-info.jpg " alt="Основатели" class="img-fluid rounded shadow">
                </div>
                <div class="col-md-6 mt-4 mt-md-0">
                    <h4 class="history-year">2020</h4>
                    <p class="lead">Do Goodness был создан студентами МГУ, которые хотели объединить молодежь для помощи нуждающимся.</p>
                    <p>Первый проект стартовал с благотворительной ярмарки, собравшей 50 000 рублей для детского дома. С тех пор мы:</p>
                    <ul>
                        <li>Провели 150+ мероприятий</li>
                        <li>Привлекли 3000+ волонтеров</li>
                        <li>Помогли 50 детским домам и 200+ семьям</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-light py-5">
        <div class="container">
            <h2 class="text-center mb-5">Наши цели и ценности</h2>
            <div class="row g-4 text-center">
                <div class="col-md-4">
                    <div class="display-4 text-primary mb-3"><i class="bi bi-people"></i></div>
                    <h5>Объединение молодежи</h5>
                    <p>Мы даем возможность молодым людям найти единомышленников и развивать лидерские качества.</p>
                </div>
                <div class="col-md-4">
                    <div class="display-4 text-primary mb-3"><i class="bi bi-globe"></i></div>
                    <h5>Экологическое сознание</h5>
                    <p>Проводим образовательные мероприятия и акции по защите окружающей среды.</p>
                </div>
                <div class="col-md-4">
                    <div class="display-4 text-primary mb-3"><i class="bi bi-heart"></i></div>
                    <h5>Социальная помощь</h5>
                    <p>Организуем сбор средств, вещей и времени для тех, кто в них нуждается.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Наша команда</h2>
            <div class="row g-4">
                <div class="col-md-4 team-card">
                    <img src="/img/nastiks.jpg " class="card-img-top" alt="Анастасия">
                    <div class="mt-3">
                        <h5>Анастасия Иванова</h5>
                        <p class="text-muted mb-1">Основатель организации</p>
                        <p>Лидер, вдохновляющий тысячи. Люблю помогать людям и организовывать масштабные акции.</p>
                    </div>
                </div>
                <div class="col-md-4 team-card">
                    <img src="/img/maxim.jpg" class="card-img-top" alt="Максим">
                    <div class="mt-3">
                        <h5>Максим Петров</h5>
                        <p class="text-muted mb-1">Координатор проектов</p>
                        <p>Организую логистику мероприятий и контролирую выполнение задач. Всегда на связи.</p>
                    </div>
                </div>
                <div class="col-md-4 team-card">
                    <img src="/img/ekat.jpg" class="card-img-top" alt="Екатерина">
                    <div class="mt-3">
                        <h5>Екатерина Смирнова</h5>
                        <p class="text-muted mb-1">Руководитель волонтеров</p>
                        <p>Обучаю новых участников и поддерживаю их на каждом этапе. Всегда улыбаюсь!</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php
require_once 'footer.php'
?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
        
    </script>
</body>
</html>