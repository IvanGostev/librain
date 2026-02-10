@extends('layouts.app')

@section('content')
    <div class="container pb-5">
        <h1 class="h2 fw-bold text-white mb-4">Авторы</h1>

        <div class="d-flex gap-2 mb-4 flex-wrap animate-fade-in-up delay-100">
            <a href="{{ route('authors.index', ['sort' => 'count']) }}"
                class="btn {{ request('sort') === 'count' ? 'btn-light' : 'btn-outline-light' }} rounded-pill px-4">По
                количеству</a>
            <a href="{{ route('authors.index', ['sort' => 'views']) }}"
                class="btn {{ request('sort') === 'views' ? 'btn-light' : 'btn-outline-light' }} rounded-pill px-4">По
                просмотрам</a>

            @php
                $currentSort = request('sort');
                $nameNext = 'name_asc';
                $nameClass = 'btn-outline-light';
                $nameIcon = '';

                if ($currentSort === 'name_asc') {
                    $nameNext = 'name_desc';
                    $nameClass = 'btn-light';
                    $nameIcon = '<i class="bi bi-sort-alpha-down me-1"></i>';
                } elseif ($currentSort === 'name_desc') {
                    $nameNext = null;
                    $nameClass = 'btn-light';
                    $nameIcon = '<i class="bi bi-sort-alpha-up-alt me-1"></i>';
                }
            @endphp
            <a href="{{ route('authors.index', ['sort' => $nameNext]) }}"
                class="btn {{ $nameClass }} rounded-pill px-4">{!! $nameIcon !!}По имени</a>
            <a href="{{ route('authors.index', ['sort' => 'alphabet']) }}"
                class="btn {{ (request('sort') === 'alphabet' || request('letter')) ? 'btn-light' : 'btn-outline-light' }} rounded-pill px-4">По
                алфавиту</a>
        </div>

        @if(isset($letters) && $letters->isNotEmpty())
            <div class="d-flex flex-wrap gap-2 mb-5 p-4 bg-dark-card rounded-4 animate-fade-in-up delay-150">
                <a href="{{ route('authors.index', ['sort' => 'alphabet']) }}"
                    class="btn btn-sm {{ !request('letter') ? 'btn-primary' : 'btn-outline-secondary border-0 text-white-50 hover-text-white' }}">Все</a>
                @foreach($letters as $l)
                    <a href="{{ route('authors.index', ['sort' => 'alphabet', 'letter' => $l]) }}"
                        class="btn btn-sm {{ request('letter') == $l ? 'btn-primary' : 'btn-outline-secondary border-0 text-white-50 hover-text-white' }}">
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