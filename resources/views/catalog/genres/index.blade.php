@extends('layouts.app')
@section('og_image', asset('favicon.svg'))
@section('content')
    <div class="container pb-5">
        <h1 class="h2 fw-bold text-white mb-4">Книги по жанрам</h1>
        <section>
            @if($genres->count() > 0)
                <div class="row g-4 animate-fade-in-up delay-200">
                    @foreach($genres as $genre)
                        <div class="col-6 col-md-4 col-lg-3">
                            <a href="{{ route('genres.show', $genre->slug) }}" class="text-decoration-none group">
                                <article
                                    class="card bg-dark-card border-0 h-100 shadow-sm hover-card-lift position-relative overflow-hidden">
                                    <div class="card-body p-4 text-center d-flex flex-column align-items-center justify-content-center"
                                        style="min-height: 180px;">
                                        <div class="position-absolute top-50 start-50 translate-middle rounded-circle bg-primary genre-glow"></div>
                                        <div class="position-relative z-1">
                                            <p class="h4 fw-bold genre-title mb-2 group-hover:text-primary transition-colors">
                                                {{ $genre->name }}
                                            </p>
                                            <span
                                                class="badge bg-white bg-opacity-10 text-white rounded-pill border border-white-10 px-3"
                                                style="color: white !important;">
                                                {{ $genre->books_count . ($genre->books_count == 1 ? ' книга' : " книг") }}
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
        /* Override for light theme to keep it white and add shadow */
        [data-bs-theme="light"] .genre-title {
            color: white !important;
            text-shadow: 0 1px 6px var(--bs-primary), 0 0 12px var(--bs-primary);
        }
        /* Text shadow for badge text too */
        [data-bs-theme="light"] .group .badge {
            text-shadow: 0 1px 4px var(--bs-primary);
        }
        [data-bs-theme="light"] .group:hover .group-hover\:text-primary {
            color: var(--bs-primary) !important;
        }
        
        /* Genre Card Glow Effect */
        .genre-glow {
            width: 160px;
            height: 160px;
            filter: blur(25px);
            opacity: 0.15;
            transition: all 0.3s ease;
        }
        
        [data-bs-theme="light"] .genre-glow {
            width: 180px;
            height: 180px;
            background-color: var(--bs-primary) !important;
            filter: blur(30px);
            opacity: 0.85;
        }
    </style>
@endsection
