@extends('layouts.app')

@section('title', 'Жанр: ' . $genre->name . ' - Читать книги онлайн | ' . config('app.name'))
@section('description', 'Лучшие книги в жанре ' . $genre->name . '. Обширная библиотека произведений, доступных для чтения онлайн.')

@section('content')
    <div class="container py-3">
        <!-- Filters/Categories Links -->
        <div class="d-flex justify-content-center gap-3 mb-3 flex-wrap animate-fade-in-up">
            <a href="{{ route('genres.show', ['slug' => $genre->slug, 'sort' => 'latest']) }}"
                class="btn {{ request('sort', 'latest') === 'latest' ? 'btn-primary' : 'btn-outline-light' }} rounded-pill px-4">Новые</a>
            <a href="{{ route('genres.show', ['slug' => $genre->slug, 'sort' => 'popular']) }}"
                class="btn {{ request('sort') === 'popular' ? 'btn-primary' : 'btn-outline-light' }} rounded-pill px-4">Популярные</a>
            <a href="{{ route('genres.show', ['slug' => $genre->slug, 'sort' => 'commented']) }}"
                class="btn {{ request('sort') === 'commented' ? 'btn-primary' : 'btn-outline-light' }} rounded-pill px-4">Комментируемые</a>
        </div>

        @if(request('sort') === 'popular')
            <div class="d-flex justify-content-center gap-2 mb-3 animate-fade-in-up delay-150" style="margin-top: -0.5rem;">
                <a href="{{ route('genres.show', ['slug' => $genre->slug, 'sort' => 'popular', 'period' => 'week']) }}"
                    class="btn {{ $period === 'week' ? 'btn-primary' : 'btn-outline-light' }} px-3 rounded-pill btn-sm">За
                    неделю</a>
                <a href="{{ route('genres.show', ['slug' => $genre->slug, 'sort' => 'popular', 'period' => 'month']) }}"
                    class="btn {{ $period === 'month' ? 'btn-primary' : 'btn-outline-light' }} px-3 rounded-pill btn-sm">За
                    месяц</a>
                <a href="{{ route('genres.show', ['slug' => $genre->slug, 'sort' => 'popular', 'period' => 'all']) }}"
                    class="btn {{ $period === 'all' ? 'btn-primary' : 'btn-outline-light' }} px-3 rounded-pill btn-sm">За
                    все время</a>
            </div>
        @endif

        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-3 animate-fade-in-up delay-100">
            <div>
                <h1 class="display-5 fw-bold text-white mb-0">{{ $genre->name }}</h1>
                <div class="text-muted small mt-2">
                    Найдено {{ $books->total() }} {{ trans_choice('книга|книги|книг', $books->total()) }}
                </div>
            </div>
        </div>

        <!-- Book List -->
        @if($books->count() > 0)
            <div
                class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 row-cols-xl-6 g-3 animate-fade-in-up delay-100">
                @foreach($books as $book)
                    <div class="col">
                        <x-book-card-vertical :book="$book" />
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-5 d-flex justify-content-center">
                {{ $books->links() }}
            </div>
        @else
            <div class="text-center py-5 text-muted bg-dark-card rounded-4 border border-white-10 animate-fade-in-up">
                <i class="bi bi-book-half fs-1 mb-3 d-block text-white-50"></i>
                <h4 class="text-white">Здесь пока пусто</h4>
                <p>В этом жанре еще нет опубликованных книг.</p>
                <a href="{{ route('genres.index') }}" class="btn btn-outline-primary rounded-pill px-4 mt-3">
                    Вернуться к жанрам
                </a>
            </div>
        @endif
    </div>

    <style>
        /* Filter buttons with primary border - only for outline variants */
        .btn-outline-light {
            border: 1px solid var(--bs-primary) !important;
            color: var(--bs-primary) !important;
        }

        /* Primary buttons already have background, no need for special border */
        .btn-primary {
            border: 1px solid var(--bs-primary) !important;
            color: white !important;
        }

        .backdrop-blur {
            backdrop-filter: blur(4px);
        }

        .duration-300 {
            transition-duration: 0.3s;
        }

        .duration-500 {
            transition-duration: 0.5s;
        }

        .group:hover .group-hover\:scale-105 {
            transform: scale(1.05);
        }

        .group:hover .group-hover\:opacity-100 {
            opacity: 1;
        }

        .group-hover\:transform-none {
            transform: translateY(0) !important;
        }

        .transform-y-2 {
            transform: translateY(10px);
        }

        .delay-75 {
            transition-delay: 0.075s;
        }

        .delay-100 {
            transition-delay: 0.1s;
        }

        .book-cover-container {
            background: #1e293b;
        }
    </style>
@endsection