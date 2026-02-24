@extends('layouts.app')

@section('title', 'Жанр: ' . $genre->name . ' - Читать книги онлайн | ' . config('app.name'))
@section('description', 'Лучшие книги в жанре ' . $genre->name . '. Обширная библиотека произведений, доступных для чтения онлайн.')

@section('content')
    <div class="container py-3">
        <!-- Filters/Categories Links -->
        <div class="d-flex justify-content-center gap-3 mb-5 flex-wrap animate-fade-in-up">
            @if(request('sort', 'latest') === 'latest')
                <span class="btn btn-primary rounded-pill px-4 cursor-default">Новые</span>
            @else
                <a href="{{ route('genres.show', ['slug' => $genre->slug, 'sort' => 'latest']) }}"
                    class="btn btn-outline-light rounded-pill px-4">Новые</a>
            @endif

            @if(request('sort') === 'popular')
                <span class="btn btn-primary rounded-pill px-4 cursor-default">Популярные</span>
            @else
                <a href="{{ route('genres.show', ['slug' => $genre->slug, 'sort' => 'popular']) }}"
                    class="btn btn-outline-light rounded-pill px-4">Популярные</a>
            @endif

            @if(request('sort') === 'commented')
                <span class="btn btn-primary rounded-pill px-4 cursor-default">Комментируемые</span>
            @else
                <a href="{{ route('genres.show', ['slug' => $genre->slug, 'sort' => 'commented']) }}"
                    class="btn btn-outline-light rounded-pill px-4">Комментируемые</a>
            @endif
        </div>

        @if(request('sort') === 'popular')
            <div class="d-flex justify-content-center gap-2 mb-3 animate-fade-in-up delay-150" style="margin-top: -0.5rem;">
                <a href="{{ route('genres.show', ['slug' => $genre->slug, 'sort' => 'popular', 'period' => 'week']) }}"
                    class="btn {{ $period === 'week' ? 'btn-primary disabled' : 'btn-outline-light' }} px-3 rounded-pill btn-sm">За
                    неделю</a>
                <a href="{{ route('genres.show', ['slug' => $genre->slug, 'sort' => 'popular', 'period' => 'month']) }}"
                    class="btn {{ $period === 'month' ? 'btn-primary disabled' : 'btn-outline-light' }} px-3 rounded-pill btn-sm">За
                    месяц</a>
                <a href="{{ route('genres.show', ['slug' => $genre->slug, 'sort' => 'popular', 'period' => 'all']) }}"
                    class="btn {{ $period === 'all' ? 'btn-primary disabled' : 'btn-outline-light' }} px-3 rounded-pill btn-sm">За
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

        @if(request('sort') === 'commented')
            @if(isset($reviews) && $reviews->count() > 0)
                <div class="d-flex flex-column gap-4 animate-fade-in-up delay-100" style="max-width: 800px; margin: 0 auto;">
                    @foreach($reviews as $review)
                        <div class="card bg-dark-card border-white-10 p-4 rounded-4 hover-elevate transition-transform">
                            <div class="mb-3 border-bottom border-white-10 pb-3 d-flex align-items-center gap-3">
                                <a href="{{ route('books.show', ['genre' => $review->book->genres->first()->slug ?? 'general', 'slug' => $review->book->slug]) }}"
                                    class="flex-shrink-0">
                                    <img src="{{ $review->book->cover_image ? asset('storage/' . $review->book->cover_image) : asset('images/no-cover.svg') }}"
                                        alt="{{ $review->book->title }}" class="rounded shadow-sm object-fit-cover"
                                        style="width: 50px; height: 75px;" onerror="this.src='{{ asset('images/no-cover.svg') }}'">
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
                                        @php
                                            $avatarUrl = Str::startsWith($authorAvatar, ['http://', 'https://'])
                                                ? $authorAvatar
                                                : asset('storage/' . $authorAvatar);
                                        @endphp
                                        <img src="{{ $avatarUrl }}" class="rounded-circle border border-white-10"
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
                <div
                    class="text-center py-5 text-muted hover-elevate transition-transform bg-dark-card rounded-4 border border-white-10">
                    <i class="bi bi-chat-square-text fs-1 mb-3 d-block opacity-50"></i>
                    <p class="mb-0">В этом жанре еще нет комментариев. Будьте первым!</p>
                </div>
            @endif
        @else
            <!-- Book List -->
            @if($books->count() > 0)
                <div
                    class="row row-cols-1 row-cols-sm-2 row-cols-md-4 row-cols-lg-5 row-cols-xl-6 g-3 animate-fade-in-up delay-100">
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
        @endif
    </div>

    @if($genre->title || $genre->description)
        <section class="py-5 bg-dark-card border-top border-white-10 mt-5 w-100">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-start">
                        @if($genre->description)
                            <div class="text-white-50">{!! $genre->description !!}</div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endif

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
