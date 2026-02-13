@props(['book'])

<div class="card bg-dark-card border-0 h-100 hover-card-lift transition-transform group rounded-4 overflow-hidden">
    <div class="position-relative">
        <a href="{{ route('books.show', ['genre' => $book->genre_slug, 'slug' => $book->slug]) }}"
            class="d-block overflow-hidden rounded-4">
            <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/no-cover.svg') }}"
                alt="{{ $book->title }}"
                class="w-100 object-fit-cover transition-transform group-hover:scale-105 duration-500"
                style="aspect-ratio: 3/4;" onerror="this.src='{{ asset('images/no-cover.svg') }}'">
        </a>

        @auth
            @php $isFavorite = auth()->user()->isBookFavorite($book->id); @endphp
            <form action="{{ route('books.favorite', $book->id) }}" method="POST"
                class="position-absolute top-0 end-0 m-2 z-2">
                @csrf
                <button type="submit"
                    class="btn btn-sm btn-dark bg-opacity-75 p-2 rounded-circle border-0 lh-1 shadow-sm hover-scale-110 transition-transform"
                    title="{{ $isFavorite ? 'Удалить из избранного' : 'Добавить в избранное' }}">
                    <i class="bi bi-heart{{ $isFavorite ? '-fill text-danger' : '' }}"></i>
                </button>
            </form>
        @endauth

        <div class="position-absolute bottom-0 start-0 w-100 p-2 bg-gradient-to-t from-black-80 to-transparent">
            <span
                class="badge bg-black bg-opacity-50 text-warning border border-white-10 backdrop-blur-sm rounded-pill px-2 py-1 fs-7">
                <i class="bi bi-star-fill text-warning me-1" style="font-size: 0.7rem;"></i>
                {{ number_format($book->rating, 1) }}
            </span>
        </div>
    </div>

    <div class="card-body p-3 d-flex flex-column">
        <h5 class="card-title fw-bold text-white fs-6 mb-1 text-truncate-2" style="min-height: 2.5rem;">
            <a href="{{ route('books.show', ['genre' => $book->genre_slug, 'slug' => $book->slug]) }}"
                class="text-white text-decoration-none hover-text-primary transition-colors">
                {{ $book->title }}
            </a>
        </h5>

        <p class="card-text text-muted small mb-2">
            {{ $book->created_at ? $book->created_at->format('d.m.Y') . ' - ' : '' }}
            @if($book->author)
                <a href="{{ route('authors.show', $book->author->slug) }}"
                    class="text-muted text-decoration-none hover-text-primary transition-colors">
                    {{ $book->author->name }}
                </a>
            @else
                Автор неизвестен
            @endif
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
</style>