@extends('layouts.app')

@section('content')
    <div class="container pb-5">
        <h1 class="h2 fw-bold text-white mb-4">Топ-100 лучших книг</h1>

        <div class="d-flex gap-2 mb-4 flex-wrap animate-fade-in-up delay-100">
            <a href="{{ route('top100') }}"
                class="btn {{ !request('period') ? 'btn-light' : 'btn-outline-light' }} rounded-pill px-4">За все время</a>
            <a href="{{ route('top100', ['period' => 'week']) }}"
                class="btn {{ request('period') === 'week' ? 'btn-light' : 'btn-outline-light' }} rounded-pill px-4">За неделю</a>
            <a href="{{ route('top100', ['period' => 'month']) }}"
                class="btn {{ request('period') === 'month' ? 'btn-light' : 'btn-outline-light' }} rounded-pill px-4">За месяц</a>
            <a href="{{ route('top100', ['period' => 'half_year']) }}"
                class="btn {{ request('period') === 'half_year' ? 'btn-light' : 'btn-outline-light' }} rounded-pill px-4">За полгода</a>
            <a href="{{ route('top100', ['period' => 'year']) }}"
                class="btn {{ request('period') === 'year' ? 'btn-light' : 'btn-outline-light' }} rounded-pill px-4">За год</a>
        </div>
        @if($books->count() > 0)
            <div class="row g-4 animate-fade-in-up delay-200">
                @foreach($books as $index => $book)
                    <div class="col-12">
                        <div class="card bg-dark-card border-0 hover-card-lift">
                            <div class="card-body p-3 p-md-4 d-flex align-items-center">
                                <!-- Rank -->
                                <div class="me-4 text-center" style="min-width: 60px;">
                                    <div class="h2 fw-bold text-outline-light mb-0"
                                        style="-webkit-text-stroke: 1px rgba(255,255,255,0.3); color: transparent;">
                                        #{{ $index + 1 }}
                                    </div>
                                </div>

                                <!-- Cover -->
                                <div class="me-4 d-none d-sm-block">
                                    <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/no-cover.svg') }}"
                                        alt="{{ $book->title }}" class="rounded shadow-sm"
                                        style="width: 120px; height: 180px; object-fit: cover;">
                                </div>

                                <!-- Info -->
                                <div class="flex-grow-1 min-w-0">
                                    <h5 class="fw-bold text-white mb-1 text-truncate">
                                        <a href="{{ route('books.show', $book->slug) }}"
                                            class="text-white text-decoration-none hover-text-primary transition-colors">
                                            {{ $book->title }}
                                        </a>
                                    </h5>
                                    <p class="text-muted mb-2 text-truncate">{{ $book->author->name ?? 'Автор неизвестен' }}</p>

                                    <div class="d-flex gap-3 small">
                                        <span class="text-warning"><i class="bi bi-star-fill me-1"></i>
                                            {{ number_format($book->rating, 1) }}</span>
                                        <span class="text-white-50"><i class="bi bi-eye-fill me-1"></i> {{ $book->views }}</span>
                                        <span class="text-white-50"><i class="bi bi-chat-fill me-1"></i>
                                            {{ $book->reviews_count ?? $book->reviews()->count() }}</span>
                                    </div>
                                </div>

                                <!-- Action -->
                                <div class="ms-3 d-none d-md-block">
                                    <a href="{{ route('books.show', $book->slug) }}"
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
@endsection