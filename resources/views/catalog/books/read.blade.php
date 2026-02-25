<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Страница {{ $page }} - {{ $book->title }} - {{ config('app.name', 'Librain') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Merriweather:wght@300;400;700&family=Lora:wght@400;500;600&display=swap"
        rel="stylesheet">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            transition: background-color 0.3s, color 0.3s;
        }
        /* Reader Themes */
        body.theme-light {
            background-color: #f8fafc;
            color: #1e293b;
        }
        body.theme-dark {
            background-color: #12151c;
            color: #e2e8f0;
        }
        body.theme-sepia {
            background-color: #f5f0e1;
            color: #433422;
        }
        /* Chapter Title Colors */
        .chapter-title {
            color: #1e293b;
        }
        body.theme-dark .chapter-title {
            color: #fff;
        }
        body.theme-sepia .chapter-title {
            color: #433422;
        }
        /* Reader Fonts */
        .font-sans {
            font-family: 'Inter', sans-serif;
        }
        .font-serif {
            font-family: 'Merriweather', serif;
        }
        .font-lora {
            font-family: 'Lora', serif;
        }
        .reader-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem 1rem;
            min-height: 100vh;
        }
        .reader-content {
            font-size: 1.1rem;
            line-height: 1.8;
        }
        .reader-toolbar {
            position: relative;
            height: 60px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            z-index: 1000;
            transition: transform 0.3s ease;
            color: #1a202c;
        }
        /* Dark Toolbar (Override) */
        body.theme-dark .reader-toolbar {
            background: rgba(18, 21, 28, 0.95);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: #e2e8f0;
        }
        body.theme-dark .reader-toolbar .btn,
        body.theme-dark .reader-toolbar h6,
        body.theme-dark .reader-toolbar i {
            color: #e2e8f0 !important;
        }
        body.theme-dark .reader-toolbar small {
            color: #94a3b8 !important;
        }
        /* Light Toolbar Tweaks (Default) */
        .reader-toolbar .btn,
        .reader-toolbar h6,
        .reader-toolbar i {
            color: #1a202c;
        }
        .reader-toolbar small {
            color: #4a5568;
        }
        body.theme-sepia .reader-toolbar {
            background: rgba(245, 240, 225, 0.95);
            border-bottom: 1px solid rgba(67, 52, 34, 0.1);
        }
        body.theme-sepia h2 {
            color: #433422 !important;
        }
        .reader-settings-drawer {
            position: fixed;
            top: 60px;
            right: 0;
            width: 300px;
            background: #ffffff;
            border-left: 1px solid rgba(0, 0, 0, 0.05);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            z-index: 999;
            box-shadow: -5px 5px 15px rgba(0, 0, 0, 0.2);
            color: #1a202c;
        }
40
        body.theme-dark .reader-settings-drawer {
            background: #1e293b;
            border-left: 1px solid rgba(255, 255, 255, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: #e2e8f0;
        }
        .reader-settings-drawer.open {
            transform: translateX(0);
        }
        .pagination-circle {
            width: 38px;
            height: 38px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }
        @media (max-width: 576px) {
            .pagination-circle {
                width: 32px;
                height: 32px;
                font-size: 0.85rem;
                border-radius: 8px !important;
            }
            .pagination-container {
                gap: 0.35rem !important;
            }
            .reader-nav-btn {
                min-width: 89px !important;
                width: 89px !important;
                padding-left: 0.25rem !important;
                padding-right: 0.25rem !important;
                font-size: 0.85rem !important;
                height: 31px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 8px !important;
            }
            .reader-nav-btn i {
                font-size: 0.8rem;
            }
            .reader-nav-wrapper {
                margin-top: 1.5rem !important;
                padding-top: 1.5rem !important;
                gap: 0.5rem;
            }
        }
        /* Hide navbar on scroll down, show on scroll up logic can be added */
    </style>
</head>
<body>
    <div class="reader-toolbar d-flex align-items-center justify-content-between px-3 px-lg-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('books.show', ['genre' => $book->genre_slug, 'slug' => $book->slug]) }}"
                class="btn btn-icon btn-ghost me-3" title="Back to Book">
                <i class="bi bi-arrow-left fs-5"></i>
            </a>
            <div class="d-none d-md-block">
                <h6 class="mb-0 fw-bold" style="max-width: 300px;">{{ $book->title }}</h6>
                <small class="text-muted">Страница {{ $page }} из {{ $totalPages }}</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-icon btn-ghost" id="toggleSettings" title="Display Settings">
                <i class="bi bi-fonts fs-5"></i>
            </button>
            <div class="dropdown">
                <button class="btn btn-icon btn-ghost" data-bs-toggle="dropdown" title="Chapters">
                    <i class="bi bi-list-ul fs-5"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end bg-dark-card border-white-10 shadow-lg p-3"
                    style="min-width: 250px;">
                    <form action="{{ route('books.read', ['slug' => $book->slug]) }}" method="GET" class="d-flex align-items-center gap-2">
                        <input type="number" name="page" class="form-control form-control-sm bg-dark text-white border-white-10" min="1" max="{{ $totalPages }}" value="{{ $page }}" placeholder="Страница">
                        <button type="submit" class="btn btn-sm btn-primary">Перейти</button>
                    </form>
                    <div class="text-muted small mt-2 text-center">Всего страниц: {{ $totalPages }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="reader-settings-drawer" id="settingsDrawer">
        <h6 class="text-uppercase text-muted fs-7 fw-bold ls-1 mb-3">Настройки чтения</h6>
        <div class="mb-4">
            <label class="d-block mb-2 text-muted small">Тема</label>
            <div class="btn-group w-100" role="group">
                <button type="button" class="btn btn-outline-secondary w-100" onclick="setTheme('dark')">Темная</button>
                <button type="button" class="btn btn-outline-secondary w-100 active"
                    onclick="setTheme('light')">Светлая</button>
                <button type="button" class="btn btn-outline-secondary w-100" onclick="setTheme('sepia')">Сепия</button>
            </div>
        </div>
        <div class="mb-4">
            <label class="d-block mb-2 text-muted small">Размер шрифта</label>
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-outline-secondary" onclick="changeFontSize(-1)"><i
                        class="bi bi-dash"></i></button>
                <span id="fontSizeDisplay" class="text-muted">18px</span>
                <button class="btn btn-sm btn-outline-secondary" onclick="changeFontSize(1)"><i
                        class="bi bi-plus"></i></button>
            </div>
        </div>
        <div class="mb-4">
            <label class="d-block mb-2 text-muted small">Шрифт</label>
            <select class="form-select bg-dark text-white border-white-10" id="fontFamilySelect"
                onchange="setFontFamily(this.value)">
                <option value="font-sans">Inter (Sans)</option>
                <option value="font-serif">Merriweather (Serif)</option>
                <option value="font-lora">Lora (Serif)</option>
            </select>
        </div>
    </div>
    <div class="reader-container">
        <div class="text-center mb-5 pt-5">
            <h2 class="mb-3 fw-bold chapter-title">Страница {{ $page }}</h2>
        </div>
        <div class="reader-content" id="readerContent">
            {!! $pageContent !!}
        </div>
        <div class="d-flex justify-content-center align-items-center flex-wrap reader-nav-wrapper mt-5 py-5 border-top border-secondary border-opacity-10 gap-2">
            @if($page > 1)
                <a href="{{ route('books.read', ['slug' => $book->slug, 'page' => $page - 1]) }}"
                    class="btn btn-outline-secondary rounded-pill px-4 reader-nav-btn">
                    <i class="bi bi-chevron-double-left me-1 d-none d-sm-inline"></i> <span class="d-none d-sm-inline">назад</span><span class="d-inline d-sm-none">назад</span>
                </a>
            @endif
            <div class="d-flex align-items-center justify-content-center flex-wrap gap-2 px-1 pagination-container">
                @php
                    $current = $page;
                    $total = $totalPages;
                    $delta = 2;
                    $start = max(1, $current - $delta);
                    $end = min($total, $current + $delta);
                @endphp
                @if($start > 1)
                    <a href="{{ route('books.read', ['slug' => $book->slug, 'page' => 1]) }}" class="btn btn-sm btn-outline-secondary rounded-circle pagination-circle">1</a>
                    @if($start > 2)
                        <span class="text-muted px-1">...</span>
                    @endif
                @endif
                @for($i = $start; $i <= $end; $i++)
                    @if($i == $current)
                        <span class="btn btn-sm btn-primary rounded-circle pagination-circle fw-bold" style="pointer-events: none; opacity: 1; color: #ffffff !important;">{{ $i }}</span>
                    @else
                        <a href="{{ route('books.read', ['slug' => $book->slug, 'page' => $i]) }}" class="btn btn-sm btn-outline-secondary rounded-circle pagination-circle">{{ $i }}</a>
                    @endif
                @endfor
                @if($end < $total)
                    @if($end < $total - 1)
                        <span class="text-muted px-1">...</span>
                    @endif
                    <a href="{{ route('books.read', ['slug' => $book->slug, 'page' => $total]) }}" class="btn btn-sm btn-outline-secondary rounded-circle pagination-circle">{{ $total }}</a>
                @endif
            </div>
            @if($page < $totalPages)
                <a href="{{ route('books.read', ['slug' => $book->slug, 'page' => $page + 1]) }}"
                    class="btn btn-outline-secondary rounded-pill px-4 reader-nav-btn">
                    <span class="d-none d-sm-inline">вперед</span><span class="d-inline d-sm-none">вперед</span> <i class="bi bi-chevron-double-right ms-1 d-none d-sm-inline"></i>
                </a>
            @else
                <a href="{{ route('books.show', ['genre' => $book->genre_slug ?? 'all', 'slug' => $book->slug]) }}"
                    class="btn btn-success rounded-pill px-4 reader-nav-btn" style="color: #ffffff !important;">
                    <span class="d-none d-sm-inline">в конец</span><span class="d-inline d-sm-none">в конец</span> <i class="bi bi-check-lg ms-1 d-none d-sm-inline"></i>
                </a>
            @endif
        </div>
    </div>
    <script>
        let currentFontSize = 18;
        let currentTheme = 'light';
        let currentFont = 'font-sans';
        const settingsDrawer = document.getElementById('settingsDrawer');
        const readerContent = document.getElementById('readerContent');
        const body = document.body;
        const fontSizeDisplay = document.getElementById('fontSizeDisplay');
        document.getElementById('toggleSettings').addEventListener('click', () => {
            settingsDrawer.classList.toggle('open');
        });
        document.addEventListener('click', (e) => {
            if (!settingsDrawer.contains(e.target) && !e.target.closest('#toggleSettings')) {
                settingsDrawer.classList.remove('open');
            }
        });
        function setTheme(theme) {
            body.classList.remove('theme-dark', 'theme-light', 'theme-sepia');
            if (theme !== 'light') {
                body.classList.add(`theme-${theme}`);
            }
            currentTheme = theme;
            localStorage.setItem('reader_theme', theme);
            const buttons = document.querySelectorAll('.btn-group button');
            buttons.forEach(btn => {
                const btnText = btn.textContent.trim().toLowerCase();
                const themeName = theme === 'dark' ? 'темная' : (theme === 'light' ? 'светлая' : 'сепия');
                if (btnText === themeName) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        }
        function changeFontSize(delta) {
            currentFontSize = Math.max(14, Math.min(32, currentFontSize + delta));
            readerContent.style.fontSize = `${currentFontSize}px`;
            fontSizeDisplay.textContent = `${currentFontSize}px`;
            localStorage.setItem('reader_fontSize', currentFontSize);
        }
        function setFontFamily(font) {
            readerContent.classList.remove('font-sans', 'font-serif', 'font-lora');
            readerContent.classList.add(font);
            localStorage.setItem('reader_fontFamily', font);
        }
        function initSettings() {
            const savedTheme = localStorage.getItem('reader_theme') || 'light';
            const savedSize = parseInt(localStorage.getItem('reader_fontSize')) || 18;
            const savedFont = localStorage.getItem('reader_fontFamily') || 'font-sans';
            setTheme(savedTheme);
            currentFontSize = savedSize;
            readerContent.style.fontSize = `${currentFontSize}px`;
            fontSizeDisplay.textContent = `${currentFontSize}px`;
            setFontFamily(savedFont);
            const fontSelect = document.getElementById('fontFamilySelect');
            if (fontSelect) fontSelect.value = savedFont;
        }
        initSettings();
    </script>
</body>
</html>
