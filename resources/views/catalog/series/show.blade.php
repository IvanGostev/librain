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
                            <h4 class="fw-bold text-white mb-2">{{ $series->name }}</h4>
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
                <h2 class="fw-bold text-white mb-4 border-start border-4 border-secondary ps-3">Книги серии</h2>

                @if($series->books->count() > 0)
                    <div class="d-flex flex-column gap-3">
                        @foreach($series->books as $index => $book)
                            <div class="card bg-dark-card border-0 p-3 hover-card-lift transition-transform">
                                <div class="d-flex align-items-center">
                                    <!-- Order Badge -->
                                    <div class="me-3 d-flex flex-column align-items-center justify-content-center bg-dark rounded-3 border border-white-10"
                                        style="width: 50px; height: 50px; min-width: 50px;">
                                        <span class="small text-muted text-uppercase" style="font-size: 0.6rem;">Книга</span>
                                        <span class="h4 fw-bold text-white mb-0">{{ $book->pivot->order ?? $index + 1 }}</span>
                                    </div>

                                    <!-- Cover (Small) -->
                                    <a href="{{ route('books.show', $book->slug) }}" class="me-3 d-none d-sm-block">
                                        <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : 'https://placehold.co/100x150/1e293b/cbd5e1?text=Cover' }}"
                                            alt="{{ $book->title }}" class="rounded shadow-sm"
                                            style="width: 50px; height: 75px; object-fit: cover;">
                                    </a>

                                    <!-- Content -->
                                    <div class="flex-grow-1 min-w-0">
                                        <h5 class="fw-bold text-white mb-1 text-truncate">
                                            <a href="{{ route('books.show', $book->slug) }}"
                                                class="text-white text-decoration-none hover-text-secondary transition-colors">
                                                {{ $book->title }}
                                            </a>
                                        </h5>
                                        <p class="text-muted small mb-0 text-truncate">
                                            {{ $book->author->name ?? 'Автор неизвестен' }}
                                        </p>
                                    </div>

                                    <!-- Action -->
                                    <div class="ms-3">
                                        <a href="{{ route('books.show', $book->slug) }}"
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
                    <div class="text-center py-5 text-muted border border-dashed border-white-10 rounded-3">
                        <p class="mb-0">В этой серии пока нет книг.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection