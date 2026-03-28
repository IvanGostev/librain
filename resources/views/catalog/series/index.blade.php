@extends('layouts.app')
@section('og_image', asset('favicon.svg'))
@section('content')
    <div class="container pb-5">
        <h1 class="h2 fw-bold text-white mb-4">Серии</h1>
        <div class="d-flex gap-2 mb-4 flex-wrap animate-fade-in-up delay-100">
            @php
                $currentSort = request('sort');
            @endphp
            @if($currentSort === 'count_desc' || $currentSort === 'count')
                <a href="{{ route('series.index', ['sort' => 'count_asc']) }}" class="btn btn-primary rounded-pill px-4">По
                    количеству<i class="bi bi-sort-numeric-down ms-1"></i></a>
            @elseif($currentSort === 'count_asc')
                <a href="{{ route('series.index', ['sort' => 'count_desc']) }}" class="btn btn-primary rounded-pill px-4">По
                    количеству<i class="bi bi-sort-numeric-up-alt ms-1"></i></a>
            @else
                <a href="{{ route('series.index', ['sort' => 'count_desc']) }}"
                    class="btn btn-outline-light rounded-pill px-4">По количеству<i class="bi bi-arrow-down-up ms-1"></i></a>
            @endif
            @if($currentSort === 'views_desc' || $currentSort === 'views')
                <a href="{{ route('series.index', ['sort' => 'views_asc']) }}" class="btn btn-primary rounded-pill px-4">По
                    просмотрам<i class="bi bi-sort-numeric-down ms-1"></i></a>
            @elseif($currentSort === 'views_asc')
                <a href="{{ route('series.index', ['sort' => 'views_desc']) }}" class="btn btn-primary rounded-pill px-4">По
                    просмотрам<i class="bi bi-sort-numeric-up-alt ms-1"></i></a>
            @else
                <a href="{{ route('series.index', ['sort' => 'views_desc']) }}"
                    class="btn btn-outline-light rounded-pill px-4">По просмотрам<i class="bi bi-arrow-down-up ms-1"></i></a>
            @endif
            @if($currentSort === 'name_asc')
                <a href="{{ route('series.index', ['sort' => 'name_desc']) }}" class="btn btn-primary rounded-pill px-4">По
                    имени<i class="bi bi-sort-alpha-down ms-1"></i></a>
            @elseif($currentSort === 'name_desc')
                <a href="{{ route('series.index', ['sort' => 'name_asc']) }}" class="btn btn-primary rounded-pill px-4">По
                    имени<i class="bi bi-sort-alpha-up-alt ms-1"></i></a>
            @else
                <a href="{{ route('series.index', ['sort' => 'name_asc']) }}" class="btn btn-outline-light rounded-pill px-4">По
                    имени<i class="bi bi-arrow-down-up ms-1"></i></a>
            @endif
            @if($currentSort === 'alphabet_asc' || $currentSort === 'alphabet' || request('letter'))
                <a href="{{ route('series.index', ['sort' => 'alphabet_desc']) }}" class="btn btn-primary rounded-pill px-4">По
                    алфавиту<i class="bi bi-sort-alpha-down ms-1"></i></a>
            @elseif($currentSort === 'alphabet_desc')
                <a href="{{ route('series.index', ['sort' => 'alphabet_asc']) }}" class="btn btn-primary rounded-pill px-4">По
                    алфавиту<i class="bi bi-sort-alpha-up-alt ms-1"></i></a>
            @else
                <a href="{{ route('series.index', ['sort' => 'alphabet_asc']) }}"
                    class="btn btn-outline-light rounded-pill px-4">По алфавиту<i class="bi bi-arrow-down-up ms-1"></i></a>
            @endif
        </div>
        @if(isset($letters) && $letters->isNotEmpty())
            <div class="d-flex flex-wrap gap-2 mb-5 p-4 bg-dark-card rounded-4 animate-fade-in-up delay-150">
                @if(!request('letter'))
                    <span class="btn btn-sm alphabet-filter btn-primary cursor-default">Все</span>
                @else
                    <a href="{{ route('series.index', ['sort' => 'alphabet']) }}"
                        class="btn btn-sm alphabet-filter btn-outline-light">Все</a>
                @endif
                @foreach($letters as $l)
                    @if(request('letter') == $l)
                        <span class="btn btn-sm alphabet-filter btn-primary cursor-default">{{ $l }}</span>
                    @else
                        <a href="{{ route('series.index', ['sort' => 'alphabet', 'letter' => $l]) }}"
                            class="btn btn-sm alphabet-filter btn-outline-light">
                            {{ $l }}
                        </a>
                    @endif
                @endforeach
            </div>
        @endif
        <section>
            @if($series->count() > 0)
                <div class="row g-3 animate-fade-in-up delay-200">
                    @foreach($series as $item)
                        <div class="col-12">
                            <a href="{{ route('series.show', $item->slug) }}" class="text-decoration-none group">
                                <article
                                    class="card bg-dark-card border-white-10 shadow-sm hover-card-lift position-relative overflow-hidden">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center">

                                            <div class="flex-grow-1 min-w-0">
                                                <div class="mb-2">
                                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                                        <span
                                                            class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3"
                                                            title="Книг в серии">
                                                            <i class="bi bi-book me-1"></i> {{ $item->books_count }}
                                                        </span>
                                                        @php
                                                            $totalViews = $item->books->sum('views');
                                                            $totalReviews = $item->books->sum('reviews_count');
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
                                                    <p
                                                        class="h5 fw-bold text-white mb-2 group-hover:text-secondary transition-colors">
                                                        {{ $item->name }}
                                                    </p>
                                                </div>
                                                <p class="text-white-50 small mb-0 text-truncate d-none d-md-block"
                                                    style="max-width: 80%;">
                                                    {{ $item->description ?? 'Описание отсутствует' }}
                                                </p>
                                            </div>
                                            <div class="ms-3">
                                                <i
                                                    class="bi bi-chevron-right text-white-50 fs-5 group-hover:text-secondary transition-colors"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <i class="bi bi-journals position-absolute bottom-0 end-0 text-white opacity-5"
                                        style="font-size: 5rem; transform: translate(20%, 30%) rotate(-15deg);"></i>
                                </article>
                            </a>
                        </div>
                    @endforeach
                </div>
                <div class="mt-5 d-flex justify-content-center">
                    {{ $series->links() }}
                </div>
            @else
                <div class="text-center py-5 text-muted animate-fade-in-up">
                    <i class="bi bi-journal-x fs-1 mb-3 d-block"></i>
                    <p>Серии пока не созданы.</p>
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
