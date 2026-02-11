@extends('layouts.app')

@section('content')
    <div class="container pb-5">
        <h1 class="h2 fw-bold text-white mb-4">Книги по жанрам</h1>

        <section>
            <h2 class="visually-hidden">Список всех жанров</h2>
            @if($genres->count() > 0)
                <div class="row g-4 animate-fade-in-up delay-200">
                    @foreach($genres as $genre)
                        <div class="col-6 col-md-4 col-lg-3">
                            <a href="{{ route('genres.show', $genre->slug) }}" class="text-decoration-none group">
                                <article
                                    class="card bg-dark-card border-0 h-100 shadow-sm hover-card-lift position-relative overflow-hidden">
                                    <div class="card-body p-4 text-center d-flex flex-column align-items-center justify-content-center"
                                        style="min-height: 180px;">
                                        <!-- Decorative Circle -->
                                        <div class="position-absolute top-50 start-50 translate-middle rounded-circle bg-primary opacity-10"
                                            style="width: 120px; height: 120px; filter: blur(20px);"></div>

                                        <div class="position-relative z-1">
                                            <h3 class="h4 fw-bold genre-title mb-2 group-hover:text-primary transition-colors">
                                                {{ $genre->name }}
                                            </h3>
                                            <span
                                                class="badge bg-white bg-opacity-10 text-white rounded-pill border border-white-10 px-3"
                                                style="color: white !important;">
                                                {{ $genre->books_count }}
                                                {{ trans_choice('книга|книги|книг', $genre->books_count) }}
                                            </span>
                                        </div>
                                    </div>
                                </article>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5 text-muted animate-fade-in-up">
                    <i class="bi bi-inbox fs-1 mb-3 d-block"></i>
                    <p>Жанры пока не добавлены.</p>
                </div>
            @endif
        </section>
    </div>

    <style>
        .transition-colors {
            transition: color 0.3s ease;
        }

        .group:hover .group-hover\:text-primary {
            color: var(--bs-primary) !important;
        }

        /* Genre title color - always white */
        .genre-title {
            color: white !important;
        }

        /* Override for light theme to keep it white */
        [data-bs-theme="light"] .genre-title {
            color: white !important;
        }

        [data-bs-theme="light"] .group:hover .group-hover\:text-primary {
            color: var(--bs-primary) !important;
        }
    </style>
@endsection