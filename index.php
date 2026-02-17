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
    <title>Главная | Do Goodness</title>
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
                url('/img/volonterskiy_proryv.jpeg');
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

        .card-img-top {
            width: 100%; 
            aspect-ratio: 16/9; 
            object-fit: cover;
            border-radius: 8px 8px 0 0; 
        }
    </style>
</head>
<body>
<?php
require_once 'header.php'
?>

    <section>
        <div class="background-container">
            <div class="background-layer"></div>
            <div class="centered-text">
                Добрые дела - наше все
            </div>
        </div>
    </section>

    <section id="about" class="bg-light py-5">
        <div class="container">
            <h2 class="section-title text-center mb-5">Мы объединяем молодежь для добрых дел</h2>
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="lead fw-normal">
                        Наша организация помогает молодым людям находить возможности для участия в социальных проектах, экологических акциях и благотворительности.
                    </h2>
                    <a href="about.php" class="btn btn-primary btn-lg mt-3">Узнать больше</a>
                </div>
                <div class="col-md-6">
                    <img src="/img/volonteri.png" alt="Группа волонтеров" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </section>

    <section id="projects" class="py-5 mb-5">
        <div class="container">
            <h2 class="section-title text-center mb-5">Наши ключевые проекты</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 shadow">
                        <img src="/img/76f97b40-ab0a-59b2-a8a2-91405ffd81eb.jpg" alt="" class="card-img-top">
                        <div class="card-body">
                            <h5 class="card-title">Экологические акции</h5>
                            <p class="card-text">Уборка парков, сортировка мусора, образовательные программы</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow">
                        <img src="/img/a6922ff8cfad5d7f36649c0f9c77253b_XL.jpg" alt="" class="card-img-top h-100">
                        <div class="card-body">
                            <h5 class="card-title">Помощь детям</h5>
                            <p class="card-text">Поддержка детских домов, организация мероприятий, творческие мастер-классы</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow">
                        <img src="/img/115664-2022223-6692-148l9nv.kbb2l.jpg" alt="" class="card-img-top h-100">
                        <div class="card-body">
                            <h5 class="card-title">Благотворительность</h5>
                            <p class="card-text">Сбор средств, помощь малообеспеченным семьям, организация благотворительных марафонов</p>
                        </div>
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

            const moveX = (x / rect.width - 0.5) * 10;  
            const moveY = (y / rect.height - 0.5) * 10; 

            const maxX = rect.width * 0.05;
            const maxY = rect.height * 0.05;
            
            bgLayer.style.transform = `translate(
                ${Math.max(-maxX, Math.min(maxX, moveX))}px,
                ${Math.max(-maxY, Math.min(maxY, moveY))}px
            )`;
        });
    </script>

</body>
</html>