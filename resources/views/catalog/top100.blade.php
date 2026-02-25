@extends('layouts.app')
@section('content')
    <div class="container pb-5">
        <h1 class="h2 fw-bold text-white mb-4">Топ-100 лучших книг</h1>
        <div class="d-flex gap-2 mb-4 flex-wrap animate-fade-in-up delay-100">
            @if(!request('period'))
                <span class="btn btn-primary rounded-pill px-4 cursor-default">За все время</span>
            @else
                <a href="{{ route('top100') }}" class="btn btn-outline-light rounded-pill px-4">За все время</a>
            @endif
            @if(request('period') === 'week')
                <span class="btn btn-primary rounded-pill px-4 cursor-default">За неделю</span>
            @else
                <a href="{{ route('top100', ['period' => 'week']) }}" class="btn btn-outline-light rounded-pill px-4">За
                    неделю</a>
            @endif
            @if(request('period') === 'month')
                <span class="btn btn-primary rounded-pill px-4 cursor-default">За месяц</span>
            @else
                <a href="{{ route('top100', ['period' => 'month']) }}" class="btn btn-outline-light rounded-pill px-4">За
                    месяц</a>
            @endif
            @if(request('period') === 'half_year')
                <span class="btn btn-primary rounded-pill px-4 cursor-default">За полгода</span>
            @else
                <a href="{{ route('top100', ['period' => 'half_year']) }}" class="btn btn-outline-light rounded-pill px-4">За
                    полгода</a>
            @endif
            @if(request('period') === 'year')
                <span class="btn btn-primary rounded-pill px-4 cursor-default">За год</span>
            @else
                <a href="{{ route('top100', ['period' => 'year']) }}" class="btn btn-outline-light rounded-pill px-4">За год</a>
            @endif
        </div>
        @if($books->count() > 0)
            <div class="row g-4 animate-fade-in-up delay-200">
                @foreach($books as $index => $book)
                    <div class="col-12">
                        <div class="card bg-dark-card border-0 hover-card-lift">
                            <div class="card-body p-3 p-md-4 d-flex align-items-center">
                                <div class="me-4 text-center" style="min-width: 60px;">
                                    <div class="h2 fw-bold rank-number mb-0">
                                        #{{ $index + 1 }}
                                    </div>
                                </div>
                                <div class="me-4 d-none d-sm-block">
                                    <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/no-cover.svg') }}"
                                        alt="{{ $book->title }}" class="rounded shadow-sm"
                                        style="width: 120px; height: 180px; object-fit: cover;">
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <p class="fw-bold text-white mb-1">
                                        <a href="{{ route('books.show', ['genre' => $book->genre_slug, 'slug' => $book->slug]) }}"
                                            class="text-white text-decoration-none hover-text-primary transition-colors">
                                            {{ $book->title }}
                                        </a>
                                    </p>
                                    <p class="text-muted mb-2">
                                        @if($book->authors->isNotEmpty())
                                            {{ $book->authors->pluck('name')->join(', ') }}
                                        @else
                                            Автор неизвестен
                                        @endif
                                    </p>
                                    <div class="d-flex gap-3 small">
                                        <span class="text-warning"><i class="bi bi-star-fill me-1"></i>
                                            {{ number_format($book->rating, 1) }}</span>
                                        <span class="text-white-50"><i class="bi bi-eye-fill me-1"></i> {{ $book->views }}</span>
                                        <span class="text-white-50"><i class="bi bi-chat-fill me-1"></i>
                                            {{ $book->reviews_count ?? $book->reviews()->count() }}</span>
                                    </div>
                                </div>
                                <div class="ms-3 d-none d-md-block">
                                    <a href="{{ route('books.show', ['genre' => $book->genre_slug, 'slug' => $book->slug]) }}"
                                        class="btn btn-outline-primary rounded-pill btn-sm px-4">
                                        Читать
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <p class="text-muted">Рейтинг формируется...</p>
            </div>
        @endif
    </div>
    @if(isset($bottomTitle) && ($bottomTitle || $bottomText))
        <section class="py-5 bg-dark-card border-top border-white-10 mt-5 w-100">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-start">
                        @if($bottomText)
                        <div class="text-white-50">{!! $bottomText !!}</div> @endif
                    </div>
                </div>
            </div>
        </section>
    @endif
    <style>
        /* Filter buttons styling */
        .btn-outline-light {
            border: 1px solid var(--bs-primary) !important;
            color: var(--bs-primary) !important;
        }
        .btn-primary {
            border: 1px solid var(--bs-primary) !important;
            color: white !important;
        }
        /* Rank number styling - visible in both themes */
        .rank-number {
            color: var(--bs-primary);
            opacity: 0.7;
        }
        [data-bs-theme="dark"] .rank-number {
            -webkit-text-stroke: 1px rgba(14, 165, 233, 0.5);
            color: transparent;
        }
        [data-bs-theme="light"] .rank-number {
            color: var(--bs-primary);
            opacity: 0.5;
        }
    </style>
@endsection