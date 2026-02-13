@props(['book'])

<div class="card bg-dark-card border-white-10 p-3 hover-card-lift transition-transform">
    <div class="d-flex align-items-center">
        <!-- Cover -->
        <div class="flex-shrink-0 me-3 position-relative">
            <a href="{{ route('books.show', ['genre' => $book->genre_slug, 'slug' => $book->slug]) }}">
                <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/no-cover.svg') }}"
                    alt="{{ $book->title }}" class="rounded shadow-sm"
                    style="width: 80px; height: 120px; object-fit: cover;"
                    onerror="this.src='{{ asset('images/no-cover.svg') }}'">
            </a>
            @auth
                @php $isFavorite = auth()->user()->isBookFavorite($book->id); @endphp
                <form action="{{ route('books.favorite', $book->id) }}" method="POST"
                    class="position-absolute top-0 start-0 m-1">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-dark bg-opacity-75 p-1 rounded-circle border-0 lh-1"
                        title="{{ $isFavorite ? 'Удалить из избранного' : 'Добавить в избранное' }}">
                        <i class="bi bi-heart{{ $isFavorite ? '-fill text-danger' : ' text-white' }}"
                            style="font-size: 0.8rem;"></i>
                    </button>
                </form>
            @endauth
        </div>

        <!-- Content -->
        <div class="flex-grow-1 min-w-0">
            <div class="d-flex flex-column h-100">
                <div class="mb-2">
                    <h5 class="fw-bold text-white mb-1 text-truncate">
                        <a href="{{ route('books.show', ['genre' => $book->genre_slug, 'slug' => $book->slug]) }}"
                            class="text-white text-decoration-none hover-text-primary transition-colors">
                            {{ $book->title }}
                        </a>
                    </h5>
                    <p class="text-muted small mb-0">
                        <i class="bi bi-person me-1"></i> {{ $book->author->name ?? 'Автор неизвестен' }}
                    </p>
                </div>

                <div class="mt-auto d-flex align-items-center gap-3">
                    <span
                        class="badge bg-black bg-opacity-25 text-warning border border-white-10 rounded-pill px-2 py-1">
                        <i class="bi bi-star-fill small me-1"></i> {{ number_format($book->rating, 1) }}
                    </span>
                    @if($book->genres->count() > 0)
                        <div class="d-none d-md-flex gap-2">
                            @foreach($book->genres->take(2) as $genre)
                                <span
                                    class="badge bg-white bg-opacity-10 text-white-50 border border-white-10 rounded-pill px-2">
                                    {{ $genre->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Action Button -->
        <div class="ms-3 d-none d-sm-block">
            <a href="{{ route('books.show', ['genre' => $book->genre_slug, 'slug' => $book->slug]) }}"
                class="btn btn-outline-primary rounded-pill px-4">
                Читать
            </a>
        </div>
    </div>
</div>