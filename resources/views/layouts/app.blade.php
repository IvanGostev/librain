<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>

        (function () {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', isset($title) ? $title : config('app.name', 'Librain'))</title>
    <meta name="description"
        content="@yield('description', isset($description) ? $description : 'Librain - ваша цифровая библиотека. Читайте книги онлайн, следите за авторами и создавайте свою коллекцию.')">
    <meta name="keywords"
        content="@yield('keywords', isset($keywords) ? $keywords : 'книги, читать онлайн, библиотека, авторы, жанры')">

    <!-- Canonical Tag -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Noindex for specific pages -->
    <!-- Noindex for specific pages -->
    @hasSection('robots')
        <meta name="robots" content="@yield('robots')">
    @else
        @if(
                request()->has('page') ||
                request()->has('sort') ||
                request()->has('q') ||
                request()->is('search*') ||
                request()->routeIs('books.read') ||
                request()->is('profile*') ||
                request()->is('library*')
            )
            <meta name="robots" content="noindex, follow">
        @endif
    @endif

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', config('app.name', 'Librain'))">
    <meta property="og:description"
        content="@yield('description', 'Librain - ваша цифровая библиотека. Читайте книги онлайн, следите за авторами и создавайте свою коллекцию.')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-image.jpg'))">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@yield('title', config('app.name', 'Librain'))">
    <meta property="twitter:description"
        content="@yield('description', 'Librain - ваша цифровая библиотека. Читайте книги онлайн, следите за авторами и создавайте свою коллекцию.')">
    <meta property="twitter:image" content="@yield('og_image', asset('images/og-image.jpg'))">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @yield('schema')

</head>

<body class="bg-body-tertiary">
    <div id="app" class="d-flex flex-column min-vh-100">
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-dark-card border-bottom border-white-10 py-3"
            style="backdrop-filter: blur(20px);">
            <div class="container-fluid px-4 px-xl-5">
                <!-- Logo -->
                <a class="navbar-brand fw-bolder text-uppercase tracking-wider fs-4 me-5" href="{{ url('/') }}">
                    <span class="text-primary">Lib</span>rain
                </a>

                <!-- Mobile Toggle -->
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navbar Content -->
                <div class="collapse navbar-collapse" id="navbarContent">
                    <!-- Left: Navigation Links -->
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-lg-3 fw-medium">

                        <li class="nav-item">
                            @if(request()->is('genres'))
                                <span class="nav-link active text-primary cursor-default">Жанры</span>
                            @else
                                <a class="nav-link {{ request()->is('genres*') ? 'active text-primary' : '' }}"
                                    href="{{ url('/genres') }}">Жанры</a>
                            @endif
                        </li>
                        <li class="nav-item">
                            @if(request()->is('series'))
                                <span class="nav-link active text-primary cursor-default">Серии</span>
                            @else
                                <a class="nav-link {{ request()->is('series*') ? 'active text-primary' : '' }}"
                                    href="{{ url('/series') }}">Серии</a>
                            @endif
                        </li>
                        <li class="nav-item">
                            @if(request()->is('authors'))
                                <span class="nav-link active text-primary cursor-default">Авторы</span>
                            @else
                                <a class="nav-link {{ request()->is('authors*') ? 'active text-primary' : '' }}"
                                    href="{{ url('/authors') }}">Авторы</a>
                            @endif
                        </li>
                        <li class="nav-item">
                            @if(request()->routeIs('top100'))
                                <span class="nav-link active text-primary cursor-default">Топ 100</span>
                            @else
                                <a class="nav-link {{ request()->routeIs('top100') ? 'active text-primary' : '' }}"
                                    href="{{ route('top100') }}">Топ 100</a>
                            @endif
                        </li>
                    </ul>

                    <!-- Center: Search Bar (Pill) -->
                    <form class="d-flex mx-lg-4 flex-grow-1" role="search" action="{{ url('/search') }}" method="GET"
                        style="max-width: 500px;">
                        <div class="input-group">
                            <span
                                class="input-group-text bg-white bg-opacity-10 border-0 rounded-start-pill ps-3 text-secondary">
                                <i class="bi bi-search"></i>
                            </span>
                            <input
                                class="form-control bg-white bg-opacity-10 border-0 py-2 text-white placeholder-muted focus-ring-primary"
                                style="border-top-right-radius: 0; border-bottom-right-radius: 0;" type="search"
                                name="q" placeholder="Название, автор, жанр или текст..." aria-label="Search"
                                value="{{ request('q') }}">
                            <button type="submit" class="btn btn-primary d-lg-none rounded-end-pill px-4">
                                Найти
                            </button>
                        </div>
                    </form>

                    <!-- Right: User Actions -->
                    <div class="d-flex align-items-center gap-2 ms-lg-3 mt-3 mt-lg-0">
                        <button class="btn btn-icon btn-ghost p-2 text-white-50 hover-text-white me-2" id="theme-toggle"
                            title="Переключить тему">
                            <i class="bi bi-sun-fill"></i>
                        </button>
                        @guest
                            @if (Route::has('login'))
                                <a class="btn btn-outline-light rounded-pill px-4 me-2"
                                    href="{{ route('login') }}">{{ __('Войти') }}</a>
                            @endif
                            @if (Route::has('register'))
                                <a class="btn btn-primary rounded-pill px-4"
                                    href="{{ route('register') }}">{{ __('Регистрация') }}</a>
                            @endif
                        @else
                            <!-- Library Link -->
                            <a href="{{ route('library.index') }}"
                                class="btn btn-icon btn-ghost p-2 position-relative text-white-50 hover-text-white me-2"
                                title="Моя библиотека">
                                <i class="bi bi-bookmark-heart fs-5"></i>
                            </a>

                            <!-- User Dropdown -->
                            <div class="dropdown">
                                <a id="navbarDropdown"
                                    class="nav-link dropdown-toggle d-flex align-items-center gap-2 text-white ps-0"
                                    href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false" v-pre>
                                    @if(Auth::user()->avatar)
                                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}"
                                            class="rounded-circle border border-white-10" width="32" height="32"
                                            style="object-fit: cover;"
                                            onerror="this.classList.add('d-none'); this.nextElementSibling.classList.remove('d-none');">
                                        <div class="avatar bg-primary text-white rounded-circle d-none align-items-center justify-content-center fw-bold shadow-sm"
                                            style="width: 32px; height: 32px; font-size: 14px;">
                                            {{ substr(Auth::user()->name, 0, 1) }}
                                        </div>
                                    @else
                                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm"
                                            style="width: 32px; height: 32px; font-size: 14px;">
                                            {{ substr(Auth::user()->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <span class="d-none d-xl-inline fw-medium ms-1">{{ Auth::user()->name }}</span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-end bg-dark-card border-white-10 shadow-lg mt-2"
                                    aria-labelledby="navbarDropdown">
                                    <div class="px-3 py-2 text-muted small text-uppercase fw-bold ls-wider">Аккаунт</div>
                                    <a class="dropdown-item text-white hover-bg-white-10"
                                        href="{{ route('profile.show') }}">
                                        <i class="bi bi-person me-2 text-primary"></i> Профиль
                                    </a>
                                    <a class="dropdown-item text-white hover-bg-white-10"
                                        href="{{ route('library.index') }}">
                                        <i class="bi bi-collection me-2 text-success"></i> Моя библиотека
                                    </a>
                                    @if(Auth::user()->isAdmin())
                                        <a class="dropdown-item text-white hover-bg-white-10" href="{{ url('/admin') }}">
                                            <i class="bi bi-shield-lock me-2 text-warning"></i> Админ-панель
                                        </a>
                                    @endif
                                    <div class="dropdown-divider bg-white-10"></div>
                                    <a class="dropdown-item text-danger hover-bg-white-10" href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right me-2"></i> {{ __('Выйти') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </div>
                        @endguest
                    </div>
                </div>
            </div>
        </nav>

        <main class="py-5 mt-5 flex-grow-1">
            <div class="container mt-3">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show bg-success bg-opacity-10 text-success border-success border-opacity-25 rounded-pill px-4 animate-fade-in-up"
                        role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show bg-danger bg-opacity-10 text-danger border-danger border-opacity-25 rounded-pill px-4 animate-fade-in-up"
                        role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show bg-danger bg-opacity-10 text-danger border-danger border-opacity-25 rounded-3 mb-3"
                        role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>
            @yield('content')
        </main>

        <footer class="footer mt-auto py-4 border-top border-white-10 bg-darker">
            <div
                class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 text-muted">
                <div class="d-flex flex-column flex-md-row align-items-center gap-3 text-center text-md-start">
                    <p class="mb-0">&copy; {{ date('Y') }} Librain. Все права защищены.</p>
                    <div class="d-flex gap-3">
                        <a href="{{ route('pages.show', 'copyright') }}"
                            class="text-decoration-none text-muted hover-text-white">Правообладателям</a>
                        <a href="{{ route('pages.show', 'privacy-policy') }}"
                            class="text-decoration-none text-muted hover-text-white">Политика конфиденциальности</a>
                    </div>
                </div>

                <div class="text-center text-md-end">
                    @php
                        $contactEmail = \App\Models\SiteSetting::where('key', 'contact_email')->value('value');
                    @endphp
                    @if($contactEmail)
                        <p class="mb-0 small">По всем вопросам: <a href="mailto:{{ $contactEmail }}"
                                class="text-white-50 hover-text-white">{{ $contactEmail }}</a></p>
                    @endif
                </div>
            </div>
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const themeToggle = document.getElementById('theme-toggle');
            const html = document.documentElement;
            const icon = themeToggle.querySelector('i');


            const updateIcon = (theme) => {
                if (theme === 'dark') {
                    icon.classList.remove('bi-moon-stars-fill');
                    icon.classList.add('bi-sun-fill');
                } else {
                    icon.classList.remove('bi-sun-fill');
                    icon.classList.add('bi-moon-stars-fill');
                }
            };


            updateIcon(html.getAttribute('data-bs-theme'));

            themeToggle.addEventListener('click', () => {
                const currentTheme = html.getAttribute('data-bs-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

                html.setAttribute('data-bs-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateIcon(newTheme);
            });
        });
    </script>

    <style>
        /* On desktop (lg and up), round the search input on the right side */
        @media (min-width: 992px) {
            .input-group input[type="search"] {
                border-top-right-radius: 50rem !important;
                border-bottom-right-radius: 50rem !important;
            }
        }
    </style>
</body>

</html>