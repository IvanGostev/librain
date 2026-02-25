@extends('layouts.app')
@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card bg-dark-card border-white-10 p-5 rounded-4 shadow-lg text-center animate-fade-in-up">
                    <div id="download-step-1">
                        <div class="mb-4">
                            <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/no-cover.svg') }}"
                                alt="{{ $book->title }}" class="rounded shadow-lg mb-4"
                                style="width: 120px; height: 180px; object-fit: cover;">
                            <h1 class="h3 fw-bold text-white mb-2">{{ $book->title }}</h1>
                            <p class="text-white-50 mb-0">Автор: {{ $book->authors->isNotEmpty() ? $book->authors->pluck('name')->join(', ') : 'Неизвестен' }}</p>
                        </div>
                        <div class="py-4" id="timer-container">
                            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                                <span class="visually-hidden">Загрузка...</span>
                            </div>
                            <h4 class="text-white mb-2">Подготовка файла...</h4>
                            <p class="text-muted">Пожалуйста, подождите <span id="timer"
                                    class="text-primary fw-bold">15</span> сек.</p>
                        </div>
                    </div>
                    <div id="download-step-2" class="d-none">
                        <div class="mb-5">
                            <i class="bi bi-file-earmark-check text-success" style="font-size: 5rem;"></i>
                        </div>
                        <h2 class="text-white mb-3">Файл готов!</h2>
                        <p class="text-white-50 mb-4">
                            Книга: <span class="text-white">{{ $book->title }}</span><br>
                            Формат: <span class="text-uppercase fw-bold text-primary">{{ $format }}</span><br>
                            Размер: <span class="text-white">{{ $formattedSize }}</span>
                        </p>
                        <a href="{{ $fileUrl }}"
                            class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow-glow hover-elevate w-100" download>
                            <i class="bi bi-download me-2"></i> Нажмите, чтобы скачать
                        </a>
                        <div class="mt-4">
                            <a href="{{ route('books.show', ['genre' => $book->genres->first()->slug ?? 'general', 'slug' => $book->slug]) }}"
                                class="text-muted text-decoration-none small hover-text-white">
                                <i class="bi bi-arrow-left me-1"></i> Вернуться к книге
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-5 pt-5">
            <section>
                @if($genres->count() > 0)
                    <div class="row g-4 animate-fade-in-up delay-200">
                        @foreach($genres as $genre)
                            <div class="col-6 col-md-4 col-lg-3">
                                <a href="{{ route('genres.show', $genre->slug) }}" class="text-decoration-none group">
                                    <article
                                        class="card bg-dark-card border-0 h-100 shadow-sm hover-card-lift position-relative overflow-hidden">
                                        <div class="card-body p-4 text-center d-flex flex-column align-items-center justify-content-center"
                                            style="min-height: 180px;">
                                            <div class="position-absolute top-50 start-50 translate-middle rounded-circle bg-primary opacity-10"
                                                style="width: 120px; height: 120px; filter: blur(20px);"></div>
                                            <div class="position-relative z-1">
                                                <p class="h4 fw-bold genre-title mb-2 group-hover:text-primary transition-colors">
                                                    {{ $genre->name }}
                                                </p>
                                                <span
                                                    class="badge bg-white bg-opacity-10 text-white rounded-pill border border-white-10 px-3"
                                                    style="color: white !important;">
                                                    {{ $genre->books_count }}
                                                    {{ trans_choice('книга|книги|книг', $genre->books_count) }}
                                                </span>
                                            </div>
                                        </div>
                                    </article>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </div>
    <style>
        .transition-colors {
            transition: color 0.3s ease;
        }
        .group:hover .group-hover\:text-primary {
            color: var(--bs-primary) !important;
        }
        /* Genre title color - always white */
        .genre-title {
            color: white !important;
        }
        /* Override for light theme to keep it white */
        [data-bs-theme="light"] .genre-title {
            color: white !important;
        }
        [data-bs-theme="light"] .group:hover .group-hover\:text-primary {
            color: var(--bs-primary) !important;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let timeLeft = 15;
            const timerEl = document.getElementById('timer');
            const step1 = document.getElementById('download-step-1');
            const step2 = document.getElementById('download-step-2');
            const countdown = setInterval(() => {
                timeLeft--;
                if (timerEl) timerEl.textContent = timeLeft;
                if (timeLeft <= 0) {
                    clearInterval(countdown);
                    if (step1) step1.classList.add('d-none');
                    if (step2) step2.classList.remove('d-none');
                }
            }, 1000);
        });
    </script>
@endsection
