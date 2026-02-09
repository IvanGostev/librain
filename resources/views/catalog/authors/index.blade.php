@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="text-center mb-5 animate-fade-in-up">
            <h6 class="text-primary text-uppercase tracking-wider fw-bold mb-2">Авторы</h6>
            <h1 class="display-4 fw-bold text-white mb-3">Наши писатели</h1>
            <p class="text-muted lead mx-auto" style="max-width: 600px;">
                Познакомьтесь с талантливыми создателями миров.
            </p>
        </div>

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
                                                <img src="{{ $author->photo ? asset('storage/' . $author->photo) : 'https://placehold.co/200x200/334155/cbd5e1?text=' . substr($author->name, 0, 1) }}"
                                                    alt="{{ $author->name }}"
                                                    class="rounded-circle border border-white-10 shadow-sm"
                                                    style="width: 80px; height: 80px; object-fit: cover;">
                                            </div>
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="d-flex justify-content-between align-items-start mb-1">
                                                    <h3
                                                        class="h5 fw-bold text-white mb-0 text-truncate group-hover:text-primary transition-colors">
                                                        {{ $author->name }}
                                                    </h3>
                                                    <span
                                                        class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 ms-2">
                                                        {{ $author->books_count }}
                                                        {{ trans_choice('книга|книги|книг', $author->books_count) }}
                                                    </span>
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
@endsection