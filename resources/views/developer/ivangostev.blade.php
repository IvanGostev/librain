<!DOCTYPE html>
<html lang="ru" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Ivan Gostev - Full Stack Developer</title>


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">


    @vite(['resources/sass/app.scss'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #050505;
            color: #e5e5e5;
            overflow-x: hidden;
        }

        .hero-section {
            position: relative;
            padding: 12rem 0 8rem;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.02) 0%, transparent 100%);
        }

        .strict-card {
            background: #111111;
            border: 1px solid #333;
            border-radius: 0;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }

        .strict-card:hover {
            transform: translateY(-2px);
            border-color: #666;
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            background: #1a1a1a;
            border: 1px solid #333;
            color: #fff;
        }

        .btn-strict {
            border-radius: 0;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.75rem;
            transition: all 0.2s;
            padding: 1rem 2rem;
        }

        .btn-strict-primary {
            background-color: #fff;
            color: #000;
            border: 1px solid #fff;
        }

        .btn-strict-primary:hover {
            background-color: transparent;
            color: #fff;
        }

        .btn-strict-outline {
            background: transparent;
            color: #fff;
            border: 1px solid #333;
        }

        .btn-strict-outline:hover {
            border-color: #fff;
            color: #fff;
        }

        .navbar-custom {
            background: rgba(5, 5, 5, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #222;
        }

        .bg-grid {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            pointer-events: none;
            z-index: -1;
        }

        h1,
        h2,
        h3,
        h4,
        .lead {
            letter-spacing: -0.03em;
        }
    </style>
</head>

<body>

    <div class="bg-grid"></div>


    <nav class="navbar navbar-expand-lg fixed-top navbar-custom py-3">
        <div class="container">
            <a class="navbar-brand fw-bold text-white fs-5 tracking-tight text-uppercase" href="#"
                style="letter-spacing: 2px;">
                IvanGostev
            </a>
            <div class="ms-auto">
                <a href="https://t.me/ivangostevdeveloper" target="_blank"
                    class="btn btn-strict btn-strict-primary fw-bold py-2 px-4">
                    Связаться
                </a>
            </div>
        </div>
    </nav>


    <section class="hero-section text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <span class="text-uppercase tracking-wider small fw-bold text-white-50 mb-4 d-block"
                        style="letter-spacing: 3px;">
                        Full Stack Development
                    </span>
                    <h1 class="display-3 fw-bolder mb-4 text-white text-uppercase" style="letter-spacing: -2px;">
                        Создаю высокопроизводительные <br> веб-приложения
                    </h1>
                    <p class="lead text-white-50 mb-5 fs-5" style="max-width: 600px; margin: 0 auto;">
                        Пример работы — проект <strong>Librain</strong>. <br>
                        Цифровая библиотека с уникальным дизайном и мощным функционалом.
                    </p>
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="https://t.me/ivangostevdeveloper" target="_blank"
                            class="btn btn-strict btn-strict-primary fw-bold">
                            Заказать проект
                        </a>
                        <a href="#features" class="btn btn-strict btn-strict-outline fw-bold">
                            Узнать больше
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section id="features" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-6 fw-bold mb-3 text-uppercase">О проекте Librain</h2>
                <p class="text-white-50">Ключевые особенности реализованного решения</p>
            </div>

            <div class="row g-4">

                <div class="col-md-4">
                    <div class="strict-card p-4 h-100">
                        <div class="feature-icon">
                            <i class="bi bi-palette"></i>
                        </div>
                        <h4 class="fw-bold mb-3 text-uppercase fs-6">UI/UX Дизайн</h4>
                        <p class="text-white-50 mb-0 small">
                            Адаптивный интерфейс с использованием Glassmorphism. Темная тема, плавные анимации и
                            интуитивно понятная навигация.
                        </p>
                    </div>
                </div>


                <div class="col-md-4">
                    <div class="strict-card p-4 h-100">
                        <div class="feature-icon">
                            <i class="bi bi-lightning-charge"></i>
                        </div>
                        <h4 class="fw-bold mb-3 text-uppercase fs-6">Скорость и SEO</h4>
                        <p class="text-white-50 mb-0 small">
                            Оптимизированная архитектура на Laravel. Кеширование, быстрый рендеринг (Blade),
                            SEO-подготовка.
                        </p>
                    </div>
                </div>


                <div class="col-md-4">
                    <div class="strict-card p-4 h-100">
                        <div class="feature-icon">
                            <i class="bi bi-book"></i>
                        </div>
                        <h4 class="fw-bold mb-3 text-uppercase fs-6">Читалка Онлайн</h4>
                        <p class="text-white-50 mb-0 small">
                            Встроенный ридер для TXT, FB2, EPUB. Сохранение прогресса, настройки шрифта и тем.
                        </p>
                    </div>
                </div>


                <div class="col-md-4">
                    <div class="strict-card p-4 h-100">
                        <div class="feature-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        <h4 class="fw-bold mb-3 text-uppercase fs-6">Личный Кабинет</h4>
                        <p class="text-white-50 mb-0 small">
                            Система авторизации, избранное, история чтения, рецензии и рейтинги книг.
                        </p>
                    </div>
                </div>


                <div class="col-md-4">
                    <div class="strict-card p-4 h-100">
                        <div class="feature-icon">
                            <i class="bi bi-hdd-network"></i>
                        </div>
                        <h4 class="fw-bold mb-3 text-uppercase fs-6">Парсинг и Импорт</h4>
                        <p class="text-white-50 mb-0 small">
                            Автоматизированные инструменты для импорта книг, обработки метаданных и обложек.
                        </p>
                    </div>
                </div>


                <div class="col-md-4">
                    <div class="strict-card p-4 h-100">
                        <div class="feature-icon">
                            <i class="bi bi-code-slash"></i>
                        </div>
                        <h4 class="fw-bold mb-3 text-uppercase fs-6">Чистый Код</h4>
                        <p class="text-white-50 mb-0 small">
                            Laravel 10+, Bootstrap 5, Vanilla JS. Модульная структура, готовая к масштабированию.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="py-5">
        <div class="container">
            <div class="strict-card p-5 text-center position-relative overflow-hidden">
                <h2 class="fw-bold mb-4 position-relative z-1 text-uppercase">Готовы обсудить ваш проект?</h2>
                <p class="lead text-white-50 mb-5 position-relative z-1 fs-6" style="max-width: 600px; margin: 0 auto;">
                    Я открыт для новых вызовов. Давайте создадим что-то уникальное вместе.
                </p>
                <a href="https://t.me/ivangostevdeveloper" target="_blank"
                    class="btn btn-strict btn-strict-primary fw-bold px-5 z-1 position-relative">
                    Написать мне
                </a>
            </div>
        </div>
    </section>


    <footer class="py-4 text-center text-muted border-top border-white-10" style="border-color: #222 !important;">
        <div class="container">
            <p class="mb-0 small text-uppercase" style="letter-spacing: 1px;">&copy; {{ date('Y') }} Ivan Gostev.
                Development & Design.</p>
        </div>
    </footer>

</body>

</html>