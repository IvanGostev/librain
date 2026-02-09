@extends('layouts.app')

@section('title', 'Жанр: ' . $genre->name . ' - Читать книги онлайн | ' . config('app.name'))
@section('description', 'Лучшие книги в жанре ' . $genre->name . '. Обширная библиотека произведений, доступных для чтения онлайн.')

@section('content')
    <div class="container py-5">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-5 animate-fade-in-up">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"
                                class="text-muted text-decoration-none">Главная</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('genres.index') }}"
                                class="text-muted text-decoration-none">Жанры</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">{{ $genre->name }}</li>
                    </ol>
                </nav>
                <h1 class="display-5 fw-bold text-white mb-0">{{ $genre->name }}</h1>
                <div class="text-muted small mt-2">
                    Найдено {{ $books->total() }} {{ trans_choice('книга|книги|книг', $books->total()) }}
                </div>
            </div>

            <!-- Filter/Sort -->
            <div class="d-none d-md-block">
                <form action="{{ url()->current() }}" method="GET" id="sortForm">
                    <select name="sort"
                        class="form-select bg-dark text-white border-white-10 shadow-sm rounded-pill ps-4 pe-5"
                        style="min-width: 200px;" onchange="document.getElementById('sortForm').submit()">
                        <option value="popular" {{ $sort === 'popular' ? 'selected' : '' }}>По популярности</option>
                        <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>По новизне</option>
                        <option value="rating" {{ $sort === 'rating' ? 'selected' : '' }}>По рейтингу</option>
                    </select>
                </form>
            </div>
        </div>

        <!-- Book List -->
        @if($books->count() > 0)
            <div class="row row-cols-1 g-3 animate-fade-in-up delay-100">
                @foreach($books as $book)
                    <div class="col">
                        <x-book-card :book="$book" />
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