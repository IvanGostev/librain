@extends('layouts.app')

@section('content')
    <div class="container pb-5">
        <h1 class="h2 fw-bold text-white mb-4">Авторы</h1>

        <div class="d-flex gap-2 mb-4 flex-wrap animate-fade-in-up delay-100">
            @php
                $currentSort = request('sort');


                $countNext = 'count_desc';
                $countClass = 'btn-outline-light';
                $countIcon = '<i class="bi bi-arrow-down-up ms-1"></i>';
                if ($currentSort === 'count_desc') {
                    $countNext = 'count_asc';
                    $countClass = 'btn-primary';
                    $countIcon = '<i class="bi bi-sort-numeric-down ms-1"></i>';
                } elseif ($currentSort === 'count_asc' || $currentSort === 'count') {
                    $countNext = null;
                    $countClass = 'btn-primary';
                    $countIcon = '<i class="bi bi-sort-numeric-up-alt ms-1"></i>';
                }


                $viewsNext = 'views_desc';
                $viewsClass = 'btn-outline-light';
                $viewsIcon = '<i class="bi bi-arrow-down-up ms-1"></i>';
                if ($currentSort === 'views_desc' || $currentSort === 'views') {
                    $viewsNext = 'views_asc';
                    $viewsClass = 'btn-primary';
                    $viewsIcon = '<i class="bi bi-sort-numeric-down ms-1"></i>';
                } elseif ($currentSort === 'views_asc') {
                    $viewsNext = null;
                    $viewsClass = 'btn-primary';
                    $viewsIcon = '<i class="bi bi-sort-numeric-up-alt ms-1"></i>';
                }


                $nameNext = 'name_asc';
                $nameClass = 'btn-outline-light';
                $nameIcon = '<i class="bi bi-arrow-down-up ms-1"></i>';
                if ($currentSort === 'name_asc') {
                    $nameNext = 'name_desc';
                    $nameClass = 'btn-primary';
                    $nameIcon = '<i class="bi bi-sort-alpha-down ms-1"></i>';
                } elseif ($currentSort === 'name_desc') {
                    $nameNext = null;
                    $nameClass = 'btn-primary';
                    $nameIcon = '<i class="bi bi-sort-alpha-up-alt ms-1"></i>';
                }


                $alphabetNext = 'alphabet_asc';
                $alphabetClass = 'btn-outline-light';
                $alphabetIcon = '<i class="bi bi-arrow-down-up ms-1"></i>';
                if ($currentSort === 'alphabet_asc' || $currentSort === 'alphabet' || request('letter')) {
                    $alphabetNext = 'alphabet_desc';
                    $alphabetClass = 'btn-primary';
                    $alphabetIcon = '<i class="bi bi-sort-alpha-down ms-1"></i>';
                } elseif ($currentSort === 'alphabet_desc') {
                    $alphabetNext = null;
                    $alphabetClass = 'btn-primary';
                    $alphabetIcon = '<i class="bi bi-sort-alpha-up-alt ms-1"></i>';
                }
            @endphp

            <a href="{{ route('authors.index', ['sort' => $countNext]) }}"
                class="btn {{ $countClass }} rounded-pill px-4">По количеству{!! $countIcon !!}</a>
            <a href="{{ route('authors.index', ['sort' => $viewsNext]) }}"
                class="btn {{ $viewsClass }} rounded-pill px-4">По просмотрам{!! $viewsIcon !!}</a>
            <a href="{{ route('authors.index', ['sort' => $nameNext]) }}" class="btn {{ $nameClass }} rounded-pill px-4">По
                имени{!! $nameIcon !!}</a>
            <a href="{{ route('authors.index', ['sort' => $alphabetNext]) }}"
                class="btn {{ $alphabetClass }} rounded-pill px-4">По алфавиту{!! $alphabetIcon !!}</a>
        </div>

        @if(isset($letters) && $letters->isNotEmpty())
            <div class="d-flex flex-wrap gap-2 mb-5 p-4 bg-dark-card rounded-4 animate-fade-in-up delay-150">
                <a href="{{ route('authors.index', ['sort' => 'alphabet']) }}"
                    class="btn btn-sm alphabet-filter {{ !request('letter') ? 'btn-primary' : 'btn-outline-light' }}">Все</a>
                @foreach($letters as $l)
                    <a href="{{ route('authors.index', ['sort' => 'alphabet', 'letter' => $l]) }}"
                        class="btn btn-sm alphabet-filter {{ request('letter') == $l ? 'btn-primary' : 'btn-outline-light' }}">
                        {{ $l }}
                    </a>
                @endforeach
            </div>
        @endif

        <section>
            <h2 class="visually-hidden">Список наших авторов</h2>
            @if($authors->count() > 0)
                <div class="row g-3 animate-fade-in-up delay-200">
                    @foreach($authors as $author)
                        <div class="col-12">
                            <a href="{{ route('authors.show', $author->slug) }}" class="text-decoration-none group">
                                <article
                                    class="card bg-dark-card border-white-10 shadow-sm hover-card-lift position-relative overflow-hidden">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-4">
                                                <img src="{{ $author->photo ? asset('storage/' . $author->photo) : asset('images/no-cover.svg') }}"
                                                    alt="{{ $author->name }}"
                                                    class="rounded-circle border border-white-10 shadow-sm"
                                                    style="width: 80px; height: 80px; object-fit: cover;">
                                            </div>
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="mb-2">
                                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                                        <span
                                                            class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 ms-0">
                                                            {{ $author->books_count }}
                                                            {{ trans_choice('книга|книги|книг', $author->books_count) }}
                                                        </span>
                                                        @php
                                                            $totalViews = $author->books->sum('views');
                                                            $totalReviews = $author->books->sum('reviews_count');
                                                        @endphp
                                                        <span
                                                            class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3"
                                                            title="Просмотры">
                                                            <i class="bi bi-eye me-1"></i>
                                                            {{ number_format($totalViews, 0, ',', ' ') }}
                                                        </span>
                                                        <span
                                                            class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3"
                                                            title="Комментарии">
                                                            <i class="bi bi-chat-left-text me-1"></i> {{ $totalReviews }}
                                                        </span>
                                                    </div>
                                                    <h3
                                                        class="h5 fw-bold text-white mb-2 text-truncate group-hover:text-primary transition-colors">
                                                        {{ $author->name }}
                                                    </h3>

                                                </div>
                                                <p class="text-white-50 small mb-0 text-truncate d-none d-md-block">
                                                    @if($author->bio)
                                                        {{ Str::limit(strip_tags($author->bio), 100) }}
                                                    @else
                                                        Биографические данные и информация об авторе...
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="ms-3">
                                                <i
                                                    class="bi bi-chevron-right text-white-50 fs-5 group-hover:text-primary transition-colors"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Decorative bg icon -->
                                    <i class="bi bi-person position-absolute bottom-0 end-0 text-white opacity-5"
                                        style="font-size: 5rem; transform: translate(20%, 30%) rotate(-15deg);"></i>
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