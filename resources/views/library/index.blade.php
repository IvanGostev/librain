@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="d-flex align-items-center justify-content-between mb-5 animate-fade-in-up">
            <h1 class="display-5 fw-bold text-white mb-0">Моя библиотека</h1>
            <a href="{{ route('genres.index') }}" class="btn btn-outline-primary rounded-pill btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Добавить книгу
            </a>
        </div>

        <!-- Tabs and Sorting Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 animate-fade-in-up delay-100 position-relative"
            style="z-index: 10;">
            <!-- Status Tabs -->
            <ul class="nav nav-pills" id="library-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'reading' ? 'active' : '' }} rounded-pill px-4"
                        id="reading-tab" data-bs-toggle="pill" data-bs-target="#reading" type="button" role="tab"
                        aria-controls="reading" aria-selected="{{ $activeTab === 'reading' ? 'true' : 'false' }}">
                        Читаю
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'want' ? 'active' : '' }} rounded-pill px-4" id="want-tab"
                        data-bs-toggle="pill" data-bs-target="#want" type="button" role="tab" aria-controls="want"
                        aria-selected="{{ $activeTab === 'want' ? 'true' : 'false' }}">
                        Хочу прочитать
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'completed' ? 'active' : '' }} rounded-pill px-4"
                        id="completed-tab" data-bs-toggle="pill" data-bs-target="#completed" type="button" role="tab"
                        aria-controls="completed" aria-selected="{{ $activeTab === 'completed' ? 'true' : 'false' }}">
                        Прочитано
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'writing' ? 'active' : '' }} rounded-pill px-4"
                        id="writing-tab" data-bs-toggle="pill" data-bs-target="#writing" type="button" role="tab"
                        aria-controls="writing" aria-selected="{{ $activeTab === 'writing' ? 'true' : 'false' }}">
                        Недописано
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'hidden' ? 'active' : '' }} rounded-pill px-4" id="hidden-tab"
                        data-bs-toggle="pill" data-bs-target="#hidden" type="button" role="tab" aria-controls="hidden"
                        aria-selected="{{ $activeTab === 'hidden' ? 'true' : 'false' }}">
                        Чёрный список
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'favorites' ? 'active' : '' }} rounded-pill px-4"
                        id="favorites-tab" data-bs-toggle="pill" data-bs-target="#favorites" type="button" role="tab"
                        aria-controls="favorites" aria-selected="{{ $activeTab === 'favorites' ? 'true' : 'false' }}">
                        <i class="bi bi-heart-fill me-1 text-danger"></i> Избранное
                    </button>
                </li>
            </ul>

            <!-- Sorting Dropdown -->
            <div class="dropdown">
                <button class="btn btn-dark-glass btn-sm rounded-pill px-3 border-white-10 dropdown-toggle" type="button"
                    data-bs-toggle="dropdown">
                    <i class="bi bi-sort-down me-1"></i>
                    @if($sort === 'title') По названию
                    @elseif($sort === 'author') По автору
                    @else По дате
                    @endif
                </button>
                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end bg-dark-card border-white-10 shadow-lg">
                    <li><a class="dropdown-item sort-link {{ $sort === 'latest' ? 'active' : '' }}"
                            href="{{ request()->fullUrlWithQuery(['sort' => 'latest', 'tab' => $activeTab]) }}">Сначала
                            новые</a></li>
                    <li><a class="dropdown-item sort-link {{ $sort === 'title' ? 'active' : '' }}"
                            href="{{ request()->fullUrlWithQuery(['sort' => 'title', 'tab' => $activeTab]) }}">По
                            названию</a></li>
                    <li><a class="dropdown-item sort-link {{ $sort === 'author' ? 'active' : '' }}"
                            href="{{ request()->fullUrlWithQuery(['sort' => 'author', 'tab' => $activeTab]) }}">По
                            автору</a></li>
                </ul>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="tab-content animate-fade-in-up delay-200" id="library-content">
            <!-- Reading -->
            <div class="tab-pane fade {{ $activeTab === 'reading' ? 'show active' : '' }}" id="reading" role="tabpanel"
                aria-labelledby="reading-tab">
                @if(isset($reading) && $reading->count() > 0)
                    <div class="row g-4">
                        @include('library.partials.book-card', ['entries' => $reading])
                    </div>
                @else
                    <div class="text-center py-5 text-muted bg-dark-card rounded-3 border border-dashed border-white-10">
                        <i class="bi bi-book fs-1 mb-3 d-block opacity-20"></i>
                        <p class="mb-0">Вы сейчас ничего не читаете.</p>
                    </div>
                @endif
            </div>

            <!-- Want to Read -->
            <div class="tab-pane fade {{ $activeTab === 'want' ? 'show active' : '' }}" id="want" role="tabpanel"
                aria-labelledby="want-tab">
                @if(isset($wantToRead) && $wantToRead->count() > 0)
                    <div class="row g-4">
                        @include('library.partials.book-card', ['entries' => $wantToRead])
                    </div>
                @else
                    <div class="text-center py-5 text-muted bg-dark-card rounded-3 border border-dashed border-white-10">
                        <i class="bi bi-calendar-event fs-1 mb-3 d-block opacity-20"></i>
                        <p class="mb-0">Список пожеланий пуст.</p>
                    </div>
                @endif
            </div>

            <!-- Completed -->
            <div class="tab-pane fade {{ $activeTab === 'completed' ? 'show active' : '' }}" id="completed" role="tabpanel"
                aria-labelledby="completed-tab">
                @if(isset($completed) && $completed->count() > 0)
                    <div class="row g-4">
                        @include('library.partials.book-card', ['entries' => $completed])
                    </div>
                @else
                    <div class="text-center py-5 text-muted bg-dark-card rounded-3 border border-dashed border-white-10">
                        <i class="bi bi-check2-circle fs-1 mb-3 d-block opacity-20"></i>
                        <p class="mb-0">Вы еще не прочитали ни одной книги полностью.</p>
                    </div>
                @endif
            </div>

            <!-- Favorites -->
            <div class="tab-pane fade {{ $activeTab === 'favorites' ? 'show active' : '' }}" id="favorites" role="tabpanel"
                aria-labelledby="favorites-tab">
                @if(isset($favorites) && $favorites->count() > 0)
                    <div class="row g-4">
                        @include('library.partials.book-card', ['entries' => $favorites])
                    </div>
                @else
                    <div class="text-center py-5 text-muted bg-dark-card rounded-3 border border-dashed border-white-10">
                        <i class="bi bi-heart fs-1 mb-3 d-block opacity-20"></i>
                        <p class="mb-0">У вас пока нет избранных книг.</p>
                    </div>
                @endif
            </div>

            <!-- In Progress (Writing) -->
            <div class="tab-pane fade {{ $activeTab === 'writing' ? 'show active' : '' }}" id="writing" role="tabpanel"
                aria-labelledby="writing-tab">
                @if(isset($writing) && $writing->count() > 0)
                    <div class="row g-4">
                        @include('library.partials.book-card', ['entries' => $writing])
                    </div>
                @else
                    <div class="text-center py-5 text-muted bg-dark-card rounded-3 border border-dashed border-white-10">
                        <i class="bi bi-pencil-square fs-1 mb-3 d-block opacity-20"></i>
                        <p class="mb-0">В этом разделе пока нет незаконченных книг.</p>
                    </div>
                @endif
            </div>

            <!-- Blacklist (Hidden) -->
            <div class="tab-pane fade {{ $activeTab === 'hidden' ? 'show active' : '' }}" id="hidden" role="tabpanel"
                aria-labelledby="hidden-tab">
                @if(isset($hidden) && $hidden->count() > 0)
                    <div class="row g-4">
                        @include('library.partials.book-card', ['entries' => $hidden])
                    </div>
                @else
                    <div class="text-center py-5 text-muted bg-dark-card rounded-3 border border-dashed border-white-10">
                        <i class="bi bi-eye-slash fs-1 mb-3 d-block opacity-20"></i>
                        <p class="mb-0">В черном списке пока пусто.</p>
                    </div>
                @endif
            </div>


        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabs = document.querySelectorAll('button[data-bs-toggle="pill"]');
            const sortLinks = document.querySelectorAll('.sort-link');

            tabs.forEach(tab => {
                tab.addEventListener('shown.bs.tab', function (event) {
                    const tabId = event.target.id.replace('-tab', '');

                    sortLinks.forEach(link => {
                        const url = new URL(link.href);
                        url.searchParams.set('tab', tabId);
                        link.href = url.toString();
                    });


                    const newUrl = new URL(window.location.href);
                    newUrl.searchParams.set('tab', tabId);
                    window.history.pushState({}, '', newUrl);
                });
            });
        });
    </script>
@endsection