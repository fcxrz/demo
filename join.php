<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
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
                url('/img/i.webp');
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

        .step-icon {
            font-size: 2.5rem;
            color: #0d6efd;
            margin-bottom: 10px;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        @media (max-width: 768px) {
            .centered-text {
                bottom: 30%;
                font-size: 2rem;
            }
            .step-card {
                text-align: center;
            }
        }
    </style>
    <title>Стать волонтером | Do Goodness</title>
</head>
<body>
<?php
require_once 'header.php'
?>

    <section>
        <div class="background-container">
            <div class="background-layer"></div>
            <div class="centered-text">
                Как стать волонтером?
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">4 простых шага, чтобы начать помогать</h2>
            <div class="row g-4 text-center">
                <div class="col-md-3 step-card">
                    <div class="step-icon"><i class="bi bi-check-circle-fill"></i></div>
                    <h5>Выбери проект</h5>
                    <p>Изучите доступные направления и выберите то, что близко вам по духу</p>
                </div>
                <div class="col-md-3 step-card">
                    <div class="step-icon"><i class="bi bi-pencil-square"></i></div>
                    <h5>Заполни форму</h5>
                    <p>Укажите свои данные и направление, в котором хотите участвовать</p>
                </div>
                <div class="col-md-3 step-card">
                    <div class="step-icon"><i class="bi bi-book"></i></div>
                    <h5>Пройди обучение</h5>
                    <p>Мы проведем короткое обучение, чтобы вы были готовы к работе</p>
                </div>
                <div class="col-md-3 step-card">
                    <div class="step-icon"><i class="bi bi-heart-pulse"></i></div>
                    <h5>Начни помогать</h5>
                    <p>Присоединяйтесь к нашей команде и меняйте мир вместе с нами!</p>
                </div>
            </div>
        </div>
    </section>


    <section class="bg-light py-5">
        <div class="container">
            <h2 class="text-center mb-5">Часто задаваемые вопросы</h2>
            <div class="accordion" id="faqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                            Сколько времени занимает обучение?
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body bg-body-secondary">
                            Обучение занимает от 2 до 4 часов в зависимости от выбранного направления. Мы стараемся сделать его максимально эффективным и интересным.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                            Можно ли участвовать удаленно?
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body bg-body-secondary">
                            Да, некоторые проекты предполагают удаленное участие. Например, перевод документов, создание контента, онлайн-обучение. Это будет указано в описании каждого проекта.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                            Какие документы нужны?
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body bg-body-secondary">
                            Для участия вам понадобятся:
                            <ul>
                                <li>Паспорт (для подтверждения личности)</li>
                                <li>Медицинская справка (требуется для некоторых проектов)</li>
                                <li>Согласие на обработку персональных данных</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container">
            <h2 class="text-center mb-4">Присоединяйтесь к нам!</h2>
            <p class="text-center mb-5">Выберите, как вы хотите помочь:</p>
            <div class="d-flex justify-content-center gap-4 mb-5">
                <button class="btn btn-primary btn-lg" id="showVolunteerForm">Я хочу стать волонтером</button>
                <button class="btn btn-outline-primary btn-lg" id="showOrganizationForm">Я представляю организацию</button>
            </div>

            <div id="volunteerFormSection" class="form-section">
                <h2 class="text-center mb-5">Регистрация волонтера</h2>
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <form class="needs-validation" action="submit_volunteer_application.php" method="POST" novalidate>
                            <div class="mb-3">
                                <label for="volunteerName" class="form-label">Имя</label>
                                <input type="text" class="form-control" id="volunteerName" name="name" placeholder="Ваше имя" required>
                                <div class="invalid-feedback">Пожалуйста, введите ваше имя.</div>
                            </div>

                            <div class="mb-3">
                                <label for="volunteerPhone" class="form-label">Телефон</label>
                                <input type="tel" class="form-control" id="volunteerPhone" name="phone" placeholder="+7 (999) 123-45-67" required>
                                <div class="invalid-feedback">Введите ваш телефон.</div>
                            </div>

                            <div class="mb-3">
                                <label for="volunteerAge" class="form-label">Возраст</label>
                                <input type="number" class="form-control" id="volunteerAge" name="age" min="14" max="99" required placeholder="Укажите возраст (от 14 лет)">
                                <div class="invalid-feedback">Укажите возраст (от 14 лет).</div>
                            </div>

                            <div class="mb-3">
                                <label for="volunteerCity" class="form-label">Город</label>
                                <input type="text" class="form-control" id="volunteerCity" name="city" required placeholder="Москва">
                                <div class="invalid-feedback">Введите ваш город.</div>
                            </div>

                            <div class="mb-3">
                                <label for="volunteerMessage" class="form-label">Дополнительная информация</label>
                                <textarea class="form-control" id="volunteerMessage" name="message" rows="3" placeholder="Почему вы хотите присоединиться?"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="volunteerEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="volunteerEmail" name="email" placeholder="example@example.com" required>
                                <div class="invalid-feedback">Пожалуйста, введите корректный email.</div>
                            </div>
                            <div class="mb-3">
                                <label for="volunteerPassword" class="form-label">Пароль</label>
                                <input type="password" class="form-control" id="volunteerPassword" name="password" required>
                                <div class="invalid-feedback">Пожалуйста, введите пароль.</div>
                            </div>
                            <div class="mb-3">
                                <label for="volunteerDirection" class="form-label">Выберите направление</label>
                                <select class="form-select" id="volunteerDirection" name="direction" required>
                                    <option selected disabled value="">Выберите...</option>
                                    <option>Экологические проекты</option>
                                    <option>Помощь детям</option>
                                    <option>Благотворительность</option>
                                    <option>Образование</option>
                                    <option>Медицина</option>
                                    <option>Другое</option>
                                </select>
                                <div class="invalid-feedback">Пожалуйста, выберите направление.</div>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" name="volunteerCheckbox" id="volunteerAgree" required>
                                <label class="form-check-label" for="volunteerAgree">Я согласен с <a href="$">политикой конфиденциальности</a></label>
                                <div class="invalid-feedback">Необходимо согласие c политикой конфиденциальности.</div>
                            </div>
                            <div class="d-grid">
                                <button class="btn btn-primary btn-lg" type="submit">Стать волонтером!</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div id="organizationFormSection" class="form-section d-none">
                <h2 class="text-center mb-5">Регистрация организации</h2>
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <form class="needs-validation" action="submit_organization_application.php" method="POST" novalidate>
                            <div class="mb-3">
                                <label for="organizationName" class="form-label">Название организации</label>
                                <input type="text" class="form-control" id="organizationName" name="organization_name" placeholder="Название вашей организации" required>
                                <div class="invalid-feedback">Пожалуйста, введите название вашей организации.</div>
                            </div>

                            <div class="mb-3">
                                <label for="contactPerson" class="form-label">Имя контактного лица</label>
                                <input type="text" class="form-control" id="contactPerson" name="contact_person" placeholder="Имя контактного лица" required>
                                <div class="invalid-feedback">Пожалуйста, введите имя контактного лица.</div>
                            </div>

                            <div class="mb-3">
                                <label for="organizationEmail" class="form-label">Email организации</label>
                                <input type="email" class="form-control" id="organizationEmail" name="email" placeholder="example@organization.com" required>
                                <div class="invalid-feedback">Пожалуйста, введите корректный email организации.</div>
                            </div>

                            <div class="mb-3">
                                <label for="organizationPassword" class="form-label">Пароль</label>
                                <input type="password" class="form-control" id="organizationPassword" name="password" required>
                                <div class="invalid-feedback">Пожалуйста, введите пароль.</div>
                            </div>

                            <div class="mb-3">
                                <label for="organizationPhone" class="form-label">Телефон организации</label>
                                <input type="tel" class="form-control" id="organizationPhone" name="phone" placeholder="+7 (999) 123-45-67">
                                <div class="invalid-feedback">Введите телефон организации.</div>
                            </div>

                            <div class="mb-3">
                                <label for="organizationDescription" class="form-label">Описание организации</label>
                                <textarea class="form-control" id="organizationDescription" name="description" rows="3" placeholder="Расскажите о вашей организации и её целях"></textarea>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" name="organizationCheckbox" id="organizationAgree" required>
                                <label class="form-check-label" for="organizationAgree">Я согласен с <a href="agreed.html">политикой конфиденциальности</a></label>
                                <div class="invalid-feedback">Необходимо согласие c политикой конфиденциальности.</div>
                            </div>
                            <div class="d-grid">
                                <button class="btn btn-primary btn-lg" type="submit">Зарегистрировать организацию</button>
                            </div>
                        </form>
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

        document.addEventListener('DOMContentLoaded', function() {
            const volunteerFormSection = document.getElementById('volunteerFormSection');
            const organizationFormSection = document.getElementById('organizationFormSection');
            const showVolunteerFormBtn = document.getElementById('showVolunteerForm');
            const showOrganizationFormBtn = document.getElementById('showOrganizationForm');

            volunteerFormSection.classList.remove('d-none');
            organizationFormSection.classList.add('d-none');
            showVolunteerFormBtn.classList.add('btn-primary');
            showVolunteerFormBtn.classList.remove('btn-outline-primary');
            showOrganizationFormBtn.classList.add('btn-outline-primary');
            showOrganizationFormBtn.classList.remove('btn-primary');

            showVolunteerFormBtn.addEventListener('click', function() {
                volunteerFormSection.classList.remove('d-none');
                organizationFormSection.classList.add('d-none');
                showVolunteerFormBtn.classList.add('btn-primary');
                showVolunteerFormBtn.classList.remove('btn-outline-primary');
                showOrganizationFormBtn.classList.add('btn-outline-primary');
                showOrganizationFormBtn.classList.remove('btn-primary');
            });

            showOrganizationFormBtn.addEventListener('click', function() {
                volunteerFormSection.classList.add('d-none');
                organizationFormSection.classList.remove('d-none');
                showOrganizationFormBtn.classList.add('btn-primary');
                showOrganizationFormBtn.classList.remove('btn-outline-primary');
                showVolunteerFormBtn.classList.add('btn-outline-primary');
                showVolunteerFormBtn.classList.remove('btn-primary');
            });

            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    } else {
                        const submitButton = event.submitter;
                        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Отправка...';
                        submitButton.disabled = true;
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        });
    </script>
</body>
</html>