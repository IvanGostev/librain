<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $chapter->title }} - {{ $book->title }} - {{ config('app.name', 'Librain') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Merriweather:wght@300;400;700&family=Lora:wght@400;500;600&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
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

        body.theme-dark .reader-settings-drawer {
            background: #1e293b;
            border-left: 1px solid rgba(255, 255, 255, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: #e2e8f0;
        }

        .reader-settings-drawer.open {
            transform: translateX(0);
        }

        /* Hide navbar on scroll down, show on scroll up logic can be added */
    </style>
</head>

<body>
    <!-- Toolbar -->
    <div class="reader-toolbar d-flex align-items-center justify-content-between px-3 px-lg-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('books.show', ['genre' => $book->genre_slug, 'slug' => $book->slug]) }}"
                class="btn btn-icon btn-ghost me-3" title="Back to Book">
                <i class="bi bi-arrow-left fs-5"></i>
            </a>
            <div class="d-none d-md-block">
                <h6 class="mb-0 fw-bold text-truncate" style="max-width: 300px;">{{ $book->title }}</h6>
                <small class="text-muted">{{ $chapter->title }}</small>
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
                <div class="dropdown-menu dropdown-menu-end bg-dark-card border-white-10 shadow-lg"
                    style="max-height: 400px; overflow-y: auto;">
                    @foreach($book->chapters as $ch)
                        <a href="{{ route('books.read', ['slug' => $book->slug, 'chapterOrder' => $ch->order]) }}"
                            class="dropdown-item text-white-50 hover-text-white {{ $ch->id === $chapter->id ? 'active bg-primary text-white' : '' }}">
                            {{ $ch->title }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Drawer -->
    <div class="reader-settings-drawer" id="settingsDrawer">
        <h6 class="text-uppercase text-muted fs-7 fw-bold ls-1 mb-3">Настройки чтения</h6>

        <!-- Theme -->
        <div class="mb-4">
            <label class="d-block mb-2 text-muted small">Тема</label>
            <div class="btn-group w-100" role="group">
                <button type="button" class="btn btn-outline-secondary w-100" onclick="setTheme('dark')">Темная</button>
                <button type="button" class="btn btn-outline-secondary w-100 active"
                    onclick="setTheme('light')">Светлая</button>
                <button type="button" class="btn btn-outline-secondary w-100" onclick="setTheme('sepia')">Сепия</button>
            </div>
        </div>

        <!-- Font Size -->
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

        <!-- Font Family -->
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

    <!-- Content -->
    <div class="reader-container">
        <div class="text-center mb-5 pt-5">
            <h2 class="mb-3 fw-bold chapter-title">{{ $chapter->title }}</h2>
        </div>

        <div class="reader-content" id="readerContent">
            {!! nl2br(e($chapter->content)) !!}
        </div>

        <!-- Navigation -->
        <div
            class="d-flex justify-content-between align-items-center mt-5 py-5 border-top border-secondary border-opacity-10">
            @if($prevChapter)
                <a href="{{ route('books.read', ['slug' => $book->slug, 'chapterOrder' => $prevChapter->order]) }}"
                    class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-2"></i> Предыдущая
                </a>
            @else
                <div></div>
            @endif

            @if($nextChapter)
                <a href="{{ route('books.read', ['slug' => $book->slug, 'chapterOrder' => $nextChapter->order]) }}"
                    class="btn btn-primary rounded-pill px-4 hover-elevate">
                    Следующая <i class="bi bi-arrow-right ms-2"></i>
                </a>
            @else
                <a href="{{ route('books.show', ['genre' => $book->genre_slug, 'slug' => $book->slug]) }}"
                    class="btn btn-success rounded-pill px-4 hover-elevate">
                    Завершить <i class="bi bi-check-lg ms-2"></i>
                </a>
            @endif
        </div>
    </div>

    <script>
        // Init state
        let currentFontSize = 18;
        let currentTheme = 'light';
        let currentFont = 'font-sans';

        // DOM elements
        const settingsDrawer = document.getElementById('settingsDrawer');
        const readerContent = document.getElementById('readerContent');
        const body = document.body;
        const fontSizeDisplay = document.getElementById('fontSizeDisplay');

        // Toggle Settings
        document.getElementById('toggleSettings').addEventListener('click', () => {
            settingsDrawer.classList.toggle('open');
        });

        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if (!settingsDrawer.contains(e.target) && !e.target.closest('#toggleSettings')) {
                settingsDrawer.classList.remove('open');
            }
        });

        // Theme function
        function setTheme(theme) {
            body.classList.remove('theme-dark', 'theme-light', 'theme-sepia');
            if (theme !== 'light') {
                body.classList.add(`theme-${theme}`);
            }
            currentTheme = theme;
            localStorage.setItem('reader_theme', theme);

            // Update buttons state
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

        // Font Size
        function changeFontSize(delta) {
            currentFontSize = Math.max(14, Math.min(32, currentFontSize + delta));
            readerContent.style.fontSize = `${currentFontSize}px`;
            fontSizeDisplay.textContent = `${currentFontSize}px`;
            localStorage.setItem('reader_fontSize', currentFontSize);
        }

        // Font Family
        function setFontFamily(font) {
            readerContent.classList.remove('font-sans', 'font-serif', 'font-lora');
            readerContent.classList.add(font);
            localStorage.setItem('reader_fontFamily', font);
        }

        // Initialize from LocalStorage
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