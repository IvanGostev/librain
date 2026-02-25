@extends('layouts.app')
@section('title', 'Librain - Лучшая онлайн библиотека для чтения книг')
@section('description', 'Librain - огромный выбор книг всех жанров. Читайте онлайн бесплатно, сохраняйте в свою библиотеку и следите за любимыми авторами.')
@section('content')
    <div class="hero-section position-relative">
        <div class="position-absolute top-0 start-0 w-100 h-100 bg-darker"></div>
        <div class="position-absolute opacity-20 flare-1 pe-none" style="z-index: 2000;"></div>
        <div class="position-absolute opacity-20 flare-2 pe-none" style="z-index: 2000;"></div>
        <div class="container position-relative z-2 d-flex flex-column align-items-center text-center pt-0 mt-0">
            <span
                class="badge rounded-pill bg-white bg-opacity-10 text-white border border-white-10 px-3 py-2 mb-4 animate-fade-in-up">
                Библиотека нового поколения
            </span>
            <h1 class="display-2 fw-bold mb-4 text-white animate-fade-in-up delay-100">
                Погрузитесь в мир<br>
                <span class="text-transparent bg-clip-text bg-gradient-primary-to-secondary">бесконечных историй</span>
            </h1>
            <p class="lead text-light text-opacity-75 mb-5 mx-auto animate-fade-in-up delay-200"
                style="max-width: 700px; font-size: 1.25rem;">
                Обширная коллекция книг для настоящих ценителей чтения. Создайте свою виртуальную полку, следите за
                новинками и наслаждайтесь любимыми произведениями в любом месте.
            </p>
            <div class="d-flex flex-column flex-sm-row gap-3 animate-fade-in-up delay-300">
                <a href="{{ route('register') }}"
                    class="btn btn-primary btn-lg rounded-pill px-5 py-3 fw-semibold shadow-glow hover-elevate">
                    Начать читать бесплатно
                </a>
                <a href="{{ url('/catalog') }}"
                    class="btn btn-dark-glass btn-lg rounded-pill px-5 py-3 fw-semibold border border-white-10 hover-elevate">
                    Перейти в каталога
                </a>
            </div>
            <div class="mt-5 pt-4 d-flex justify-content-center gap-5 text-white-50 animate-fade-in-up delay-400 w-100">
                <div class="text-center">
                    <div class="h3 fw-bold text-white mb-0">{{ $booksCount }}</div>
                    <div class="small">Книг</div>
                </div>
                <div class="text-center">
                    <div class="h3 fw-bold text-white mb-0">{{ $authorsCount }}</div>
                    <div class="small">Авторов</div>
                </div>
            </div>
        </div>
    </div>
    <section class="section py-4 bg-body-tertiary position-relative" style="margin-top: -100px; z-index: 10;">
        <div class="container">
            <div class="row g-5">
                <div class="col-md-4">
                    <article class="card h-100 border-0 bg-feature-card shadow-sm hover-card-lift">
                        <div class="card-body p-4 text-center">
                            <div
                                class="icon-box text-primary mx-auto mb-4 bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-journal-bookmark-fill fs-3"></i>
                            </div>
                            <h3 class="fw-bold text-white mb-3">Личная библиотека</h3>
                            <p class="text-muted mb-0">
                                Удобная организация ваших книг. Отмечайте статус "Читаю", "Прочитал" или "Хочу
                                прочитать".
                            </p>
                        </div>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="card h-100 border-0 bg-feature-card shadow-sm hover-card-lift">
                        <div class="card-body p-4 text-center">
                            <div
                                class="icon-box text-info mx-auto mb-4 bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-eyeglasses fs-3"></i>
                            </div>
                            <h3 class="fw-bold text-white mb-3">Комфортное чтение</h3>
                            <p class="text-muted mb-0">
                                Настраиваемая читалка: меняйте шрифт, размер текста и тему для максимального удобства.
                            </p>
                        </div>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="card h-100 border-0 bg-feature-card shadow-sm hover-card-lift">
                        <div class="card-body p-4 text-center">
                            <div
                                class="icon-box text-success mx-auto mb-4 bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-heart-fill fs-3"></i>
                            </div>
                            <h3 class="fw-bold text-white mb-3">Избранное</h3>
                            <p class="text-muted mb-0">
                                Сохраняйте любимые книги, отмечайте то, что планируете прочитать, и ведите список уже
                                завершенных произведений.
                            </p>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>
    <style>
        .hero-section {
            min-height: 100vh;
            padding-top: 80px;
            background: #12151c;
        }
        .bg-clip-text {
            background-clip: text;
            -webkit-background-clip: text;
        }
        .text-transparent {
            color: transparent;
        }
        .bg-gradient-primary-to-secondary {
            background-image: linear-gradient(135deg, #0ea5e9 0%, #10b981 100%);
        }
        .flare-1 {
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.15) 0%, rgba(0, 0, 0, 0) 70%);
            top: -25%;
            left: -10%;
            filter: blur(60px);
        }
        .flare-2 {
            width: 700px;
            height: 700px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.1) 0%, rgba(0, 0, 0, 0) 70%);
            bottom: -10%;
            right: -10%;
            filter: blur(60px);
        }
        .bg-dark-card {
            background-color: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
        }
        .icon-box {
            width: 80px;
            height: 80px;
        }
        .btn-dark-glass {
            background: rgba(255, 255, 255, 0.05);
            color: white;
            transition: all 0.3s ease;
        }
        .btn-dark-glass:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-color: rgba(255, 255, 255, 0.2) !important;
        }
        .hover-card-lift {
            transition: transform 0.3s ease, background-color 0.3s ease;
        }
        .hover-card-lift:hover {
            transform: translateY(-5px);
            background-color: rgba(30, 41, 59, 0.8);
        }
        .shadow-glow {
            box-shadow: 0 0 25px rgba(14, 165, 233, 0.3);
            transition: box-shadow 0.3s ease;
        }
        .shadow-glow:hover {
            box-shadow: 0 0 35px rgba(14, 165, 233, 0.5);
        }
    </style>
@endsection