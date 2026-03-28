@props(['book'])
<div class="card bg-dark-card border-white-10 p-3 hover-card-lift transition-transform">
    <div class="d-flex align-items-center">
        <div class="flex-shrink-0 me-3 position-relative">
            <a href="{{ route('books.show', ['genre' => $book->genre_slug, 'slug' => $book->slug]) }}">
                <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/no-cover.svg') }}"
                    alt="{{ $book->title }}" class="rounded shadow-sm bg-dark"
                    style="width: 80px; height: 120px; object-fit: contain;"
                    onerror="this.src='{{ asset('images/no-cover.svg') }}'">
            </a>
            @auth
                @php
                    $entry = auth()->user()->libraryEntries()->where('book_id', $book->id)->first();
                    $status = $entry ? $entry->status : null;
                    $isFavorite = $entry ? $entry->is_favorite : false;
                @endphp
                <div class="dropdown position-absolute top-0 start-0 m-1">
                    <button class="btn btn-sm btn-dark bg-opacity-75 p-1 rounded-circle border-0 lh-1" type="button"
                        data-bs-toggle="dropdown">
                        <i class="bi bi-heart{{ $isFavorite ? '-fill text-danger' : ' text-white' }}"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark bg-dark-card border-white-10 shadow-lg fs-7"
                        style="min-width: 150px;">
                        <li>
                            <form action="{{ route('books.favorite', $book->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="dropdown-item d-flex align-items-center gap-2 {{ $isFavorite ? 'text-danger' : '' }}">
                                    <i class="bi bi-heart{{ $isFavorite ? '-fill' : '' }}"></i>
                                    {{ $isFavorite ? 'В избранном' : 'В избранное' }}
                                </button>
                            </form>
                        </li>
                        <li>
                            <hr class="dropdown-divider border-white-10 my-1">
                        </li>
                        <li>
                            <form action="{{ route('books.status', $book->id) }}" method="POST">
                                @csrf <input type="hidden" name="status" value="planned">
                                <button type="submit"
                                    class="dropdown-item d-flex align-items-center gap-2 {{ $status === 'planned' ? 'active' : '' }}">
                                    <i class="bi bi-calendar-plus"></i> В планах
                                </button>
                            </form>
                        </li>
                        <li>
                            <form action="{{ route('books.status', $book->id) }}" method="POST">
                                @csrf <input type="hidden" name="status" value="reading">
                                <button type="submit"
                                    class="dropdown-item d-flex align-items-center gap-2 {{ $status === 'reading' ? 'active' : '' }}">
                                    <i class="bi bi-book"></i> Читаю
                                </button>
                            </form>
                        </li>
                        <li>
                            <form action="{{ route('books.status', $book->id) }}" method="POST">
                                @csrf <input type="hidden" name="status" value="finished">
                                <button type="submit"
                                    class="dropdown-item d-flex align-items-center gap-2 {{ $status === 'finished' ? 'active' : '' }}">
                                    <i class="bi bi-check-circle"></i> Прочитано
                                </button>
                            </form>
                        </li>
                        @if($status)
                            <li>
                                <hr class="dropdown-divider border-white-10 my-1">
                            </li>
                            <li>
                                <form action="{{ route('books.status', $book->id) }}" method="POST">
                                    @csrf <input type="hidden" name="status" value="none">
                                    <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-danger">
                                        <i class="bi bi-trash"></i> Убрать
                                    </button>
                                </form>
                            </li>
                        @endif
                    </ul>
                </div>
            @endauth
            @guest
                <div class="dropdown position-absolute top-0 start-0 m-1">
                    <button class="btn btn-sm btn-dark bg-opacity-75 p-1 rounded-circle border-0 lh-1" type="button"
                        onclick="alert('Вы не авторизованы. Пожалуйста, войдите в систему.')">
                        <i class="bi bi-heart text-white"></i>
                    </button>
                </div>
            @endguest
        </div>
        <div class="flex-grow-1 min-w-0">
            <div class="d-flex flex-column h-100">
                <div class="mb-2">
                    <p class="fw-bold text-white mb-1">
                        <a href="{{ route('books.show', ['genre' => $book->genre_slug, 'slug' => $book->slug]) }}"
                            class="text-white text-decoration-none hover-text-primary transition-colors">
                            {{ $book->title }}
                        </a>
                    </p>
                    <p class="text-muted small mb-0">
                        <i class="bi bi-person me-1"></i> {{ $book->authors->isNotEmpty() ? $book->authors->pluck('name')->join(', ') : 'Автор неизвестен' }}
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
        <div class="ms-3 d-none d-sm-block">
            <a href="{{ route('books.show', ['genre' => $book->genre_slug, 'slug' => $book->slug]) }}"
                class="btn btn-outline-primary rounded-pill px-4">
                Читать
            </a>
        </div>
    </div>
</div>