@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row g-5">
            <!-- Sidebar Info -->
            <div class="col-md-4 col-lg-3 animate-fade-in-up">
                <div class="card bg-dark-card border-0 shadow-lg position-sticky" style="top: 100px;">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <img src="{{ $series->cover ? asset('storage/' . $series->cover) : asset('images/no-cover.svg') }}"
                                    alt="{{ $series->name }}" class="rounded-3 shadow-lg border border-white-10 w-100"
                                    style="max-width: 150px; aspect-ratio: 1/1; object-fit: cover;"
                                    onerror="this.src='{{ asset('images/no-cover.svg') }}'">
                            </div>
                            <h1 class="fw-bold text-white mb-2">{{ $series->name }}</h1>
                            <div class="badge bg-secondary text-dark fw-bold rounded-pill px-3">
                                {{ $series->books->count() }}
                                {{ trans_choice('книга|книги|книг', $series->books->count()) }}
                            </div>
                        </div>

                        <hr class="border-white-10 my-4">

                        <div class="text-white-50 small">
                            @if($series->description)
                                {!! nl2br(e($series->description)) !!}
                            @else
                                <p class="fst-italic mb-0">Описание серии отсутствует.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content: Books List -->
            <div class="col-md-8 col-lg-9 animate-fade-in-up delay-100">
                <p class="fw-bold text-white mb-4 border-start border-4 border-secondary ps-3">Книги серии</p>

                <div
                    class="d-flex justify-content-center justify-content-md-start gap-3 mb-4 flex-wrap animate-fade-in-up delay-100">
                    @if($filter === 'order')
                        <span class="btn btn-primary rounded-pill px-4 cursor-default">По порядку</span>
                    @else
                        <a href="{{ route('series.show', ['slug' => $series->slug, 'filter' => 'order']) }}"
                            class="btn btn-outline-light rounded-pill px-4">По порядку</a>
                    @endif

                    @if($filter === 'new')
                        <span class="btn btn-primary rounded-pill px-4 cursor-default">Новые</span>
                    @else
                        <a href="{{ route('series.show', ['slug' => $series->slug, 'filter' => 'new']) }}"
                            class="btn btn-outline-light rounded-pill px-4">Новые</a>
                    @endif

                    @if($filter === 'popular')
                        <span class="btn btn-primary rounded-pill px-4 cursor-default">Популярные</span>
                    @else
                        <a href="{{ route('series.show', ['slug' => $series->slug, 'filter' => 'popular']) }}"
                            class="btn btn-outline-light rounded-pill px-4">Популярные</a>
                    @endif

                    @if($filter === 'discussed')
                        <span class="btn btn-primary rounded-pill px-4 cursor-default">Обсуждаемое</span>
                    @else
                        <a href="{{ route('series.show', ['slug' => $series->slug, 'filter' => 'discussed']) }}"
                            class="btn btn-outline-light rounded-pill px-4">Обсуждаемое</a>
                    @endif
                </div>

                @if($filter === 'popular')
                    <div
                        class="d-flex justify-content-center justify-content-md-start gap-2 mb-4 animate-fade-in-up delay-150 flex-wrap">
                        @foreach(['week' => 'За неделю', 'month' => 'За месяц', 'half_year' => 'За полгода', 'year' => 'За год', 'all' => 'За все время'] as $key => $label)
                            @if($period === $key)
                                <span class="btn btn-primary px-3 rounded-pill btn-sm cursor-default">{{ $label }}</span>
                            @else
                                <a href="{{ route('series.show', ['slug' => $series->slug, 'filter' => 'popular', 'period' => $key]) }}"
                                    class="btn btn-outline-light px-3 rounded-pill btn-sm">{{ $label }}</a>
                            @endif
                        @endforeach
                    </div>
                @endif

                @if($books->count() > 0)
                    @if($filter === 'discussed' && isset($reviews))
                        @if($reviews->count() > 0)
                            <div class="d-flex flex-column gap-3">
                                @foreach($reviews as $review)
                                    <div class="card bg-dark-card border-white-10 p-4 rounded-4 hover-elevate transition-transform">
                                        <div class="mb-3 border-bottom border-white-10 pb-3 d-flex align-items-center gap-3">
                                            <a href="{{ route('books.show', ['genre' => $review->book->genres->first()->slug ?? 'general', 'slug' => $review->book->slug]) }}"
                                                class="flex-shrink-0">
                                                <img src="{{ $review->book->cover_image ? asset('storage/' . $review->book->cover_image) : asset('images/no-cover.svg') }}"
                                                    alt="{{ $review->book->title }}" class="rounded shadow-sm object-fit-cover"
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
                                <p class="mb-0">В этой серии еще нет комментариев. Будьте первым!</p>
                            </div>
                        @endif
                    @elseif($filter === 'order')
                        <div class="d-flex flex-column gap-3">
                            @foreach($books as $index => $book)
                                <div class="card bg-dark-card border-0 p-3 hover-card-lift transition-transform">
                                    <div class="d-flex align-items-center">
                                        <!-- Order Badge -->
                                        <div class="me-3 d-flex flex-column align-items-center justify-content-center"
                                            style="width: 50px; height: 50px; min-width: 50px;">
                                            <span class="small text-muted text-uppercase" style="font-size: 0.6rem;">Книга</span>
                                            <span class="h4 fw-bold mb-0">{{ $book->pivot->order ?? $index + 1 }}</span>
                                        </div>

                                        <!-- Cover (Small) -->
                                        <a href="{{ route('books.show', ['genre' => $book->genre_slug, 'slug' => $book->slug]) }}"
                                            class="me-3 d-none d-sm-block">
                                            <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/no-cover.svg') }}"
                                                alt="{{ $book->title }}" class="rounded shadow-sm"
                                                style="width: 50px; height: 75px; object-fit: cover;">
                                        </a>

                                        <!-- Content -->
                                        <div class="flex-grow-1 min-w-0">
                                            <p class="fw-bold text-white mb-1">
                                                <a href="{{ route('books.show', ['genre' => $book->genre_slug, 'slug' => $book->slug]) }}"
                                                    class="text-white text-decoration-none hover-text-secondary transition-colors">
                                                    {{ $book->title }}
                                                </a>
                                            </p>
                                            <p class="text-muted small mb-0">
                                                {{ $book->authors->isNotEmpty() ? $book->authors->pluck('name')->join(', ') : 'Автор неизвестен' }}
                                            </p>
                                        </div>

                                        <!-- Action -->
                                        <div class="ms-3">
                                            <a href="{{ route('books.show', ['genre' => $book->genre_slug, 'slug' => $book->slug]) }}"
                                                class="btn btn-sm btn-outline-secondary rounded-circle" title="Читать">
                                                <i class="bi bi-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Connector Line (Visual only, skip for last item) -->
                                @if(!$loop->last)
                                    <div class="ms-4 ps-2 border-start border-white-10 h-4"></div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        {{-- Grid Layout for other filters --}}
                        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3">
                            @foreach($books as $book)
                                <div class="col animate-fade-in-up">
                                    <x-book-card-vertical :book="$book" />
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-5 d-flex justify-content-center">
                        {{ $books->appends(['filter' => $filter, 'period' => $period])->links() }}
                    </div>
                @else
                    <div class="text-center py-5 text-muted border border-dashed border-white-10 rounded-3">
                        <p class="mb-0">В этой серии пока нет книг.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .btn-outline-light {
            border: 1px solid var(--bs-primary) !important;
            color: var(--bs-primary) !important;
        }

        .btn-outline-light:hover {
            background-color: var(--bs-primary) !important;
            color: white !important;
        }

        .btn-primary {
            border: 1px solid var(--bs-primary) !important;
            color: white !important;
        }

        .cursor-default {
            cursor: default !important;
        }
    </style>
@endsection
