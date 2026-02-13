@foreach($entries as $entry)
    <div class="col-12">
        <div class="card bg-dark-card border-white-10 d-flex flex-row p-3 hover-card-lift">
            <div class="position-relative flex-shrink-0">
                <a href="{{ route('books.show', ['genre' => $entry->book->genre_slug, 'slug' => $entry->book->slug]) }}">
                    <img src="{{ $entry->book->cover_image ? asset('storage/' . $entry->book->cover_image) : asset('images/no-cover.svg') }}"
                        class="rounded-3 shadow-sm" style="width: 80px; height: 120px; object-fit: cover;"
                        onerror="this.src='{{ asset('images/no-cover.svg') }}'">
                </a>
                <form action="{{ route('books.favorite', $entry->book->id) }}" method="POST"
                    class="position-absolute top-0 start-0 m-1">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-dark bg-opacity-75 p-1 rounded-circle border-0 lh-1">
                        <i class="bi bi-heart{{ $entry->is_favorite ? '-fill text-danger' : ' text-white' }}"
                            style="font-size: 0.8rem;"></i>
                    </button>
                </form>
            </div>
            <div class="flex-grow-1 ms-3 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <h6 class="text-white mb-0 text-truncate" title="{{ $entry->book->title }}">{{ $entry->book->title }}
                    </h6>
                    <div class="d-flex gap-1">
                        @if(!$entry->book->is_published)
                            <span
                                class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-2 py-0"
                                style="font-size: 0.6rem;">
                                Скрыто
                            </span>
                        @endif
                        @if($entry->book->status === 'writing')
                            <span
                                class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-2 py-0"
                                style="font-size: 0.6rem;">
                                В процессе
                            </span>
                        @endif
                    </div>
                </div>
                <p class="text-muted small mb-2 text-truncate">{{ $entry->book->author->name ?? 'Автор неизвестен' }}</p>

                <div class="mt-auto">
                    <!-- Progress Bar -->
                    <div class="progress bg-dark border border-white-10 mb-1" style="height: 6px;">
                        <div class="progress-bar bg-primary" role="progressbar"
                            style="width: {{ $entry->progress_percent }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-white-50 small">{{ $entry->progress_percent }}%</span>
                        <a href="{{ route('books.read', $entry->book->slug) }}"
                            class="btn btn-link btn-sm text-primary text-decoration-none p-0 small fw-bold">
                            {{ $entry->status == 'finished' ? 'Перечитать' : 'Читать' }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach