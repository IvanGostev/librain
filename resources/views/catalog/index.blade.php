@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="text-center mb-5 animate-fade-in-up">
            <h6 class="text-secondary text-uppercase tracking-wider fw-bold mb-2">Библиотека</h6>
            <h1 class="display-4 fw-bold text-white mb-3">Каталог книг</h1>
            <p class="text-muted lead mx-auto mb-4" style="max-width: 600px;">
                Исследуйте нашу коллекцию фантастических историй и найдите свое следующее приключение.
            </p>
            <div class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-4 py-2 fw-bold animate-fade-in-up delay-100">
                Всего книг: {{ $books->total() }}
            </div>
        </div>

        <!-- Filters/Categories Links (Optional) -->
        <div class="d-flex justify-content-center gap-3 mb-5 flex-wrap animate-fade-in-up delay-100">
            <a href="{{ route('genres.index') }}" class="btn btn-outline-light rounded-pill px-4">Жанры</a>
            <a href="{{ route('authors.index') }}" class="btn btn-outline-light rounded-pill px-4">Авторы</a>
            <a href="{{ route('series.index') }}" class="btn btn-outline-light rounded-pill px-4">Серии</a>
            <a href="{{ route('top100') }}" class="btn btn-outline-warning rounded-pill px-4"><i
                    class="bi bi-star-fill me-2"></i>Топ 100</a>
        </div>

        @if($books->count() > 0)
            <div class="row row-cols-1 g-3 animate-fade-in-up delay-200">
                @foreach($books as $book)
                    <div class="col">
                        <x-book-card :book="$book" />
                    </div>
                @endforeach
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $books->links() }}
            </div>
        @else
            <div class="text-center py-5 text-muted animate-fade-in-up">
                <i class="bi bi-book fs-1 mb-3 d-block opacity-50"></i>
                <p class="mb-0">Книги не найдены. Загляните позже!</p>
            </div>
        @endif
    </div>
@endsection