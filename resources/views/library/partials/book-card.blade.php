@foreach($entries as $entry)
    <div class="col-12">
        <div class="card bg-dark-card border-white-10 d-flex flex-row p-3 hover-card-lift">
            <div class="position-relative flex-shrink-0">
                <a href="{{ route('books.show', ['genre' => $entry->book->genre_slug, 'slug' => $entry->book->slug]) }}">
                    <img src="{{ $entry->book->cover_image ? asset('storage/' . $entry->book->cover_image) : asset('images/no-cover.svg') }}"
                        class="rounded-3 shadow-sm" style="width: 80px; height: 120px; object-fit: cover;"
                        onerror="this.src='{{ asset('images/no-cover.svg') }}'">
                </a>
                <div class="dropdown position-absolute top-0 start-0 m-1">
                    <button class="btn btn-sm btn-light shadow p-0 rounded-circle border-0 lh-1 d-flex align-items-center justify-content-center" 
                        type="button"
                        data-bs-toggle="dropdown" 
                        style="width: 28px; height: 28px;">
                        <i class="bi bi-three-dots-vertical text-dark fs-6"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark bg-dark-card border-white-10 shadow-lg fs-7"
                        style="min-width: 150px;">
                        <li>
                            <form action="{{ route('books.favorite', $entry->book->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="dropdown-item d-flex align-items-center gap-2 {{ $entry->is_favorite ? 'text-danger' : '' }}">
                                    <i class="bi bi-heart{{ $entry->is_favorite ? '-fill' : '' }}"></i>
                                    {{ $entry->is_favorite ? 'Убрать из избранного' : 'В избранное' }}
                                </button>
                            </form>
                        </li>
                        <li>
                            <hr class="dropdown-divider border-white-10 my-1">
                        </li>
                        @if($entry->status === 'blacklist')
                            <li>
                                <form action="{{ route('books.status', $entry->book->id) }}" method="POST">
                                    @csrf <input type="hidden" name="status" value="none">
                                    <button type="submit" class="dropdown-item d-flex align-items-center gap-2">
                                        <i class="bi bi-arrow-counterclockwise"></i> Вернуть из ЧС
                                    </button>
                                </form>
                            </li>
                        @else
                            <li>
                                <form action="{{ route('books.status', $entry->book->id) }}" method="POST">
                                    @csrf <input type="hidden" name="status" value="blacklist">
                                    <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-danger">
                                        <i class="bi bi-eye-slash"></i> В черный список
                                    </button>
                                </form>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="flex-grow-1 ms-3 d-flex flex-column overflow-hidden" style="min-width: 0;">
                <div class="d-flex justify-content-between align-items-start mb-1 gap-2">
                    <h6 class="text-white mb-0" title="{{ $entry->book->title }}">{{ $entry->book->title }}
                    </h6>
                    <div class="d-flex gap-1 flex-shrink-0">
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
                <p class="text-muted small mb-2">
                    @if($entry->book->authors->isNotEmpty())
                        {{ $entry->book->authors->pluck('name')->join(', ') }}
                    @else
                        Автор неизвестен
                    @endif
                </p>

                <div class="mt-auto">
                    <!-- Progress Bar -->
                    <div class="progress bg-dark border border-white-10 mb-1" style="height: 6px;">
                        <div class="progress-bar bg-primary" role="progressbar"
                            style="width: {{ $entry->progress_percent }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-white-50 small">{{ $entry->progress_percent }}%</span>
                        <a href="{{ route('books.show', ['genre' => $entry->book->genres->first()->slug ?? 'general', 'slug' => $entry->book->slug]) }}"
                            class="btn btn-link btn-sm text-primary text-decoration-none p-0 small fw-bold">
                            {{ $entry->status == 'finished' ? 'Перечитать' : 'Читать' }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach