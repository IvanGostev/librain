@extends('layouts.app')

@section('content')
    <div class="container pb-5">


        <!-- Filters/Categories Links (Optional) -->
        <div class="d-flex justify-content-center gap-3 mb-5 flex-wrap animate-fade-in-up delay-100">
            <a href="{{ route('catalog.index', ['sort' => 'new']) }}"
                class="btn {{ request('sort', 'new') === 'new' ? 'btn-light' : 'btn-outline-light' }} rounded-pill px-4">Новые</a>
            <a href="{{ route('catalog.index', ['sort' => 'popular']) }}"
                class="btn {{ request('sort') === 'popular' ? 'btn-light' : 'btn-outline-light' }} rounded-pill px-4">Популярные</a>
            <a href="{{ route('catalog.index', ['sort' => 'commented']) }}"
                class="btn {{ request('sort') === 'commented' ? 'btn-light' : 'btn-outline-light' }} rounded-pill px-4">Комментируемые</a>
            <a href="{{ route('top100') }}" class="btn btn-outline-warning rounded-pill px-4"><i
                    class="bi bi-star-fill me-2"></i>Топ 100</a>
        </div>

        @if(request('sort') === 'popular')
            <div class="d-flex justify-content-center gap-2 mb-5 animate-fade-in-up delay-150" style="margin-top: -1.5rem;">
                <a href="{{ route('catalog.index', ['sort' => 'popular', 'period' => 'week']) }}"
                    class="btn {{ request('period') === 'week' ? 'btn-light' : 'btn-outline-secondary text-white-50 border-0' }} px-3 rounded-pill btn-sm">За
                    неделю</a>
                <a href="{{ route('catalog.index', ['sort' => 'popular', 'period' => 'month']) }}"
                    class="btn {{ request('period') === 'month' ? 'btn-light' : 'btn-outline-secondary text-white-50 border-0' }} px-3 rounded-pill btn-sm">За
                    месяц</a>
                <a href="{{ route('catalog.index', ['sort' => 'popular']) }}"
                    class="btn {{ !request('period') ? 'btn-light' : 'btn-outline-secondary text-white-50 border-0' }} px-3 rounded-pill btn-sm">За
                    все время</a>
            </div>
        @endif

        @if($books->count() > 0)
            <div
                class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 row-cols-xl-6 g-3 animate-fade-in-up delay-200">
                @foreach($books as $book)
                    <div class="col">
                        <x-book-card-vertical :book="$book" />
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