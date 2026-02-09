@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="text-center mb-5 animate-fade-in-up">
            <h6 class="text-secondary text-uppercase tracking-wider fw-bold mb-2">Серии</h6>
            <h1 class="display-4 fw-bold text-white mb-3">Коллекции книг</h1>
            <p class="text-muted lead mx-auto" style="max-width: 600px;">
                Истории, которые не заканчиваются на одной книге.
            </p>
        </div>

        <section>
            <h2 class="visually-hidden">Список книжных серий</h2>
            @if($series->count() > 0)
                <div class="row g-3 animate-fade-in-up delay-200">
                    @foreach($series as $item)
                            <div class="col-12">
                                <a href="{{ route('series.show', $item->slug) }}" class="text-decoration-none group">
                                    <article
                                        class="card bg-dark-card border-white-10 shadow-sm hover-card-lift position-relative overflow-hidden">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-4">
                                                    <img src="{{ $item->cover ? asset('storage/' . $item->cover) : asset('images/no-cover.svg') }}"
                                                        alt="{{ $item->name }}" class="rounded-3 shadow-sm border border-white-10"
                                                        style="width: 80px; height: 80px; object-fit: cover;"
                                                        onerror="this.src='{{ asset('images/no-cover.svg') }}'">
                                                </div>
                                                <div class="flex-grow-1 min-w-0">
                                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                                        <h3
                                                        class="h5 fw-bold text-white mb-0 text-truncate group-hover:text-secondary transition-colors">
                                                        {{ $item->name }}
                                                    </h3>
                                                        <div class="d-flex flex-wrap gap-2 ms-3">
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
                                        <!-- Decorative bg icon -->
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
@endsection