@extends('layouts.app')
@section('og_image', asset('favicon.svg'))
@section('content')
    <div class="container pb-5">
        <div class="d-flex justify-content-center gap-3 mb-5 flex-wrap animate-fade-in-up delay-100">
            @if(request('sort', 'new') === 'new')
                <span class="btn btn-primary rounded-pill px-4 cursor-default">Новые</span>
            @else
                <a href="{{ route('catalog.index') }}" class="btn btn-outline-light rounded-pill px-4">Новые</a>
            @endif
            @if(request('sort') === 'popular')
                <span class="btn btn-primary rounded-pill px-4 cursor-default">Популярные</span>
            @else
                <a href="{{ route('catalog.index', ['sort' => 'popular']) }}"
                    class="btn btn-outline-light rounded-pill px-4">Популярные</a>
            @endif
            @if(request('sort') === 'commented')
                <span class="btn btn-primary rounded-pill px-4 cursor-default">Комментируемые</span>
            @else
                <a href="{{ route('catalog.index', ['sort' => 'commented']) }}"
                    class="btn btn-outline-light rounded-pill px-4">Комментируемые</a>
            @endif
            <a href="{{ route('top100') }}" class="btn btn-outline-warning rounded-pill px-4"><i
                    class="bi bi-star-fill me-2"></i>Топ 100</a>
        </div>
        @if(request('sort') === 'popular')
            <div class="d-flex justify-content-center gap-2 mb-5 animate-fade-in-up delay-150 flex-wrap"
                style="margin-top: -1.5rem;">
                <a href="{{ route('catalog.index', ['sort' => 'popular', 'period' => 'week']) }}"
                    class="btn {{ $period === 'week' ? 'btn-primary disabled' : 'btn-outline-light' }} px-3 rounded-pill btn-sm">За
                    неделю</a>
                <a href="{{ route('catalog.index', ['sort' => 'popular', 'period' => 'month']) }}"
                    class="btn {{ $period === 'month' ? 'btn-primary disabled' : 'btn-outline-light' }} px-3 rounded-pill btn-sm">За
                    месяц</a>
                <a href="{{ route('catalog.index', ['sort' => 'popular', 'period' => 'half_year']) }}"
                    class="btn {{ $period === 'half_year' ? 'btn-primary disabled' : 'btn-outline-light' }} px-3 rounded-pill btn-sm">За
                    полгода</a>
                <a href="{{ route('catalog.index', ['sort' => 'popular', 'period' => 'year']) }}"
                    class="btn {{ $period === 'year' ? 'btn-primary disabled' : 'btn-outline-light' }} px-3 rounded-pill btn-sm">За
                    год</a>
                <a href="{{ route('catalog.index', ['sort' => 'popular', 'period' => 'all']) }}"
                    class="btn {{ $period === 'all' ? 'btn-primary disabled' : 'btn-outline-light' }} px-3 rounded-pill btn-sm">За
                    все время</a>
            </div>
        @endif
        @if(request('sort') === 'commented')
            @if(isset($reviews) && $reviews->count() > 0)
                <div class="d-flex flex-column gap-4 animate-fade-in-up delay-200" style="max-width: 800px; margin: 0 auto;">
                    @foreach($reviews as $review)
                        <div class="card bg-dark-card border-white-10 p-4 rounded-4 hover-elevate transition-transform">
                            <div class="mb-3 border-bottom border-white-10 pb-3 d-flex align-items-center gap-3">
                                <a href="{{ route('books.show', ['genre' => $review->book->genres->first()->slug ?? 'general', 'slug' => $review->book->slug]) }}" class="flex-shrink-0">
                                    <img src="{{ $review->book->cover_image ? asset('storage/' . $review->book->cover_image) : asset('images/no-cover.svg') }}"
                                         alt="{{ $review->book->title }}"
                                         class="rounded shadow-sm object-fit-cover"
                                         style="width: 50px; height: 75px;"
                                         onerror="this.src='{{ asset('images/no-cover.svg') }}'">
                                </a>
                                <div>
                                    <p class="mb-1 fw-bold">
                                        <a href="{{ route('books.show', ['genre' => $review->book->genres->first()->slug ?? 'general', 'slug' => $review->book->slug]) }}"
                                            class="text-white text-decoration-none hover-text-primary transition-colors">
                                            {{ $review->book->title }}
                                        </a>
                                    </p>
                                    <div class="small text-muted">
                                        @if($review->book->authors->isNotEmpty())
                                            Авторы: 
                                            @foreach($review->book->authors as $author)
                                                <a href="{{ route('authors.show', $author->slug) }}" class="text-muted text-decoration-none hover-text-white">{{ $author->name }}</a>{{ !$loop->last ? ', ' : '' }}
                                            @endforeach
                                        @else
                                            Автор неизвестен
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <div class="flex-shrink-0">
                                    @php
                                        $authorName = $review->user ? $review->user->name : ($review->guest_name ?? 'Гость');
                                        $authorAvatar = $review->user ? $review->user->avatar : null;
                                        $authorInitial = mb_substr($authorName, 0, 1);
                                    @endphp
                                    @if($authorAvatar)
                                        <img src="{{ asset('storage/' . $authorAvatar) }}" class="rounded-circle border border-white-10"
                                            style="width: 48px; height: 48px; object-fit: cover;"
                                            onerror="this.style.display='none'; this.nextElementSibling.classList.remove('d-none'); this.nextElementSibling.classList.add('d-flex');">
                                        <div class="avatar bg-primary bg-opacity-10 text-primary rounded-circle d-none align-items-center justify-content-center fw-bold"
                                            style="width: 48px; height: 48px; font-size: 1.2rem;">
                                            {{ $authorInitial }}
                                        </div>
                                    @else
                                        <div class="avatar bg-{{ $review->user ? 'primary' : 'secondary' }} bg-opacity-10 text-{{ $review->user ? 'primary' : 'white' }} rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                            style="width: 48px; height: 48px; font-size: 1.2rem;">
                                            {{ $authorInitial }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <p class="mb-0 fw-bold text-white">{{ $authorName }}</p>
                                        <span class="text-muted small">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="text-warning small mb-2">
                                        @if($review->rating)
                                            @for($i = 0; $i < $review->rating; $i++) <i class="bi bi-star-fill"></i> @endfor
                                            @for($i = $review->rating; $i < 5; $i++) <i class="bi bi-star"></i> @endfor
                                        @endif
                                    </div>
                                    <div class="text-white-75" style="line-height: 1.6;">
                                        {{ $review->comment }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-5 d-flex justify-content-center">
                    {{ $reviews->links() }}
                </div>
            @else
                <div class="text-center py-5 text-muted animate-fade-in-up">
                    <i class="bi bi-chat-square-text fs-1 mb-3 d-block opacity-50"></i>
                    <p class="mb-0">Комментариев пока нет. Будьте первым!</p>
                </div>
            @endif
        @else
            @if(isset($books) && $books->count() > 0)
                <div
                    class="row row-cols-1 row-cols-sm-2 row-cols-md-4 row-cols-lg-5 row-cols-xl-6 g-2 g-sm-3 animate-fade-in-up delay-200">
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
        @endif
    </div>
    @if(isset($bottomTitle) && ($bottomTitle || $bottomText))
        <section class="py-5 bg-dark-card border-top border-white-10 mt-5 w-100">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-start">
                        @if($bottomTitle)
                        <h1 class="h1 fw-bold mb-4 text-white">{{ $bottomTitle }}</h1> @endif
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
    </style>
@endsection
