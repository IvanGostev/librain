@props(['book'])

<div class="card bg-dark-card border-0 h-100 hover-card-lift transition-transform group rounded-4 overflow-hidden mx-auto vertical-book-card-wrapper">
    <div class="position-relative">
        <a href="{{ route('books.show', ['genre' => $book->genre_slug, 'slug' => $book->slug]) }}"
            class="d-block overflow-hidden rounded-4">
            <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/no-cover.svg') }}"
                alt="{{ $book->title }}"
                class="w-100 object-fit-contain transition-transform group-hover:scale-105 duration-500 bg-dark"
                style="aspect-ratio: 3/4;" onerror="this.src='{{ asset('images/no-cover.svg') }}'">
        </a>

        @auth
            @php 
                $entry = auth()->user()->libraryEntries()->where('book_id', $book->id)->first();
                $status = $entry ? $entry->status : null;
                $isFavorite = $entry ? $entry->is_favorite : false;
            @endphp
            <div class="dropdown position-absolute top-0 end-0 m-2 z-2">
                <button class="btn btn-sm btn-dark bg-opacity-75 p-2 rounded-circle border-0 lh-1 shadow-sm hover-scale-110 transition-transform" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-heart{{ $isFavorite ? '-fill text-danger' : '' }}"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end bg-dark-card border-white-10 shadow-lg p-1" style="min-width: 170px; font-size: 0.85rem;">
                    <li>
                        <form action="{{ route('books.favorite', $book->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item rounded-2 d-flex align-items-center gap-2 py-2 {{ $isFavorite ? 'text-danger' : 'text-white-50 hover-text-white' }}">
                                <i class="bi bi-heart{{ $isFavorite ? '-fill' : '' }}"></i>
                                {{ $isFavorite ? 'В избранном' : 'В избранное' }}
                            </button>
                        </form>
                    </li>
                    <li><hr class="dropdown-divider border-white-10 my-1"></li>
                    <li>
                        <form action="{{ route('books.status', $book->id) }}" method="POST">
                            @csrf <input type="hidden" name="status" value="planned">
                            <button type="submit" class="dropdown-item rounded-2 d-flex align-items-center gap-2 py-2 {{ $status === 'planned' ? 'active bg-primary text-white' : 'text-white-50 hover-text-white' }}">
                                <i class="bi bi-calendar-plus"></i> В планах
                            </button>
                        </form>
                    </li>
                    <li>
                        <form action="{{ route('books.status', $book->id) }}" method="POST">
                            @csrf <input type="hidden" name="status" value="reading">
                            <button type="submit" class="dropdown-item rounded-2 d-flex align-items-center gap-2 py-2 {{ $status === 'reading' ? 'active bg-primary text-white' : 'text-white-50 hover-text-white' }}">
                                <i class="bi bi-book"></i> Читаю
                            </button>
                        </form>
                    </li>
                    <li>
                        <form action="{{ route('books.status', $book->id) }}" method="POST">
                            @csrf <input type="hidden" name="status" value="finished">
                            <button type="submit" class="dropdown-item rounded-2 d-flex align-items-center gap-2 py-2 {{ $status === 'finished' ? 'active bg-primary text-white' : 'text-white-50 hover-text-white' }}">
                                <i class="bi bi-check-circle"></i> Прочитано
                            </button>
                        </form>
                    </li>
                    @if($status)
                        <li><hr class="dropdown-divider border-white-10 my-1"></li>
                        <li>
                            <form action="{{ route('books.status', $book->id) }}" method="POST">
                                @csrf <input type="hidden" name="status" value="none">
                                <button type="submit" class="dropdown-item rounded-2 d-flex align-items-center gap-2 py-2 text-danger hover-bg-danger bg-opacity-10">
                                    <i class="bi bi-trash"></i> Убрать
                                </button>
                            </form>
                        </li>
                    @endif
                </ul>
            </div>
        @endauth
        @guest
            <div class="dropdown position-absolute top-0 end-0 m-2 z-2">
                <button class="btn btn-sm btn-dark bg-opacity-75 p-2 rounded-circle border-0 lh-1 shadow-sm hover-scale-110 transition-transform" type="button"
                    onclick="alert('Вы не авторизованы. Пожалуйста, войдите в систему.')">
                    <i class="bi bi-heart"></i>
                </button>
            </div>
        @endguest

        <div class="position-absolute bottom-0 start-0 w-100 p-2 bg-gradient-to-t from-black-80 to-transparent">
            <span
                class="badge bg-black bg-opacity-50 text-warning border border-white-10 backdrop-blur-sm rounded-pill px-2 py-1 fs-7">
                <i class="bi bi-star-fill text-warning me-1" style="font-size: 0.7rem;"></i>
                {{ number_format($book->rating, 1) }}
            </span>
        </div>
    </div>

    <div class="card-body p-2 p-sm-3 d-flex flex-column">
        <p class="card-title fw-bold text-white book-title-responsive mb-1 text-truncate-2">
            <a href="{{ route('books.show', ['genre' => $book->genre_slug, 'slug' => $book->slug]) }}"
                class="text-white text-decoration-none hover-text-primary transition-colors">
                {{ $book->title }}
            </a>
        </p>

        <p class="card-text text-muted small mb-1">
            {{ $book->created_at ? $book->created_at->format('d.m.Y') . ' - ' : '' }}
            @if($book->authors->isNotEmpty())
                @foreach($book->authors as $author)
                    <a href="{{ route('authors.show', $author->slug) }}" class="text-muted text-decoration-none hover-text-primary transition-colors">{{ $author->name }}</a>{{ !$loop->last ? ', ' : '' }}
                @endforeach
            @else
                Автор неизвестен
            @endif
        </p>

        <p class="card-text text-muted small mb-2" title="{{ $book->genres->isNotEmpty() ? $book->genres->first()->name : 'Без жанра' }}">
            <i class="bi bi-tags me-1"></i>{{ $book->genres->isNotEmpty() ? $book->genres->first()->name : 'Без жанра' }}
        </p>

        <div class="mt-auto pt-2 border-top border-white-10 d-flex justify-content-between align-items-center">
            <div class="small text-white-50">
                <i class="bi bi-eye me-1"></i> {{ number_format($book->views, 0, ',', ' ') }}
            </div>
            <div class="small text-white-50">
                <i class="bi bi-chat-left-text me-1"></i> {{ $book->reviews_count ?? 0 }}
            </div>
        </div>
    </div>
</div>

<style>
    .vertical-book-card-wrapper {
        width: 100%;
    }
    @media (max-width: 575.98px) {
        .vertical-book-card-wrapper {
            max-width: 260px;
        }
    }

    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .hover-scale-110:hover {
        transform: scale(1.1);
    }

    .fs-7 {
        font-size: 0.75rem;
    }

    .book-title-responsive {
        font-size: 1rem;
        line-height: 1.25;
    }
    
    @media (max-width: 575.98px) {
        .book-title-responsive {
            font-size: 1.05rem;
        }
    }
</style>