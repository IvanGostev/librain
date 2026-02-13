@extends('layouts.app')

@section('title', $book->title . ' - ' . ($book->author->name ?? 'Автор') . ' | ' . config('app.name'))
@section('description', Str::limit(strip_tags($book->description), 160))
@section('keywords', $book->title . ', ' . ($book->author->name ?? '') . ', ' . $book->genres->pluck('name')->implode(', '))
@section('og_image', $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/no-cover.svg'))

@section('schema')
    <script type="application/ld+json">
                                                                                                    {
                                                                                                      "@@context": "https://schema.org",
                                                                                                      "@@type": "Book",
                                                                                                      "name": "{{ $book->title }}",
                                                                                                      "author": {
                                                                                                        "@@type": "Person",
                                                                                                        "name": "{{ $book->author->name ?? 'Unknown' }}"
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
    <div class="container py-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4 animate-fade-in-up">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('catalog.index') }}"
                        class="text-decoration-none text-muted hover-text-primary">Каталог</a></li>
                @if($book->genres->isNotEmpty())
                    <li class="breadcrumb-item"><a href="{{ route('genres.show', $book->genres->first()->slug) }}"
                            class="text-decoration-none text-muted hover-text-primary">{{ $book->genres->first()->name }}</a>
                    </li>
                @endif
                <li class="breadcrumb-item active text-white" aria-current="page">{{ $book->title }}</li>
            </ol>
        </nav>

        <div class="row g-5 position-relative">
            <!-- ... (Sidebar omitted, changes are further down) ... -->
            @if(!$book->is_published)
                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center z-3"
                    style="background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(10px); border-radius: 1rem;">
                    <div class="text-center p-5">
                        <i class="bi bi-eye-slash-fill display-1 text-white-50 mb-4 animate-bounce"></i>
                        <h2 class="fw-bold text-white mb-3">Книга скрыта автором</h2>
                        <p class="text-muted lead mb-4">Эта книга временно недоступна для чтения. Автор (админ) решил скрыть ее
                            от публичного доступа.</p>
                        <a href="{{ route('catalog.index') }}" class="btn btn-primary rounded-pill px-5">Вернуться в каталог</a>
                    </div>
                </div>
            @endif

            <!-- Sidebar: Cover & Actions -->
            <div class="col-lg-4 col-md-5 animate-fade-in-up delay-100">
                <div class="position-sticky" style="top: 100px;">
                    <div class="card bg-transparent border-0 mb-4">


                        <div class="position-relative rounded-3 overflow-hidden shadow-lg card-cover">
                            <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/no-cover.svg') }}"
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
                            <form action="{{ route('books.planned', $book->id) }}" method="POST" class="flex-grow-1">
                                @csrf
                                <button type="submit"
                                    class="btn btn-{{ $isPlanned ? 'primary' : 'dark-glass border-white-10' }} w-100 rounded-pill py-2 fw-bold transition-all d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-calendar-plus{{ $isPlanned ? '-fill' : '' }}"></i>
                                    <span>{{ $isPlanned ? 'В планах' : 'Хочу прочитать' }}</span>
                                </button>
                            </form>

                            <form action="{{ route('books.favorite', $book->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="btn btn-{{ $isFavorite ? 'danger' : 'dark-glass border-white-10' }} rounded-pill py-2 fw-bold transition-all px-4"
                                    title="{{ $isFavorite ? 'Убрать из избранного' : 'В избранное' }}">
                                    <i class="bi bi-heart{{ $isFavorite ? '-fill' : '' }}"></i>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}"
                                class="btn btn-dark-glass border-white-10 flex-grow-1 rounded-pill py-2 fw-bold transition-all d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-calendar-plus"></i>
                                <span>Хочу прочитать</span>
                            </a>
                            <a href="{{ route('login') }}"
                                class="btn btn-dark-glass border-white-10 rounded-pill py-2 fw-bold transition-all px-4"
                                title="В избранное">
                                <i class="bi bi-heart"></i>
                            </a>
                        @endauth
                    </div>

                    <!-- Share Button -->
                    <div class="dropdown">
                        <button
                            class="btn btn-dark-glass btn-lg rounded-pill fw-semibold border-white-10 hover-elevate w-100"
                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-share-fill me-2"></i> Поделиться
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end bg-dark-card border-white-10 shadow-lg p-2 mt-2"
                            style="min-width: 200px;">
                            <li>
                                <a class="dropdown-item rounded-2 d-flex align-items-center gap-3 py-2 text-white-50 hover-text-white hover-bg-white-10 transition-colors"
                                    href="https://vk.com/share.php?url={{ urlencode(url()->current()) }}" target="_blank">
                                    <svg class="text-primary" width="20" height="20" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                                        <path
                                            d="M15.07294,2H8.9375C3.33331,2,2,3.33331,2,8.92706V15.0625C2,20.66663,3.32294,22,8.92706,22H15.0625C20.66669,22,22,20.67706,22,15.07288V8.9375C22,3.33331,20.67706,2,15.07294,2Zm3.07287,14.27081H16.6875c-.55206,0-.71875-.44793-1.70831-1.4375-.86463-.83331-1.22919-.9375-1.44794-.9375-.30206,0-.38544.08332-.38544.5v1.3125c0,.35419-.11456.5625-1.04162.5625a5.69214,5.69214,0,0,1-4.44794-2.66668A11.62611,11.62611,0,0,1,5.35419,8.77081c0-.21875.08331-.41668.5-.41668H7.3125c.375,0,.51044.16668.65625.55212.70831,2.08331,1.91669,3.89581,2.40625,3.89581.1875,0,.27081-.08331.27081-.55206V10.10413c-.0625-.97913-.58331-1.0625-.58331-1.41663a.36008.36008,0,0,1,.375-.33337h2.29169c.3125,0,.41662.15625.41662.53125v2.89587c0,.3125.13544.41663.22919.41663.1875,0,.33331-.10413.67706-.44788a11.99877,11.99877,0,0,0,1.79169-2.97919.62818.62818,0,0,1,.63544-.41668H17.9375c.4375,0,.53125.21875.4375.53125A18.20507,18.20507,0,0,1,16.41669,12.25c-.15625.23956-.21875.36456,0,.64581.14581.21875.65625.64582,1,1.05207a6.48553,6.48553,0,0,1,1.22912,1.70837C18.77081,16.0625,18.5625,16.27081,18.14581,16.27081Z" />
                                    </svg> ВКонтакте
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

            <!-- Main Content -->
            <div class="col-lg-8 col-md-7 animate-fade-in-up delay-200">
                <h1 class="display-5 fw-bold text-white mb-2">{{ $book->title }}</h1>

                <div class="d-flex align-items-center gap-3 mb-3 text-white">
                    <a href="{{ route('authors.show', $book->author->slug) }}"
                        class="text-decoration-none text-primary fw-semibold fs-5">
                        {{ $book->author->name }}
                    </a>
                    <span>•</span>
                    <span class="d-flex align-items-center">
                        <i class="bi bi-star-fill text-warning me-1"></i> <span class="text-white"
                            id="average-rating-display">{{ number_format($book->rating, 1) }}</span>
                    </span>
                    <span>•</span>
                    <span class="text-white">{{ $book->views }} просмотров</span>
                    <span>•</span>
                    <span>{{ $book->published_year }}</span>
                </div>

                <!-- New Info Line: Status, Age, Volume, Date -->
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
                        @auth
                            @for($i = 1; $i <= 10; $i++)
                                <i class="bi bi-star{{ $userRating && $i <= $userRating ? '-fill text-warning' : ' text-white-50' }} fs-5 cursor-pointer hover-text-warning transition-colors user-rating-star"
                                    data-rating="{{ $i }}" title="Оценка: {{ $i }}" onmouseenter="highlightStars({{ $i }})"
                                    onclick="rateBook({{ $book->id }}, {{ $i }})"></i>
                            @endfor
                        @else
                            <span class="text-white-50">
                                <a href="{{ route('login') }}" class="text-primary text-decoration-none">Войдите</a>, чтобы
                                оценить книгу.
                            </span>
                        @endauth
                    </div>
                </div>

                <div class="mb-5">
                    <h5 class="text-white text-uppercase tracking-wider fw-bold mb-3">Жанры</h5>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($book->genres as $genre)
                            <a href="{{ route('genres.show', $genre->slug) }}"
                                class="btn btn-sm btn-dark-glass rounded-pill border-white-10">
                                {{ $genre->name }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="d-inline-flex flex-column gap-2 mb-5">
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('books.read', $book->slug) }}"
                            class="btn btn-primary rounded-pill px-4 fw-bold shadow-glow hover-elevate">
                            <i class="bi bi-book-half me-2"></i> Читать онлайн
                        </a>

                        @if($book->file_txt || $book->file_fb2 || $book->file_epub)
                            <a href="#downloads" class="btn btn-primary rounded-pill px-4 fw-bold shadow-glow hover-elevate">
                                <i class="bi bi-download me-2"></i> Скачать книгу
                            </a>
                        @endif

                        <a href="#reviews-form" class="btn btn-primary rounded-pill px-4 fw-bold shadow-glow hover-elevate">
                            <i class="bi bi-chat-text me-2"></i> Оставить отзыв
                        </a>
                    </div>

                    <a href="https://t.me/+mcfxd7HG3aM1NDRi" target="_blank"
                        class="btn w-100 rounded-4 fw-bold shadow-glow hover-elevate d-flex align-items-center justify-content-center gap-2"
                        style="background-color: #229ED9; border-color: #229ED9; color: #ffffff !important;">
                        <i class="bi bi-telegram" style="color: #ffffff !important;"></i>
                        <span style="color: #ffffff !important;">Чтобы и дальше читать бесплатно подпишись на наш
                            Telegram</span>
                    </a>
                </div>

                <div class="mb-5">
                    <h5 class="text-white text-uppercase tracking-wider fw-bold mb-3">О книге</h5>
                    <div class="text-white lead" style="font-size: 1.1rem; line-height: 1.8;">
                        {!! nl2br(e($book->description)) !!}
                    </div>
                </div>



                <div class="mb-5 border-top border-white-10 pt-5">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h5 class="text-white text-uppercase tracking-wider fw-bold mb-0">Отзывы
                            @if($book->reviews->isNotEmpty()) <span
                            class="text-muted ms-2">{{ $book->reviews->count() }}</span> @endif
                        </h5>

                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-light dropdown-toggle rounded-pill" type="button"
                                data-bs-toggle="dropdown">
                                Сортировка:
                                {{ request('reviews_sort', 'newest') === 'best' ? 'По рейтингу' : 'Сначала новые' }}
                            </button>
                            <ul
                                class="dropdown-menu dropdown-menu-dark dropdown-menu-end bg-dark-card border-white-10 shadow-lg">
                                <li><a class="dropdown-item {{ request('reviews_sort', 'newest') !== 'best' ? 'active' : '' }}"
                                        href="{{ request()->fullUrlWithQuery(['reviews_sort' => 'newest']) }}">Сначала
                                        новые</a></li>
                                <li><a class="dropdown-item {{ request('reviews_sort') === 'best' ? 'active' : '' }}"
                                        href="{{ request()->fullUrlWithQuery(['reviews_sort' => 'best']) }}">По рейтингу</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="card bg-feature-card border-white-10 p-4 mb-5">
                        <h6 class="text-white fw-bold mb-3">Оставить отзыв</h6>
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
                                    отзыв</button>
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
                    <div class="mb-5" id="downloads">
                        <h5 class="text-white text-uppercase tracking-wider fw-bold mb-3">Скачать книгу</h5>
                        <div class="d-flex flex-wrap gap-3">
                            @if($book->file_txt)
                                <a class="btn btn-outline-primary rounded-pill download-link"
                                    href="{{ asset('storage/' . $book->file_txt) }}" data-auth="{{ auth()->check() ? '1' : '0' }}"
                                    download>
                                    <i class="bi bi-file-text fs-5 me-2"></i> Скачать TXT <span
                                        class="ms-1 opacity-75 small">{{ $formatSize($book->file_txt) }}</span>
                                </a>
                            @endif
                            @if($book->file_fb2)
                                <a class="btn btn-outline-info rounded-pill download-link"
                                    href="{{ asset('storage/' . $book->file_fb2) }}" data-auth="{{ auth()->check() ? '1' : '0' }}"
                                    download>
                                    <i class="bi bi-book fs-5 me-2"></i> Скачать FB2 <span
                                        class="ms-1 opacity-75 small">{{ $formatSize($book->file_fb2) }}</span>
                                </a>
                            @endif
                            @if($book->file_epub)
                                <a class="btn btn-outline-success rounded-pill download-link"
                                    href="{{ asset('storage/' . $book->file_epub) }}" data-auth="{{ auth()->check() ? '1' : '0' }}"
                                    download>
                                    <i class="bi bi-journal-richtext fs-5 me-2"></i> Скачать EPUB <span
                                        class="ms-1 opacity-75 small">{{ $formatSize($book->file_epub) }}</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                @if($book->series->isNotEmpty())
                    @foreach($book->series as $series)
                        <div class="mb-5">
                            <div class="d-flex align-items-center mb-3">
                                <h5 class="text-white text-uppercase tracking-wider fw-bold mb-0">
                                    Все книги из цикла «<a href="{{ route('series.show', $series->slug) }}"
                                        class="text-white text-decoration-none hover-text-primary">{{ $series->name }}</a>»
                                </h5>
                                <span class="text-muted fw-bold ms-3 fs-5">({{ $series->books->count() }})</span>
                            </div>

                            <div class="bg-dark-card rounded-3 overflow-hidden border border-white-10">
                                @foreach($series->books as $sBook)
                                    <a href="{{ route('books.show', ['genre' => $sBook->genre_slug, 'slug' => $sBook->slug]) }}"
                                        class="d-flex align-items-center py-3 px-4 text-decoration-none border-bottom border-white-10 hover-bg-white-5 transition-colors group {{ $sBook->id === $book->id ? 'bg-white-5' : '' }}">
                                        <i
                                            class="bi bi-journal-text fs-5 me-3 text-white-50 group-hover:text-primary transition-colors"></i>
                                        <div class="flex-grow-1">
                                            <span class="text-white fw-medium group-hover:text-primary transition-colors">
                                                {{ $loop->iteration }}. {{ $sBook->title }}
                                            </span>
                                        </div>
                                        @if($sBook->id === $book->id)
                                            <i class="bi bi-geo-alt-fill text-primary ms-3" title="Текущая книга"></i>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif
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

            const forms = document.querySelectorAll('[id^="reply-form-"]');
            forms.forEach(f => f.classList.add('d-none'));

            document.getElementById('reply-form-' + reviewId).classList.remove('d-none');
            document.getElementById('reply-form-' + reviewId).querySelector('textarea').focus();
        }

        function hideReplyForm(reviewId) {
            document.getElementById('reply-form-' + reviewId).classList.add('d-none');
        }
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

    <!-- Download Timer Overlay -->
    <div id="download-timer-overlay"
        class="d-none position-fixed top-0 start-0 w-100 h-100 bg-dark z-3 d-flex flex-column justify-content-center align-items-center"
        style="z-index: 9999 !important; background-color: #0f172a !important;">
        <h2 class="mb-4" style="color: #ffffff !important;">Подготовка ссылки...</h2>
        <div class="display-1 text-primary fw-bold mb-4" id="download-timer">15</div>
        <div class="spinner-border text-primary" role="status"></div>
        <p class="mt-4" style="color: rgba(255, 255, 255, 0.5) !important;">Пожалуйста, подождите</p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.download-link').forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const isAuth = this.dataset.auth === '1';

                    if (!isAuth) {
                        alert('Сначала авторизируйтесь для скачивания книг.');
                        window.location.href = "{{ route('login') }}";
                        return;
                    }

                    const url = this.href;
                    const overlay = document.getElementById('download-timer-overlay');
                    const timerEl = document.getElementById('download-timer');
                    let timeLeft = 15;

                    overlay.classList.remove('d-none');
                    timerEl.textContent = timeLeft;

                    const interval = setInterval(() => {
                        timeLeft--;
                        timerEl.textContent = timeLeft;
                        if (timeLeft <= 0) {
                            clearInterval(interval);
                            window.location.href = url;
                            setTimeout(() => {
                                overlay.classList.add('d-none');
                            }, 2000);
                        }
                    }, 1000);
                });
            });
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
                body: JSON.stringify({ rating: rating })
            })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {

                        container.dataset.currentRating = previousRating;
                        highlightStars(previousRating);
                        console.error('Rating failed:', data);
                    } else {

                        if (data.rating !== undefined) {
                            updateAverageRatingDisplay(data.rating);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);

                    container.dataset.currentRating = previousRating;
                    highlightStars(previousRating);
                });
        }
    </script>
@endsection