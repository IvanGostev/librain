@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card bg-dark-card border-white-10 p-5 rounded-4 shadow-lg text-center animate-fade-in-up">

                    <!-- Step 1: Timer -->
                    <div id="download-step-1">
                        <div class="mb-4">
                            <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/no-cover.svg') }}"
                                alt="{{ $book->title }}" class="rounded shadow-lg mb-4"
                                style="width: 120px; height: 180px; object-fit: cover;">
                            <h1 class="h3 fw-bold text-white mb-2">{{ $book->title }}</h1>
                            <p class="text-white-50 mb-0">Автор: {{ $book->author->name }}</p>
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

                    <!-- Step 2: Download Button -->
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
    </div>

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