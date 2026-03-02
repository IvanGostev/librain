@extends('layouts.app')
@section('og_image', asset('favicon.svg'))
@section('content')
    <div class="container pb-5">
        <h1 class="h2 fw-bold text-white mb-4">Авторы</h1>
        <div class="d-flex gap-2 mb-4 flex-wrap animate-fade-in-up delay-100">
            @php
                $currentSort = request('sort');
            @endphp
            @if($currentSort === 'count_desc' || $currentSort === 'count')
                <a href="{{ route('authors.index', ['sort' => 'count_asc']) }}" class="btn btn-primary rounded-pill px-4">По
                    количеству<i class="bi bi-sort-numeric-down ms-1"></i></a>
            @elseif($currentSort === 'count_asc')
                <a href="{{ route('authors.index', ['sort' => 'count_desc']) }}" class="btn btn-primary rounded-pill px-4">По
                    количеству<i class="bi bi-sort-numeric-up-alt ms-1"></i></a>
            @else
                <a href="{{ route('authors.index', ['sort' => 'count_desc']) }}"
                    class="btn btn-outline-light rounded-pill px-4">По количеству<i class="bi bi-arrow-down-up ms-1"></i></a>
            @endif
            @if($currentSort === 'views_desc' || $currentSort === 'views')
                <a href="{{ route('authors.index', ['sort' => 'views_asc']) }}" class="btn btn-primary rounded-pill px-4">По
                    просмотрам<i class="bi bi-sort-numeric-down ms-1"></i></a>
            @elseif($currentSort === 'views_asc')
                <a href="{{ route('authors.index', ['sort' => 'views_desc']) }}" class="btn btn-primary rounded-pill px-4">По
                    просмотрам<i class="bi bi-sort-numeric-up-alt ms-1"></i></a>
            @else
                <a href="{{ route('authors.index', ['sort' => 'views_desc']) }}"
                    class="btn btn-outline-light rounded-pill px-4">По просмотрам<i class="bi bi-arrow-down-up ms-1"></i></a>
            @endif
            @if($currentSort === 'name_asc')
                <a href="{{ route('authors.index', ['sort' => 'name_desc']) }}" class="btn btn-primary rounded-pill px-4">По
                    имени<i class="bi bi-sort-alpha-down ms-1"></i></a>
            @elseif($currentSort === 'name_desc')
                <a href="{{ route('authors.index', ['sort' => 'name_asc']) }}" class="btn btn-primary rounded-pill px-4">По
                    имени<i class="bi bi-sort-alpha-up-alt ms-1"></i></a>
            @else
                <a href="{{ route('authors.index', ['sort' => 'name_asc']) }}"
                    class="btn btn-outline-light rounded-pill px-4">По имени<i class="bi bi-arrow-down-up ms-1"></i></a>
            @endif
            @if($currentSort === 'alphabet_asc' || $currentSort === 'alphabet' || request('letter'))
                <a href="{{ route('authors.index', ['sort' => 'alphabet_desc']) }}" class="btn btn-primary rounded-pill px-4">По
                    алфавиту<i class="bi bi-sort-alpha-down ms-1"></i></a>
            @elseif($currentSort === 'alphabet_desc')
                <a href="{{ route('authors.index', ['sort' => 'alphabet_asc']) }}" class="btn btn-primary rounded-pill px-4">По
                    алфавиту<i class="bi bi-sort-alpha-up-alt ms-1"></i></a>
            @else
                <a href="{{ route('authors.index', ['sort' => 'alphabet_asc']) }}"
                    class="btn btn-outline-light rounded-pill px-4">По алфавиту<i class="bi bi-arrow-down-up ms-1"></i></a>
            @endif
        </div>
        @if(isset($letters) && $letters->isNotEmpty())
            <div class="d-flex flex-wrap gap-2 mb-5 p-4 bg-dark-card rounded-4 animate-fade-in-up delay-150">
                @if(!request('letter'))
                    <span class="btn btn-sm alphabet-filter btn-primary cursor-default">Все</span>
                @else
                    <a href="{{ route('authors.index', ['sort' => 'alphabet']) }}"
                        class="btn btn-sm alphabet-filter btn-outline-light">Все</a>
                @endif
                @foreach($letters as $l)
                    @if(request('letter') == $l)
                        <span class="btn btn-sm alphabet-filter btn-primary cursor-default">{{ $l }}</span>
                    @else
                        <a href="{{ route('authors.index', ['sort' => 'alphabet', 'letter' => $l]) }}"
                            class="btn btn-sm alphabet-filter btn-outline-light">
                            {{ $l }}
                        </a>
                    @endif
                @endforeach
            </div>
        @endif
        <section>
            @if($authors->count() > 0)
                <div class="row g-3 animate-fade-in-up delay-200">
                    @foreach($authors as $author)
                        <div class="col-12">
                            <a href="{{ route('authors.show', $author->slug) }}" class="text-decoration-none group">
                                <article
                                    class="card bg-dark-card border-white-10 shadow-sm hover-card-lift position-relative overflow-hidden">
                                    <div class="card-body p-3 position-relative" style="z-index: 1;">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3 me-md-4">
                                                <img src="{{ $author->photo ? asset('storage/' . $author->photo) : asset('images/no-cover.svg') }}"
                                                    alt="{{ $author->name }}"
                                                    class="rounded-circle border border-white-10 shadow-sm"
                                                    style="width: 80px; height: 80px; object-fit: cover;">
                                            </div>
                                            <div class="flex-grow-1 min-w-0 pe-2 pe-md-4">
                                                <div class="mb-2">
                                                    <div class="d-flex flex-wrap gap-1 gap-md-2 mb-2">
                                                        <span
                                                            class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2 px-md-3 ms-0 author-badge">
                                                            {{ $author->books_count }}
                                                            <span class="d-none d-sm-inline">{{ trans_choice('книга|книги|книг', $author->books_count) }}</span>
                                                            <span class="d-inline d-sm-none">кн.</span>
                                                        </span>
                                                        @php
                                                            $totalViews = $author->books->sum('views');
                                                            $totalReviews = $author->books->sum('reviews_count');
                                                        @endphp
                                                        <span
                                                            class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-2 px-md-3 author-badge"
                                                            title="Просмотры">
                                                            <i class="bi bi-eye me-1"></i>
                                                            {{ number_format($totalViews, 0, ',', ' ') }}
                                                        </span>
                                                        <span
                                                            class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-2 px-md-3 author-badge"
                                                            title="Комментарии">
                                                            <i class="bi bi-chat-left-text me-1"></i> {{ $totalReviews }}
                                                        </span>
                                                    </div>
                                                    <p
                                                        class="h5 fw-bold text-white mb-2 group-hover:text-primary transition-colors pe-3 author-name">
                                                        {{ $author->name }}
                                                    </p>
                                                </div>
                                                <p class="text-white-50 small mb-0 text-truncate d-none d-md-block">
                                                    @if($author->bio)
                                                        {{ Str::limit(strip_tags($author->bio), 100) }}
                                                    @else
                                                        Биографические данные и информация об авторе...
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="ms-1 ms-md-3">
                                                <i
                                                    class="bi bi-chevron-right text-white-50 fs-5 group-hover:text-primary transition-colors"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <i class="bi bi-person position-absolute bottom-0 end-0 text-white"
                                        style="font-size: 5rem; transform: translate(20%, 30%) rotate(-15deg); opacity: 0.05; z-index: 0;"></i>
                                </article>
                            </a>
                        </div>
                    @endforeach
                </div>
                <div class="mt-5 d-flex justify-content-center">
                    {{ $authors->links() }}
                </div>
            @else
                <div class="text-center py-5 text-muted animate-fade-in-up">
                    <i class="bi bi-person-slash fs-1 mb-3 d-block"></i>
                    <p>Авторы пока не зарегистрированы.</p>
                </div>
            @endif
        </section>
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
        @media (max-width: 576px) {
            .author-name {
                font-size: 1.05rem !important;
            }
            .author-badge {
                font-size: 0.65rem !important;
                padding: 0.35em 0.6em !important;
            }
        }
        /* Filter buttons styling */
        .btn-outline-light {
            border: 1px solid var(--bs-primary) !important;
            color: var(--bs-primary) !important;
        }
        .btn-primary {
            border: 1px solid var(--bs-primary) !important;
            color: white !important;
        }
        /* Alphabet filter buttons */
        .alphabet-filter.btn-outline-light {
            transition: all 0.3s ease;
        }
        .alphabet-filter.btn-outline-light:hover {
            background-color: var(--bs-primary);
            color: white !important;
            transform: translateY(-2px);
        }
    </style>
@endsection
