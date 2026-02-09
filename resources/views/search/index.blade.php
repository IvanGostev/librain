@extends('layouts.app')

@section('title', $title ?? 'Поиск книг - Librain')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center mb-5 animate-fade-in-up">
            <div class="col-md-8 text-center">
                <h1 class="fw-bold text-white mb-4">Поиск книг</h1>
                <form action="{{ url('/search') }}" method="GET" class="position-relative">
                    <input type="text" name="q" value="{{ request('q') }}"
                        class="form-control form-control-lg bg-dark border-white-10 text-white rounded-pill ps-5 pe-5 shadow-lg"
                        placeholder="Название, автор, жанр или текст..." autofocus>
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-4 text-muted fs-5"></i>
                    <button type="submit"
                        class="btn btn-primary rounded-pill position-absolute top-50 end-0 translate-middle-y me-2 px-4">
                        Найти
                    </button>
                </form>
            </div>
        </div>

        @if(request('q'))
            <div class="d-flex align-items-center justify-content-between mb-4 animate-fade-in-up delay-100">
                <h2 class="h4 text-white mb-0">
                    Результаты поиска: <span class="text-primary text-break">«{{ request('q') }}»</span>
                </h2>
                <div class="text-muted small">
                    Найдено {{ $books->total() }} {{ trans_choice('книга|книги|книг', $books->total()) }}
                </div>
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
                    {{ $books->withQueryString()->links() }}
                </div>
            @else
                <div
                    class="text-center py-5 text-muted animate-fade-in-up delay-100 bg-dark-card rounded-3 border border-dashed border-white-10 mt-4">
                    <i class="bi bi-search fs-1 mb-3 d-block opacity-50"></i>
                    <p class="lead mb-0">Ничего не найдено.</p>
                    <p class="small">Попробуйте изменить запрос.</p>
                </div>
            @endif
        @endif
    </div>
@endsection