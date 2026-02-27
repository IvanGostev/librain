@extends('layouts.app')
@section('title', trim($book->title . ' ' . \App\Models\SiteSetting::where('key', $book->seo_title ?: 'seo_title_book_default')->value('value')))
@section('og_image', $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/no-cover.svg'))
@section('schema')
    <script type="application/ld+json">
                                                                                                                                                                {
                                                                                                                                                                  "@@context": "https://schema.org",
                                                                                                                                                                  "@@type": "Book",
                                                                                                                                                                  "name": "{{ $book->title }}",
                                                                                                                                                                  "author": {
                                                                                                                                                                    "@@type": "Person",
                                                                                                                                                                    "name": "{{ $book->authors->isNotEmpty() ? $book->authors->pluck('name')->join(', ') : 'Unknown' }}"
                                                                                                                                                                  },
                                                                                                                                                                  "description": "{{ Str::limit(strip_tags($book->description), 200) }}",
                                                                                                                                                                  "image": "{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/no-cover.svg') }}",
                                                                                                                                                                  "genre": "{{ $book->genres->pluck('name')->implode(', ') }}",
                                                                                                                                                                  "aggregateRating": {
                                                                                                                                                                    "@@type": "AggregateRating",
                                                                                                                                                                    "ratingValue": "{{ $book->rating }}",
                                                                                                                                                                    "reviewCount": "{{ $book->reviews->count() }}"
                                                                                                                                                                  }
                                                                                                                                                                }
  </script>
@endsection
@section('content')
    @php
        $totalSymbols = $book->chapters->sum('symbols_count');
        $totalPages = ceil($totalSymbols / 1500);
    @endphp
    <div class="container pt-0 pt-lg-5 pb-5 mt-0">
        <div class="row g-4 g-lg-5 position-relative">
            @if(!$book->is_published)
                <div
                    class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center z-3"
                    style="background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(10px); border-radius: 1rem;">
                    <div class="text-center p-5">
                        <i class="bi bi-eye-slash-fill display-1 text-white-50 mb-4 animate-bounce"></i>
                        <p class="fw-bold text-white mb-3 h2">Книга скрыта автором</p>
                        <p class="text-muted lead mb-4">Эта книга временно недоступна для чтения. Автор (админ) решил
                            скрыть ее
                            от публичного доступа.</p>
                        <a href="{{ route('catalog.index') }}" class="btn btn-primary rounded-pill px-5">Вернуться в
                            каталог</a>
                    </div>
                </div>
            @endif
            <div class="col-lg-4 col-md-5 animate-fade-in-up delay-100 position-relative" style="z-index: 10;">
                <div class="position-sticky" style="top: 100px;">
                    <div class="card bg-transparent border-0 mb-4">
                        <div class="position-relative rounded-3 overflow-hidden shadow-lg card-cover"
                             style="max-width: 300px; margin: 0 auto;">
                            <img
                                src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/no-cover.svg') }}"
                                class="w-100 object-fit-cover mx-auto d-block" alt="{{ $book->title }}"
                                style="width: 100%; aspect-ratio: 2/3;"
                                onerror="this.src='{{ asset('images/no-cover.svg') }}'">
                            <div class="position-absolute top-0 end-0 p-3">
                                <span class="badge bg-dark bg-opacity-75 backdrop-blur-sm fs-6"
                                      style="color: #fff !important;" id="cover-rating-display">
                                    <i class="bi bi-star-fill text-warning me-1"></i> {{ number_format($book->rating, 1) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mb-4">
                        @auth
                            <div class="flex-grow-1 dropdown">
                                <button
                                    class="btn btn-{{ $userStatus ? 'primary' : 'dark-glass border-white-10' }} w-100 rounded-pill py-2 fw-bold transition-all d-flex align-items-center justify-content-center gap-2 dropdown-toggle"
                                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-calendar-plus{{ $userStatus ? '-fill' : '' }}"></i>
                                    <span>
                                        @if($userStatus === 'planned')
                                            Хочу прочитать
                                        @elseif($userStatus === 'reading')
                                            Читаю
                                        @elseif($userStatus === 'finished')
                                            Прочитано
                                        @elseif($userStatus === 'dropped')
                                            Брошено
                                        @else
                                            Хочу прочитать
                                        @endif
                                    </span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-dark bg-dark-card border-white-10 shadow-lg w-100">
                                    <li>
                                        <form action="{{ route('books.status', $book->id) }}" method="POST">
                                            @csrf <input type="hidden" name="status" value="planned">
                                            <button class="dropdown-item d-flex align-items-center gap-2" type="submit">
                                                <i class="bi bi-calendar-plus"></i> Хочу прочитать
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="{{ route('books.status', $book->id) }}" method="POST">
                                            @csrf <input type="hidden" name="status" value="reading">
                                            <button class="dropdown-item d-flex align-items-center gap-2" type="submit">
                                                <i class="bi bi-book"></i> Читаю
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="{{ route('books.status', $book->id) }}" method="POST">
                                            @csrf <input type="hidden" name="status" value="finished">
                                            <button class="dropdown-item d-flex align-items-center gap-2" type="submit">
                                                <i class="bi bi-check-circle"></i> Прочитано
                                            </button>
                                        </form>
                                    </li>
                                    @if($userStatus)
                                        <li>
                                            <hr class="dropdown-divider border-white-10 my-1">
                                        </li>
                                        <li>
                                            <form action="{{ route('books.status', $book->id) }}" method="POST">
                                                @csrf <input type="hidden" name="status" value="none">
                                                <button
                                                    class="dropdown-item text-danger d-flex align-items-center gap-2"
                                                    type="submit">
                                                    <i class="bi bi-trash"></i> Убрать из списка
                                                </button>
                                            </form>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                            <form action="{{ route('books.favorite', $book->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="btn btn-{{ $isFavorite ? 'danger' : 'dark-glass border-white-10' }} rounded-pill py-2 fw-bold transition-all px-4"
                                        title="{{ $isFavorite ? 'Убрать из избранного' : 'В избранное' }}">
                                    <i class="bi bi-heart{{ $isFavorite ? '-fill' : '' }}"></i>
                                </button>
                            </form>
                        @else
                            <a href="#" onclick="event.preventDefault(); alert('Вы не авторизованы. Пожалуйста, войдите в систему.');"
                               class="btn btn-dark-glass border-white-10 flex-grow-1 rounded-pill py-2 fw-bold transition-all d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-calendar-plus"></i>
                                <span>Хочу прочитать</span>
                            </a>
                            <a href="#" onclick="event.preventDefault(); alert('Вы не авторизованы. Пожалуйста, войдите в систему.');"
                               class="btn btn-dark-glass border-white-10 rounded-pill py-2 fw-bold transition-all px-4"
                               title="В избранное">
                                <i class="bi bi-heart"></i>
                            </a>
                        @endauth
                    </div>
                    <div class="dropdown">
                        <button
                            class="btn btn-dark-glass btn-lg rounded-pill fw-semibold border-white-10 hover-elevate w-100"
                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-share-fill me-2"></i> Поделиться
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end bg-dark-card border-white-10 shadow-lg p-2 mt-2"
                            style="min-width: 200px; z-index: 9999;">
                            <li>
                                <a class="dropdown-item rounded-2 d-flex align-items-center gap-3 py-2 text-white-50 hover-text-white hover-bg-white-10 transition-colors"
                                   href="https://vk.com/share.php?url={{ urlencode(url()->current()) }}"
                                   target="_blank">
                                    <svg class="text-primary" width="20" height="20" viewBox="0 0 24 24"
                                         xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                                        <path
                                            d="M15.07294,2H8.9375C3.33331,2,2,3.33331,2,8.92706V15.0625C2,20.66663,3.32294,22,8.92706,22H15.0625C20.66669,22,22,20.67706,22,15.07288V8.9375C22,3.33331,20.67706,2,15.07294,2Zm3.07287,14.27081H16.6875c-.55206,0-.71875-.44793-1.70831-1.4375-.86463-.83331-1.22919-.9375-1.44794-.9375-.30206,0-.38544.08332-.38544.5v1.3125c0,.35419-.11456.5625-1.04162.5625a5.69214,5.69214,0,0,1-4.44794-2.66668A11.62611,11.62611,0,0,1,5.35419,8.77081c0-.21875.08331-.41668.5-.41668H7.3125c.375,0,.51044.16668.65625.55212.70831,2.08331,1.91669,3.89581,2.40625,3.89581.1875,0,.27081-.08331.27081-.55206V10.10413c-.0625-.97913-.58331-1.0625-.58331-1.41663a.36008.36008,0,0,1,.375-.33337h2.29169c.3125,0,.41662.15625.41662.53125v2.89587c0,.3125.13544.41663.22919.41663.1875,0,.33331-.10413.67706-.44788a11.99877,11.99877,0,0,0,1.79169-2.97919.62818.62818,0,0,1,.63544-.41668H17.9375c.4375,0,.53125.21875.4375.53125A18.20507,18.20507,0,0,1,16.41669,12.25c-.15625.23956-.21875.36456,0,.64581.14581.21875.65625.64582,1,1.05207a6.48553,6.48553,0,0,1,1.22912,1.70837C18.77081,16.0625,18.5625,16.27081,18.14581,16.27081Z"/>
                                    </svg>
                                    ВКонтакте
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-2 d-flex align-items-center gap-3 py-2 text-white-50 hover-text-white hover-bg-white-10 transition-colors"
                                   href="https://t.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode($book->title) }}"
                                   target="_blank">
                                    <i class="bi bi-telegram fs-5 text-info"></i> Telegram
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-2 d-flex align-items-center gap-3 py-2 text-white-50 hover-text-white hover-bg-white-10 transition-colors"
                                   href="https://wa.me/?text={{ urlencode($book->title . ' ' . url()->current()) }}"
                                   target="_blank">
                                    <i class="bi bi-whatsapp fs-5 text-success"></i> WhatsApp
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-2 d-flex align-items-center gap-3 py-2 text-white-50 hover-text-white hover-bg-white-10 transition-colors"
                                   href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($book->title) }}"
                                   target="_blank">
                                    <i class="bi bi-twitter-x fs-5 text-white"></i> X (Twitter)
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-2 d-flex align-items-center gap-3 py-2 text-white-50 hover-text-white hover-bg-white-10 transition-colors"
                                   href="https://connect.ok.ru/dk?st.cmd=WidgetSharePreview&st.shareUrl={{ urlencode(url()->current()) }}"
                                   target="_blank">
                                    <i class="bi bi-person-circle fs-5 text-warning"></i> Одноклассники
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-2 d-flex align-items-center gap-3 py-2 text-white-50 hover-text-white hover-bg-white-10 transition-colors"
                                   href="viber://forward?text={{ urlencode($book->title . ' ' . url()->current()) }}"
                                   target="_blank">
                                    <i class="bi bi-chat-fill fs-5 text-primary"></i> Viber
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider border-white-10 my-1">
                            </li>
                            <li>
                                <button
                                    class="dropdown-item rounded-2 d-flex align-items-center gap-3 py-2 text-white-50 hover-text-white hover-bg-white-10 transition-colors"
                                    onclick="navigator.clipboard.writeText('{{ url()->current() }}'); this.innerHTML = '<i class=\'bi bi-check2 fs-5 text-success\'></i> Скопировано!'; setTimeout(() => { this.innerHTML = '<i class=\'bi bi-link-45deg fs-5\'></i> Скопировать ссылку'; }, 2000);">
                                    <i class="bi bi-link-45deg fs-5"></i> Скопировать ссылку
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-md-7 animate-fade-in-up delay-200">
                <h1 class="display-5 fw-bold text-white mb-2">{{ $book->title }}</h1>
                <div class="mb-3 text-white fs-5 fw-semibold" style="line-height: 1.6;">
                    <span class="me-2">
                        @if($book->authors->isNotEmpty())
                            @foreach($book->authors as $author)
                                <a href="{{ route('authors.show', $author->slug) }}"
                                   class="text-decoration-none text-primary">
                                    {{ $author->name }}
                                </a>@if(!$loop->last)<span class="text-white">, </span>@endif
                            @endforeach
                        @else
                            <span class="text-white-50">Автор неизвестен</span>
                        @endif
                    </span>
                    <span class="text-white-50 me-2">•</span>
                    <span class="d-inline-flex align-items-center me-2">
                        <i class="bi bi-star-fill text-warning me-1"></i> <span class="text-white"
                                                                                id="average-rating-display">{{ number_format($book->rating, 1) }}</span>
                    </span>
                    <span class="text-white-50 me-2">•</span>
                    <span class="text-white me-2">{{ $book->views }} просмотров</span>
                    <span class="text-white-50 me-2">•</span>
                    <span>{{ $book->published_year }}</span>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-3 mb-4 text-muted small">
                    <span
                        class="badge bg-{{ $book->status === 'finished' ? 'success' : 'warning text-dark' }} rounded-pill">
                        {{ $book->status === 'finished' ? 'Завершена' : 'В процессе' }}
                    </span>
                    <span class="badge bg-secondary rounded-pill border border-white-10">{{ $book->age_rating }}</span>
                    <span title="Объем текста" class="d-flex align-items-center">
                        <i class="bi bi-file-earmark-text me-1"></i>
                        {{ number_format($totalSymbols, 0, ',', ' ') }} зн. / ~{{ $totalPages }} стр.
                    </span>
                    <span title="Дата публикации" class="d-flex align-items-center">
                        <i class="bi bi-calendar3 me-1"></i> {{ $book->created_at->format('d.m.Y') }}
                        @if($book->updated_at->gt($book->created_at))
                            <span class="text-white-50 ms-1">(обн. {{ $book->updated_at->format('d.m.Y') }})</span>
                        @endif
                    </span>
                </div>
                @if($book->series->isNotEmpty())
                    <div class="mb-4">
                        <span class="text-muted">Серия:</span>
                        @foreach($book->series as $series)
                            <a href="{{ route('series.show', $series->slug) }}"
                               class="text-decoration-none text-info fw-medium ms-1">
                                {{ $series->name }}
                            </a>{{ !$loop->last ? ',' : '' }}
                        @endforeach
                    </div>
                @endif
                <div class="mb-5">
                    <div class="d-flex flex-wrap gap-2 justify-content-start" id="user-rating-stars"
                         data-current-rating="{{ $userRating ?? 0 }}" onmouseleave="resetStars()">
                        @for($i = 1; $i <= 10; $i++)
                            <i class="bi bi-star{{ $userRating && $i <= $userRating ? '-fill text-warning' : ' text-white-50' }} fs-5 cursor-pointer hover-text-warning transition-colors user-rating-star"
                               data-rating="{{ $i }}" title="Оценка: {{ $i }}" onmouseenter="highlightStars({{ $i }})"
                               onclick="rateBook({{ $book->id }}, {{ $i }})"></i>
                        @endfor
                    </div>
                </div>
                <div class="mb-5">
                    <p class="text-white text-uppercase tracking-wider fw-bold mb-3 h5">Жанры</p>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($book->genres as $genre)
                            <a href="{{ route('genres.show', $genre->slug) }}"
                               class="btn btn-sm btn-dark-glass rounded-pill border-white-10">
                                {{ $genre->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="d-flex flex-column gap-3 mb-5 align-items-center align-items-lg-start">
                    <div class="d-flex flex-column gap-3" style="width: fit-content; max-width: 100%;">
                        <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                            <a href="{{ route('books.read', $book->slug) }}"
                               class="btn btn-primary rounded-pill px-4 fw-bold shadow-glow hover-elevate">
                                <i class="bi bi-book-half me-2"></i> Читать онлайн
                            </a>
                            @if($book->file_txt || $book->file_fb2 || $book->file_epub)
                                <a href="#downloads"
                                   class="btn btn-primary rounded-pill px-4 fw-bold shadow-glow hover-elevate d-flex align-items-center">
                                    <i class="bi bi-download me-2"></i>   Скачать книгу
                                </a>
                            @endif
                            <a href="#reviews-form"
                               class="btn btn-primary rounded-pill px-4 fw-bold shadow-glow hover-elevate">
                                <i class="bi bi-chat-text me-2"></i> Оставить отзыв
                            </a>
                        </div>
                        <a href="https://t.me/+mcfxd7HG3aM1NDRi" target="_blank"
                           class="btn w-100 rounded-4 fw-bold shadow-glow hover-elevate d-flex align-items-center justify-content-center gap-2"
                           style="background-color: #229ED9; border-color: #229ED9; color: #ffffff !important;">
                            <i class="bi bi-telegram fs-5" style="color: #ffffff !important;"></i>
                            <span style="color: #ffffff !important; white-space: normal; text-align: center;">Чтобы и дальше читать бесплатно подпишись на наш Telegram</span>
                        </a>
                    </div>
                </div>
                <div class="mb-5">
                    <h2 class="text-white text-uppercase tracking-wider fw-bold mb-3 h5">О книге</h2>
                    <div class="text-white lead" style="font-size: 1.1rem; line-height: 1.8;">
                        {!! nl2br(e($book->description)) !!}
                    </div>
                </div>
                <div class="mb-5 border-top border-white-10 pt-5">
                    <p class="text-white text-uppercase tracking-wider fw-bold mb-2 h5">Статистика</p>
                    <p class="text-white-50 mb-4 fw-medium">
                        Средний рейтинг: <span class="text-white fw-bold">{{ number_format($book->rating, 2) }}</span>
                        <i class="bi bi-star-fill text-warning"></i>
                    </p>
                    <div class="row g-4 g-lg-5">
                        <div class="col-md-6">
                            @for($i = 10; $i >= 1; $i--)
                                <div class="d-flex align-items-center mb-2">
                                    <div class="text-white text-end me-1 fw-bold fs-6"
                                         style="width: 20px;">{{ $i }}</div>
                                    <i class="bi bi-star-fill text-warning me-3 fs-6"></i>
                                    <div class="progress flex-grow-1 rounded-pill"
                                         style="height: 6px; background-color: rgba(150, 150, 150, 0.3) !important;">
                                        <div class="progress-bar bg-warning rounded-pill" role="progressbar"
                                             style="width: {{ $ratingCounts[$i]['percent'] }}%"
                                             aria-valuenow="{{ $ratingCounts[$i]['percent'] }}" aria-valuemin="0"
                                             aria-valuemax="100"></div>
                                    </div>
                                    <div class="text-white-50 ms-3 text-end" style="width: 35px; font-size: 0.85rem;">
                                        {{ number_format($ratingCounts[$i]['count'], 0, ',', ' ') }}
                                    </div>
                                </div>
                            @endfor
                        </div>
                        <div class="col-md-6">
                            @foreach($statusCounts as $key => $data)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="text-white-50 me-2 text-center" style="width: 24px;">
                                        <i class="bi {{ $data['icon'] }} fs-5"></i>
                                    </div>
                                    <div class="text-white me-3" style="width: 110px; font-size: 0.95rem;">
                                        {{ $data['label'] }}
                                    </div>
                                    <div class="progress flex-grow-1 rounded-pill"
                                         style="height: 6px; background-color: rgba(150, 150, 150, 0.3) !important;">
                                        <div class="progress-bar bg-{{ $data['color'] }} rounded-pill"
                                             role="progressbar" style="width: {{ $data['percent'] }}%"
                                             aria-valuenow="{{ $data['percent'] }}" aria-valuemin="0"
                                             aria-valuemax="100"></div>
                                    </div>
                                    <div class="text-white-50 ms-3 text-end" style="width: 35px; font-size: 0.85rem;">
                                        {{ number_format($data['count'], 0, ',', ' ') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @if($book->file_txt || $book->file_fb2 || $book->file_epub)
                    @php
                        $formatSize = function ($path) {
                            if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($path))
                                return '';
                            $bytes = \Illuminate\Support\Facades\Storage::disk('public')->size($path);
                            if ($bytes >= 1048576)
                                return number_format($bytes / 1048576, 2) . ' МБ';
                            if ($bytes >= 1024)
                                return number_format($bytes / 1024, 0) . ' КБ';
                            return $bytes . ' Б';
                        };
                    @endphp
                    <div class="mb-5 text-center text-lg-start" id="downloads">
                        <h2 class="text-white text-uppercase tracking-wider fw-bold mb-3 h5">Скачать книгу</h2>
                        <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                            @if($book->file_txt)
                                <a class="btn btn-outline-primary rounded-pill download-link"
                                   href="{{ route('books.download.page', ['book' => $book->id, 'format' => 'txt']) }}">
                                    <i class="bi bi-file-text fs-5 me-2"></i> Скачать TXT <span
                                        class="ms-1 opacity-75 small">{{ $formatSize($book->file_txt) }}</span>
                                </a>
                            @endif
                            @if($book->file_fb2)
                                <a class="btn btn-outline-info rounded-pill download-link"
                                   href="{{ route('books.download.page', ['book' => $book->id, 'format' => 'fb2']) }}">
                                    <i class="bi bi-book fs-5 me-2"></i> Скачать FB2 <span
                                        class="ms-1 opacity-75 small">{{ $formatSize($book->file_fb2) }}</span>
                                </a>
                            @endif
                            @if($book->file_epub)
                                <a class="btn btn-outline-success rounded-pill download-link"
                                   href="{{ route('books.download.page', ['book' => $book->id, 'format' => 'epub']) }}">
                                    <i class="bi bi-journal-richtext fs-5 me-2"></i> Скачать EPUB <span
                                        class="ms-1 opacity-75 small">{{ $formatSize($book->file_epub) }}</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
                @if($book->series->isNotEmpty())
                    @foreach($book->series as $series)
                        <div class="mb-5 border-top border-white-10 pt-5">
                            <div class="d-flex align-items-center mb-3">
                                <p class="text-white text-uppercase tracking-wider fw-bold mb-0 h5">
                                    Все книги из цикла «<a href="{{ route('series.show', $series->slug) }}"
                                                           class="text-white text-decoration-none hover-text-primary">{{ $series->name }}</a>»
                                </p>
                                <span class="text-muted fw-bold ms-3 fs-5">({{ $series->books->count() }})</span>
                            </div>
                            <div class="bg-dark-card rounded-3 overflow-hidden border border-white-10">
                                @foreach($series->books as $sBook)
                                    @if($sBook->id === $book->id)
                                        <div
                                            class="d-flex align-items-center py-3 px-4 text-decoration-none border-bottom border-white-10 bg-white-5">
                                            <i class="bi bi-journal-text fs-5 me-3 text-white-50"></i>
                                            <div class="flex-grow-1">
                                                <span class="text-white fw-medium">
                                                    {{ $loop->iteration }}. {{ $sBook->title }}
                                                </span>
                                            </div>
                                            <i class="bi bi-circle-fill text-primary ms-3" style="font-size: 0.5rem;"
                                               title="Текущая книга"></i>
                                        </div>
                                    @else
                                        <a href="{{ route('books.show', ['genre' => $sBook->genre_slug, 'slug' => $sBook->slug]) }}"
                                           class="d-flex align-items-center py-3 px-4 text-decoration-none border-bottom border-white-10 hover-bg-white-5 transition-colors group">
                                            <i
                                                class="bi bi-journal-text fs-5 me-3 text-white-50 group-hover:text-primary transition-colors"></i>
                                            <div class="flex-grow-1">
                                                <span
                                                    class="text-white fw-medium group-hover:text-primary transition-colors">
                                                    {{ $loop->iteration }}. {{ $sBook->title }}
                                                </span>
                                            </div>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif
                <div class="mb-5 border-top border-white-10 pt-5">
                    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3 mb-4">
                        <p class="text-white text-uppercase tracking-wider fw-bold mb-0 h5">Отзывы
                            @if($book->reviews->isNotEmpty())
                                <span
                                    class="text-muted ms-2">{{ $book->reviews->count() }}</span>
                            @endif
                        </p>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-light dropdown-toggle rounded-pill" type="button"
                                    data-bs-toggle="dropdown">
                                Сортировка:
                                {{ request('reviews_sort', 'newest') === 'best' ? 'По рейтингу' : 'Сначала новые' }}
                            </button>
                            <ul
                                class="dropdown-menu dropdown-menu-dark dropdown-menu-end bg-dark-card border-white-10 shadow-lg">
                                <li>
                                    <a class="dropdown-item {{ request('reviews_sort', 'newest') !== 'best' ? 'active' : '' }}"
                                       href="{{ request()->fullUrlWithQuery(['reviews_sort' => 'newest']) }}">Сначала
                                        новые</a></li>
                                <li><a class="dropdown-item {{ request('reviews_sort') === 'best' ? 'active' : '' }}"
                                       href="{{ request()->fullUrlWithQuery(['reviews_sort' => 'best']) }}">По
                                        рейтингу</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card bg-feature-card border-white-10 p-4 mb-5">
                        <p class="text-white fw-bold mb-3 h6">Оставить отзыв</p>
                        <form action="{{ route('reviews.store', $book->id) }}" method="POST" id="reviews-form">
                            @csrf
                            @guest
                                <div class="mb-3">
                                    <label class="form-label text-muted small">Ваше имя <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="guest_name" class="form-control form-control-dark" required
                                           placeholder="Представьтесь">
                                </div>
                            @endguest
                            <div class="mb-3">
                                <div class="mb-3">
                                    <textarea name="comment" class="form-control form-control-dark" rows="4"
                                              placeholder="Поделитесь вашим мнением о книге..." required></textarea>
                                </div>
                                <button type="submit"
                                        class="btn btn-primary rounded-pill px-4 fw-bold shadow-glow">Отправить
                                    отзыв
                                </button>
                        </form>
                    </div>
                    <div class="reviews-list">
                        @forelse($book->reviews as $review)
                            @include('catalog.books.partials.review', ['review' => $review, 'level' => 0])
                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-chat-left-dots fs-1 mb-3 d-block opacity-20"></i>
                                <p>Отзывов пока нет. Будьте первым!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-5 pt-5 border-top border-white-10" id="related-books-section">
            <p class="h3 fw-bold text-white mb-4 text-uppercase tracking-wider">Что еще можно почитать</p>
            <div class="d-flex flex-wrap gap-2 mb-4">
                <button class="btn btn-primary rounded-pill px-4 related-filter active" data-filter="new">Новые</button>
                <button class="btn btn-outline-light rounded-pill px-4 related-filter"
                        data-filter="popular">Популярные
                </button>
                <button class="btn btn-outline-light rounded-pill px-4 related-filter"
                        data-filter="discussed">Обсуждаемые
                </button>
            </div>
            <div class="d-flex flex-wrap gap-2 mb-4 d-none animate-fade-in-up" id="popular-sub-filters">
                <button class="btn btn-primary rounded-pill px-3 btn-sm popular-period-filter active"
                        data-period="week">За
                    неделю
                </button>
                <button class="btn btn-outline-light rounded-pill px-3 btn-sm popular-period-filter"
                        data-period="month">За
                    месяц
                </button>
                <button class="btn btn-outline-light rounded-pill px-3 btn-sm popular-period-filter"
                        data-period="half_year">За полгода
                </button>
                <button class="btn btn-outline-light rounded-pill px-3 btn-sm popular-period-filter" data-period="year">
                    За
                    год
                </button>
                <button class="btn btn-outline-light rounded-pill px-3 btn-sm popular-period-filter" data-period="all">
                    За
                    все время
                </button>
            </div>
            <div class="position-relative">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-6 g-3" id="related-books-grid">
                </div>
                <div class="text-center py-5 d-none" id="related-loading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Загрузка...</span>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4 d-none" id="related-load-more-container">
                <button class="btn btn-outline-primary rounded-pill px-5 py-2 fw-bold" id="related-load-more">
                    Загрузить еще
                </button>
            </div>
        </div>
    </div>
    <script>
        function setRating(rating) {
            document.getElementById('rating-input').value = rating;
            const stars = document.querySelectorAll('.rating-star');
            stars.forEach(star => {
                const sRating = parseInt(star.getAttribute('data-rating'));
                if (sRating <= rating) {
                    star.classList.replace('bi-star', 'bi-star-fill');
                } else {
                    star.classList.replace('bi-star-fill', 'bi-star');
                }
            });
        }
        function showReplyForm(reviewId) {
            const form = document.getElementById(`reply-form-${reviewId}`);
            form.classList.remove('d-none');
        }
        function hideReplyForm(reviewId) {
            const form = document.getElementById(`reply-form-${reviewId}`);
            form.classList.add('d-none');
        }
        function voteReview(reviewId, type, btn) {
            const url = `/reviews/${reviewId}/vote`;
            const container = btn.closest('.d-flex');
            const likeBtn = container.querySelectorAll('button')[0];
            const dislikeBtn = container.querySelectorAll('button')[1];
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    type: type
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        likeBtn.querySelector('.vote-count').textContent = data.likes;
                        dislikeBtn.querySelector('.vote-count').textContent = data.dislikes;
                        likeBtn.className = 'btn btn-sm btn-link text-decoration-none p-0 text-muted hover-text-success';
                        likeBtn.querySelector('i').className = 'bi bi-hand-thumbs-up';
                        dislikeBtn.className = 'btn btn-sm btn-link text-decoration-none p-0 text-muted hover-text-danger';
                        dislikeBtn.querySelector('i').className = 'bi bi-hand-thumbs-down';
                        if (data.action !== 'removed') {
                            if (type === 'like') {
                                likeBtn.className = 'btn btn-sm btn-link text-decoration-none p-0 text-success';
                                likeBtn.querySelector('i').className = 'bi bi-hand-thumbs-up-fill';
                            } else {
                                dislikeBtn.className = 'btn btn-sm btn-link text-decoration-none p-0 text-danger';
                                dislikeBtn.querySelector('i').className = 'bi bi-hand-thumbs-down-fill';
                            }
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
        }
        document.addEventListener('DOMContentLoaded', function () {
            let currentRelatedPage = 1;
            let currentRelatedFilter = 'new';
            let currentRelatedPeriod = 'week';
            const bookId = {{ $book->id }};
            const grid = document.getElementById('related-books-grid');
            const loadMoreBtn = document.getElementById('related-load-more');
            const loadMoreContainer = document.getElementById('related-load-more-container');
            const loadingSpinner = document.getElementById('related-loading');
            const filters = document.querySelectorAll('.related-filter');
            const subFiltersContainer = document.getElementById('popular-sub-filters');
            const subFilters = document.querySelectorAll('.popular-period-filter');
            function loadRelatedBooks(filter, period, page, append = false) {
                if (!append) {
                    grid.innerHTML = '';
                    loadingSpinner.classList.remove('d-none');
                    loadMoreContainer.classList.add('d-none');
                } else {
                    loadMoreBtn.disabled = true;
                    loadMoreBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Загрузка...';
                }
                let url = `/books/${bookId}/related?filter=${filter}&page=${page}`;
                if (filter === 'popular') {
                    url += `&period=${period}`;
                }
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (append) {
                            grid.insertAdjacentHTML('beforeend', data.html);
                            loadMoreBtn.disabled = false;
                            loadMoreBtn.innerHTML = 'Загрузить еще';
                        } else {
                            grid.innerHTML = data.html;
                            loadingSpinner.classList.add('d-none');
                        }
                        if (data.hasMore) {
                            loadMoreContainer.classList.remove('d-none');
                        } else {
                            loadMoreContainer.classList.add('d-none');
                        }
                    })
                    .catch(error => {
                        console.error('Error loading related books:', error);
                        loadingSpinner.classList.add('d-none');
                        if (append) {
                            loadMoreBtn.disabled = false;
                            loadMoreBtn.innerHTML = 'Загрузить еще';
                        }
                    });
            }
            loadRelatedBooks(currentRelatedFilter, currentRelatedPeriod, currentRelatedPage);
            filters.forEach(btn => {
                btn.addEventListener('click', function () {
                    if (this.classList.contains('active')) return;
                    filters.forEach(b => {
                        b.classList.remove('btn-primary', 'active');
                        b.classList.add('btn-outline-light');
                    });
                    this.classList.remove('btn-outline-light');
                    this.classList.add('btn-primary', 'active');
                    currentRelatedFilter = this.getAttribute('data-filter');
                    currentRelatedPage = 1;
                    if (currentRelatedFilter === 'popular') {
                        subFiltersContainer.classList.remove('d-none');
                        subFiltersContainer.classList.add('d-flex');
                    } else {
                        subFiltersContainer.classList.add('d-none');
                        subFiltersContainer.classList.remove('d-flex');
                    }
                    loadRelatedBooks(currentRelatedFilter, currentRelatedPeriod, currentRelatedPage);
                });
            });
            subFilters.forEach(btn => {
                btn.addEventListener('click', function () {
                    if (this.classList.contains('active')) return;
                    subFilters.forEach(b => {
                        b.classList.remove('btn-primary', 'active');
                        b.classList.add('btn-outline-light');
                    });
                    this.classList.remove('btn-outline-light');
                    this.classList.add('btn-primary', 'active');
                    currentRelatedPeriod = this.getAttribute('data-period');
                    currentRelatedPage = 1;
                    loadRelatedBooks('popular', currentRelatedPeriod, currentRelatedPage);
                });
            });
            loadMoreBtn.addEventListener('click', function () {
                currentRelatedPage++;
                loadRelatedBooks(currentRelatedFilter, currentRelatedPeriod, currentRelatedPage, true);
            });
        });
    </script>
    <style>
        /* Filter buttons styling */
        .btn-outline-light {
            border: 1px solid var(--bs-primary) !important;
            color: var(--bs-primary) !important;
        }
        .btn-primary,
        .btn-danger {
            color: white !important;
        }
        /* Heart icon should always be white */
        .bi-heart-fill {
            color: white !important;
        }
        /* Firework Effect */
        .firework-particle {
            position: fixed;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            pointer-events: none;
            z-index: 9999;
            animation: firework-pop 0.8s ease-out forwards;
        }
        @keyframes firework-pop {
            0% {
                transform: translate(0, 0) scale(1);
                opacity: 1;
            }
            100% {
                transform: translate(var(--tx), var(--ty)) scale(0);
                opacity: 0;
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
        });
        function highlightStars(rating) {
            document.querySelectorAll('.user-rating-star').forEach(star => {
                const r = parseInt(star.dataset.rating);
                if (r <= rating) {
                    star.classList.remove('bi-star', 'text-white-50');
                    star.classList.add('bi-star-fill', 'text-warning');
                } else {
                    star.classList.remove('bi-star-fill', 'text-warning');
                    star.classList.add('bi-star', 'text-white-50');
                }
            });
        }
        function resetStars() {
            const container = document.getElementById('user-rating-stars');
            const currentRating = parseInt(container.dataset.currentRating);
            highlightStars(currentRating);
        }
        function updateAverageRatingDisplay(newAvg) {
            const formattedAvg = parseFloat(newAvg).toFixed(1);
            const avgEl = document.getElementById('average-rating-display');
            if (avgEl) avgEl.textContent = formattedAvg;
            const coverEl = document.getElementById('cover-rating-display');
            if (coverEl) coverEl.innerHTML = `<i class="bi bi-star-fill text-warning me-1"></i> ${formattedAvg}`;
        }
        function createFirework(x, y) {
            const colors = ['#ffc107', '#ff5722', '#4caf50', '#2196f3', '#ffffff'];
            const particleCount = 30;
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.classList.add('firework-particle');
                particle.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                particle.style.left = x + 'px';
                particle.style.top = y + 'px';
                const angle = Math.random() * Math.PI * 2;
                const velocity = 50 + Math.random() * 100;
                const tx = Math.cos(angle) * velocity;
                const ty = Math.sin(angle) * velocity;
                particle.style.setProperty('--tx', `${tx}px`);
                particle.style.setProperty('--ty', `${ty}px`);
                document.body.appendChild(particle);
                particle.addEventListener('animationend', () => {
                    particle.remove();
                });
            }
        }
        function rateBook(bookId, rating) {
            const container = document.getElementById('user-rating-stars');
            const previousRating = parseInt(container.dataset.currentRating);
            container.dataset.currentRating = rating;
            highlightStars(rating);
            const star = document.querySelector(`.user-rating-star[data-rating="${rating}"]`);
            if (star) {
                const rect = star.getBoundingClientRect();
                createFirework(rect.left + rect.width / 2, rect.top + rect.height / 2);
            }
            fetch(`/books/${bookId}/rate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({rating: rating})
            }).then(response => response.json()).then(data => {
                if (!data.success) {
                    container.dataset.currentRating = previousRating;
                    highlightStars(previousRating);
                    console.error('Rating failed:', data);
                } else {
                    if (data.rating !== undefined) {
                        updateAverageRatingDisplay(data.rating);
                    }
                }
            }).catch(error => {
                console.error('Error:', error);
                container.dataset.currentRating = previousRating;
                highlightStars(previousRating);
            });
        }
    </script>
@endsection
