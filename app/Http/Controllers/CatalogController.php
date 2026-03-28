<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Genre;
use App\Models\Author;
use App\Models\Series;
use App\Models\Book;
use App\Models\LibraryEntry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Review;
class CatalogController extends Controller
{
    public function index()
    {
        $sort = request('sort', 'new');
        $period = request('period');
        $books = null;
        $reviews = null;
        if ($sort === 'commented') {
            $reviews = Review::with(['user', 'book'])
                ->whereHas('book', function ($q) {
                    $q->where('is_published', true);
                })
                ->where('is_approved', true)
                ->latest()
                ->paginate(20);
            $reviews->appends(request()->query());
        } else {
            if ($sort === 'popular' && !$period) {
                $period = 'week';
            }
            $query = Book::where('is_published', true)->with('authors')->withCount('reviews');
            $date = null;
            if ($period) {
                $date = match ($period) {
                    'week' => now()->subWeek(),
                    'month' => now()->subMonth(),
                    'half_year' => now()->subMonths(6),
                    'year' => now()->subYear(),
                    default => null
                };
            }
            if ($sort === 'popular') {
                if ($date) {
                    $query->withSum([
                        'dailyViews' => function ($q) use ($date) {
                            $q->where('date', '>=', $date);
                        }
                    ], 'views')
                        ->orderByDesc('daily_views_sum_views');
                } else {
                    $query->orderByDesc('views');
                }
            } else {
                if ($date) {
                    $query->where('created_at', '>=', $date);
                }
                $query->orderByDesc('created_at');
            }
            $books = $query->paginate(24);
            $books->appends(request()->query());
        }
        $title = 'Каталог книг - Читать онлайн на Librain';
        $bottomTitle = \App\Models\SiteSetting::where('key', 'home_bottom_title')->value('value');
        $bottomText = \App\Models\SiteSetting::where('key', 'home_bottom_text')->value('value');
        return view('catalog.index', compact('books', 'reviews', 'title', 'sort', 'period', 'bottomTitle', 'bottomText'));
    }
    public function genres()
    {
        $genres = Genre::withCount('books')->orderBy('name')->get();
        $seoTitleTpl = \App\Models\SiteSetting::where('key', 'tpl_seo_title_genres_index')->value('value') ?: 'Все жанры книг - Librain';
        $settingsReplacements = \App\Models\SiteSetting::pluck('value', 'key')->mapWithKeys(fn($v, $k) => ["{setting:$k}" => $v])->toArray();
        $title = strtr($seoTitleTpl, $settingsReplacements);
        $bottomTitle = \App\Models\SiteSetting::where('key', 'genres_bottom_title')->value('value');
        $bottomText = \App\Models\SiteSetting::where('key', 'genres_bottom_text')->value('value');
        return view('catalog.genres.index', compact('genres', 'title', 'bottomTitle', 'bottomText'));
    }
    public function genre(Request $request, $slug)
    {
        $genre = Genre::where('slug', $slug)->firstOrFail();
        $sort = $request->get('sort', 'latest');
        $period = $request->get('period');
        if ($sort === 'popular' && !$period) {
            $period = 'week';
        }
        $query = $genre->books()->where('is_published', true);
        if ($period && $sort === 'popular') {
            $date = match ($period) {
                'week' => now()->subWeek(),
                'month' => now()->subMonth(),
                'half_year' => now()->subMonths(6),
                'year' => now()->subYear(),
                default => null
            };
            if ($date) {
                $query->where('created_at', '>=', $date);
            }
        }
        if ($sort === 'latest') {
            $query->latest();
        } elseif ($sort === 'commented') {
            $reviews = Review::with(['user', 'book'])
                ->whereHas('book', function ($q) use ($genre) {
                    $q->where('is_published', true)
                        ->whereHas('genres', function ($g) use ($genre) {
                            $g->where('genres.id', $genre->id);
                        });
                })
                ->where('is_approved', true)
                ->latest()
                ->paginate(20);
            $query->withCount('reviews')->orderByDesc('reviews_count');
        } else {
            $query->orderByDesc('views');
        }
        $books = $query->paginate(12)->withQueryString();
        $title = $genre->name;
        $seoTitleTpl = $genre->seo_title ?: \App\Models\SiteSetting::where('key', 'tpl_seo_title_genre')->value('value') ?? '{name} — Книги жанра онлайн';
        $seoDescTpl = $genre->seo_description ?: \App\Models\SiteSetting::where('key', 'tpl_seo_desc_genre')->value('value') ?? '{name} — большая библиотека произведений онлайн.';
        
        $settingsReplacements = \App\Models\SiteSetting::pluck('value', 'key')->mapWithKeys(fn($v, $k) => ["{setting:$k}" => $v])->toArray();
        $replacements = array_merge(['{name}' => $genre->name], $settingsReplacements);
        
        $seoTitle = strtr($seoTitleTpl, $replacements);
        $seoDescription = strtr($seoDescTpl, $replacements);
        
        $reviews = isset($reviews) ? $reviews : null;
        return view('catalog.genres.show', compact('genre', 'books', 'sort', 'period', 'title', 'seoTitle', 'seoDescription', 'reviews'));
    }
    public function authors()
    {
        $sort = request('sort');
        $currentLetter = request('letter');
        $query = Author::withCount('books')
            ->with([
                'books' => function ($q) {
                    $q->select('books.id', 'books.views')->withCount('reviews');
                }
            ]);
        if ($currentLetter) {
            $query->where('name', 'like', $currentLetter . '%');
        }
        switch ($sort) {
            case 'count_desc':
            case 'count':
                $query->orderByDesc('books_count');
                break;
            case 'count_asc':
                $query->orderBy('books_count');
                break;
            case 'views_desc':
            case 'views':
                $query->orderByDesc('views_count');
                break;
            case 'views_asc':
                $query->orderBy('views_count');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'alphabet_asc':
            case 'alphabet':
                $query->orderBy('name', 'asc');
                break;
            case 'alphabet_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'name':
                $query->orderBy('name');
                break;
            default:
                $query->orderBy('name');
        }
        $letters = collect();
        if (in_array($sort, ['alphabet', 'alphabet_asc', 'alphabet_desc']) || $currentLetter) {
            $letters = Author::query()
                ->selectRaw('SUBSTR(name, 1, 1) as l')
                ->distinct()
                ->pluck('l')
                ->map(fn($l) => mb_strtoupper($l))
                ->unique()
                ->sort()
                ->values();
        }
        $authors = $query->paginate(20);
        $authors->appends(request()->query());
        $seoTitleTpl = \App\Models\SiteSetting::where('key', 'tpl_seo_title_authors_index')->value('value') ?: 'Все авторы - Librain';
        $settingsReplacements = \App\Models\SiteSetting::pluck('value', 'key')->mapWithKeys(fn($v, $k) => ["{setting:$k}" => $v])->toArray();
        $title = strtr($seoTitleTpl, $settingsReplacements);
        $bottomTitle = \App\Models\SiteSetting::where('key', 'authors_bottom_title')->value('value');
        $bottomText = \App\Models\SiteSetting::where('key', 'authors_bottom_text')->value('value');
        return view('catalog.authors.index', compact('authors', 'title', 'sort', 'letters', 'currentLetter', 'bottomTitle', 'bottomText'));
    }
    public function author(Request $request, $slug)
    {
        $author = Author::where('slug', $slug)->firstOrFail();
        $booksCount = $author->books()->count();
        $viewsCount = $author->books()->sum('views');
        $author->books_count = $booksCount;
        $author->views_count = $viewsCount;
        $filter = $request->input('filter', 'new');
        $period = $request->input('period');
        if ($filter === 'popular' && !$period) {
            $period = 'week';
        }
        $query = $author->books()->where('is_published', true);
        if ($filter === 'popular') {
            $date = match ($period) {
                'week' => now()->subWeek(),
                'month' => now()->subMonth(),
                'half_year' => now()->subMonths(6),
                'year' => now()->subYear(),
                default => null
            };
            if ($date) {
                $query->withSum([
                    'dailyViews' => function ($q) use ($date) {
                        $q->where('date', '>=', $date);
                    }
                ], 'views')
                    ->orderByDesc('daily_views_sum_views');
            } else {
                $query->orderByDesc('views');
            }
        } elseif ($filter === 'discussed') {
            $query->withCount('reviews')->orderByDesc('reviews_count');
        } else {
            $query->latest();
        }
        $books = $query->paginate(12)->withQueryString();
        $title = $author->name;
        
        $seoTitleTpl = $author->seo_title ?: \App\Models\SiteSetting::where('key', 'tpl_seo_title_author')->value('value') ?? '{name} — Книги автора читать онлайн';
        $seoDescTpl = $author->seo_description ?: \App\Models\SiteSetting::where('key', 'tpl_seo_desc_author')->value('value') ?? '{name} — читать лучшие книги онлайн.';
        
        $settingsReplacements = \App\Models\SiteSetting::pluck('value', 'key')->mapWithKeys(fn($v, $k) => ["{setting:$k}" => $v])->toArray();
        $replacements = array_merge(['{name}' => $author->name], $settingsReplacements);
        
        $seoTitle = strtr($seoTitleTpl, $replacements);
        $seoDescription = strtr($seoDescTpl, $replacements);
        
        return view('catalog.authors.show', compact('author', 'books', 'title', 'seoTitle', 'seoDescription', 'filter', 'period'));
    }
    public function series()
    {
        $sort = request('sort');
        $currentLetter = request('letter');
        $query = Series::withCount('books')
            ->with([
                'books' => function ($q) {
                    $q->select('books.id', 'books.views')->withCount('reviews');
                }
            ]);
        if ($currentLetter) {
            $query->where('name', 'like', $currentLetter . '%');
        }
        switch ($sort) {
            case 'count_desc':
            case 'count':
                $query->orderByDesc('books_count');
                break;
            case 'count_asc':
                $query->orderBy('books_count');
                break;
            case 'views_desc':
            case 'views':
                $query->withSum('books', 'views');
                $query->orderByDesc('books_sum_views');
                break;
            case 'views_asc':
                $query->withSum('books', 'views');
                $query->orderBy('books_sum_views');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'alphabet_asc':
            case 'alphabet':
                $query->orderBy('name', 'asc');
                break;
            case 'alphabet_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'name':
                $query->orderBy('name');
                break;
            default:
                $query->orderBy('name');
        }
        $letters = collect();
        if (in_array($sort, ['alphabet', 'alphabet_asc', 'alphabet_desc']) || $currentLetter) {
            $letters = Series::query()
                ->selectRaw('SUBSTR(name, 1, 1) as l')
                ->distinct()
                ->pluck('l')
                ->map(fn($l) => mb_strtoupper($l))
                ->unique()
                ->sort()
                ->values();
        }
        $series = $query->paginate(20);
        $series->appends(request()->query());
        $seoTitleTpl = \App\Models\SiteSetting::where('key', 'tpl_seo_title_series_index')->value('value') ?: 'Все книжные серии - Librain';
        $settingsReplacements = \App\Models\SiteSetting::pluck('value', 'key')->mapWithKeys(fn($v, $k) => ["{setting:$k}" => $v])->toArray();
        $title = strtr($seoTitleTpl, $settingsReplacements);
        $bottomTitle = \App\Models\SiteSetting::where('key', 'series_bottom_title')->value('value');
        $bottomText = \App\Models\SiteSetting::where('key', 'series_bottom_text')->value('value');
        return view('catalog.series.index', compact('series', 'title', 'sort', 'letters', 'currentLetter', 'bottomTitle', 'bottomText'));
    }
    public function seriesShow(Request $request, $slug)
    {
        $series = Series::where('slug', $slug)->firstOrFail();
        $booksCount = $series->books()->count();
        $viewsCount = $series->books()->sum('views');
        $filter = $request->input('filter', 'order');
        $period = $request->input('period');
        if ($filter === 'popular' && !$period) {
            $period = 'week';
        }
        $query = $series->books()->where('is_published', true);
        if ($filter === 'order') {
            $booksList = (clone $query)->select('books.id', 'books.title')->get();
            $sortedIds = $booksList->sortBy('title', SORT_NATURAL | SORT_FLAG_CASE)->pluck('id')->toArray();
            if (!empty($sortedIds)) {
                $idsOrdered = implode(',', $sortedIds);
                $query->reorder()->orderByRaw("FIELD(books.id, $idsOrdered)");
            }
        } elseif ($filter === 'new') {
            $query->reorder()->latest();
        } elseif ($filter === 'popular') {
            $query->reorder();
            $date = match ($period) {
                'week' => now()->subWeek(),
                'month' => now()->subMonth(),
                'half_year' => now()->subMonths(6),
                'year' => now()->subYear(),
                default => null
            };
            if ($date) {
                $query->withSum([
                    'dailyViews' => function ($q) use ($date) {
                        $q->where('date', '>=', $date);
                    }
                ], 'views')
                    ->orderByDesc('daily_views_sum_views');
            } else {
                $query->orderByDesc('views');
            }
        } elseif ($filter === 'discussed') {
            $reviews = Review::with(['user', 'book'])
                ->whereHas('book', function ($q) use ($series) {
                    $q->where('is_published', true)
                        ->whereHas('series', function ($s) use ($series) {
                            $s->where('series.id', $series->id);
                        });
                })
                ->where('is_approved', true)
                ->latest()
                ->paginate(20);
            $query->reorder()->withCount('reviews')->orderByDesc('reviews_count');
        }
        $books = $query->paginate(12)->withQueryString();
        $reviews = isset($reviews) ? $reviews : null;
        $title = $series->name;
        
        $seoTitleTpl = $series->seo_title ?: \App\Models\SiteSetting::where('key', 'tpl_seo_title_series')->value('value') ?? '{name} — Книжная серия читать';
        $seoDescTpl = $series->seo_description ?: \App\Models\SiteSetting::where('key', 'tpl_seo_desc_series')->value('value') ?? '{name} — читать книги серии по порядку.';
        
        $settingsReplacements = \App\Models\SiteSetting::pluck('value', 'key')->mapWithKeys(fn($v, $k) => ["{setting:$k}" => $v])->toArray();
        $replacements = array_merge(['{name}' => $series->name], $settingsReplacements);
        
        $seoTitle = strtr($seoTitleTpl, $replacements);
        $seoDescription = strtr($seoDescTpl, $replacements);
        
        return view('catalog.series.show', compact('series', 'books', 'title', 'seoTitle', 'seoDescription', 'filter', 'period', 'reviews'));
    }
    public function bookLegacy(Request $request, $slug)
    {
        $book = Book::where('slug', $slug)->with('genres')->firstOrFail();
        $genreSlug = $book->genres->first()?->slug ?? 'general';
        return redirect()->route('books.show', ['genre' => $genreSlug, 'slug' => $slug], 301);
    }
    public function book(Request $request, $genre, $slug)
    {
        $reviewsSort = $request->input('reviews_sort', 'newest');
        $book = Book::where('slug', $slug)
            ->with([
                'authors',
                'genres',
                'series.books' => function ($q) {
                    $q->select('books.id', 'books.title', 'books.slug', 'books.cover_image', 'books.rating', 'books.created_at');
                }
            ])
            ->with([
                'chapters' => function ($q) {
                    $q->select('id', 'book_id', 'title', 'order', 'symbols_count')
                        ->orderBy('order');
                }
            ])
            ->with([
                'reviews' => function ($q) use ($reviewsSort) {
                    $q->whereNull('parent_id')
                        ->where('is_approved', true)
                        ->with([
                            'user',
                            'votes',
                            'children' => function ($q) {
                                $q->where('is_approved', true)->with([
                                    'user',
                                    'votes',
                                    'children' => function ($q) {
                                        $q->where('is_approved', true)->with(['user', 'votes']);
                                    }
                                ]);
                            }
                        ]);
                    if ($reviewsSort === 'best') {
                        $q->orderByDesc('rating')->orderByDesc('created_at');
                    } else {
                        $q->latest();
                    }
                }
            ])
            ->firstOrFail();
        $primaryGenre = $book->genres->first();
        $correctGenreSlug = $primaryGenre ? $primaryGenre->slug : 'general';
        if ($genre !== $correctGenreSlug) {
            return redirect()->route('books.show', ['genre' => $correctGenreSlug, 'slug' => $slug], 301);
        }
        $book->increment('views');
        \App\Models\BookDailyView::firstOrCreate(
            ['book_id' => $book->id, 'date' => now()->toDateString()]
        )->increment('views');
        $isFavorite = false;
        $isPlanned = false;
        $userRating = 0;
        $userStatus = null;
        if (Auth::check()) {
            $entry = Auth::user()->libraryEntries()
                ->where('book_id', $book->id)
                ->first();
            if ($entry) {
                $isFavorite = $entry->is_favorite;
                $isPlanned = $entry->status === 'planned';
                $userStatus = $entry->status;
            }
            $userRating = \App\Models\Rating::where('user_id', Auth::id())
                ->where('book_id', $book->id)
                ->value('rating') ?? 0;
        } else {
            $userRating = \App\Models\Rating::where('ip_address', request()->ip())
                ->where('book_id', $book->id)
                ->whereNull('user_id')
                ->value('rating') ?? 0;
        }
        $authorName = $book->authors->isNotEmpty() ? $book->authors->pluck('name')->join(', ') : 'Автор неизвестен';
        $title = $book->title;
        $description = Str::limit(strip_tags($book->description), 160);
        
        $seoTitleTpl = $book->seo_title ?: \App\Models\SiteSetting::where('key', 'tpl_seo_title_book')->value('value') ?? '{title} — Читать книгу онлайн';
        $seoDescTpl = $book->seo_description ?: \App\Models\SiteSetting::where('key', 'tpl_seo_desc_book')->value('value') ?? '{title} — читать онлайн или скачать в форматах fb2, epub, txt.';
        
        $settingsReplacements = \App\Models\SiteSetting::pluck('value', 'key')->mapWithKeys(fn($v, $k) => ["{setting:$k}" => $v])->toArray();
        $replacements = array_merge([
            '{title}' => $book->title,
            '{author}' => $authorName,
            '{genres}' => $book->genres->pluck('name')->join(', '),
            '{year}' => $book->published_year ?? '',
        ], $settingsReplacements);
        
        $seoTitle = strtr($seoTitleTpl, $replacements);
        $seoDescription = strtr($seoDescTpl, $replacements);
        $ratings = \App\Models\Rating::where('book_id', $book->id)
            ->select('rating', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();
        $ratingCounts = [];
        $totalRatings = array_sum($ratings);
        for ($i = 10; $i >= 1; $i--) {
            $count = $ratings[$i] ?? 0;
            $percent = $totalRatings > 0 ? round(($count / $totalRatings) * 100) : 0;
            $ratingCounts[$i] = [
                'count' => $count,
                'percent' => $percent
            ];
        }
        $statuses = \App\Models\LibraryEntry::where('book_id', $book->id)
            ->select('status', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        $favoritesCount = \App\Models\LibraryEntry::where('book_id', $book->id)
            ->where('is_favorite', true)
            ->count();
        $statusLabels = [
            'reading' => ['label' => 'Читаю', 'icon' => 'bi-book', 'color' => 'primary'],
            'planned' => ['label' => 'Хочу прочитать', 'icon' => 'bi-calendar-plus', 'color' => 'info'],
            'finished' => ['label' => 'Прочитано', 'icon' => 'bi-check-circle', 'color' => 'success'],
            'dropped' => ['label' => 'Брошено', 'icon' => 'bi-x-circle', 'color' => 'danger'],
            'favorite' => ['label' => 'В избранном', 'icon' => 'bi-heart', 'color' => 'danger'],
        ];
        $statusCounts = [];
        $totalStatuses = array_sum($statuses) + $favoritesCount; 
        foreach ($statusLabels as $key => $data) {
            $count = $key === 'favorite' ? $favoritesCount : ($statuses[$key] ?? 0);
            $percent = $totalStatuses > 0 ? round(($count / $totalStatuses) * 100) : 0;
            $statusCounts[$key] = [
                'label' => $data['label'],
                'icon' => $data['icon'],
                'color' => $data['color'],
                'count' => $count,
                'percent' => $percent
            ];
        }
        return view('catalog.books.show', compact(
            'book', 'title', 'description', 'seoTitle', 'seoDescription', 'userStatus', 'isFavorite', 'isPlanned', 'userRating',
            'ratingCounts', 'totalRatings', 'statusCounts', 'totalStatuses'
        ));
    }
    public function read($slug, $page = 1)
    {
        $book = Book::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();
        $pages = \Illuminate\Support\Facades\Cache::remember('book_pages_' . $book->id, 86400, function () use ($book) {
            $paragraphs = [];
            if (!empty($book->full_text)) {
                $text = preg_replace('/(<br\s*\/?>)/i', "$1\n", $book->full_text);
                $text = preg_replace('/(<\/(p|div|h[1-6]|figure|blockquote|ul|ol)>)/i', "$1\n", $text);
                $lines = explode("\n", $text);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line !== '') {
                        $paragraphs[] = $line;
                    }
                }
            } else {
                $allChapters = $book->chapters()->orderBy('order')->get();
                foreach ($allChapters as $chapter) {
                    if ($allChapters->count() > 1 && $chapter->title) {
                        $paragraphs[] = "<h3 class='mt-4 mb-3 fw-bold'>" . htmlspecialchars($chapter->title) . "</h3>"; 
                    }
                    $chapterParagraphs = explode("\n", $chapter->content);
                    foreach ($chapterParagraphs as $p) {
                        $p = trim($p);
                        if ($p !== '') {
                            $paragraphs[] = "<p>" . nl2br(htmlspecialchars($p)) . "</p>";
                        }
                    }
                }
            }
            $pagesArray = [];
            $currentPage = [];
            $currentCharCount = 0;
            foreach ($paragraphs as $p) {
                $cleanP = strip_tags($p);
                $charCount = mb_strlen(trim($cleanP));
                
                if ($charCount > 4000) {
                    $words = preg_split('/(\s+)/u', $p, -1, PREG_SPLIT_DELIM_CAPTURE);
                    $tempP = '';
                    $tempCount = 0;
                    foreach ($words as $word) {
                        $tempP .= $word;
                        $tempCount += mb_strlen(strip_tags($word));
                        if ($tempCount >= 2500) {
                            $currentPage[] = $tempP;
                            $pagesArray[] = implode("\n", $currentPage);
                            $currentPage = [];
                            $tempCount = 0;
                            $tempP = '';
                            $currentCharCount = 0;
                        }
                    }
                    if ($tempP !== '') {
                        $currentPage[] = $tempP;
                        $currentCharCount += mb_strlen(strip_tags($tempP));
                        if ($currentCharCount >= 2500) {
                            $pagesArray[] = implode("\n", $currentPage);
                            $currentPage = [];
                            $currentCharCount = 0;
                        }
                    }
                } else {
                    $currentPage[] = $p;
                    $currentCharCount += $charCount;
                    if ($currentCharCount >= 2500) {
                        $pagesArray[] = implode("\n", $currentPage);
                        $currentPage = [];
                        $currentCharCount = 0;
                    }
                }
            }
            if (!empty($currentPage)) {
                $pagesArray[] = implode("\n", $currentPage);
            }
            return $pagesArray;
        });
        if (empty($pages)) {
            return redirect()->route('books.show', $slug)->with('error', 'В этой книге нет текста для чтения.');
        }
        $totalPages = count($pages);
        $page = (int) request('page', $page);
        if ($page < 1) $page = 1;
        if ($page > $totalPages) $page = $totalPages;
        $pageContent = $pages[$page - 1];
        if (Auth::check()) {
            $progress = $totalPages > 0 ? round(($page / $totalPages) * 100) : 0;
            $progress = min(100, $progress);
            LibraryEntry::updateOrCreate(
                ['user_id' => Auth::id(), 'book_id' => $book->id],
                [
                    'status' => $progress >= 100 ? 'finished' : 'reading',
                    'progress_percent' => $progress
                ]
            );
        }
        $title = $book->title . ' - Страница ' . $page;
        return view('catalog.books.read', compact('book', 'pageContent', 'page', 'totalPages', 'title'));
    }
    public function top100()
    {
        $period = request('period');
        $query = Book::where('is_published', true);
        if ($period) {
            $date = match ($period) {
                'week' => now()->subWeek(),
                'month' => now()->subMonth(),
                'half_year' => now()->subMonths(6),
                'year' => now()->subYear(),
                default => null
            };
            if ($date) {
                $query->withSum([
                    'dailyViews as period_views' => function ($q) use ($date) {
                        $q->where('date', '>=', $date);
                    }
                ], 'views')
                    ->orderByDesc('period_views');
            } else {
                $query->orderByDesc('views');
            }
        } else {
            $query->orderByDesc('views');
        }
        $books = $query->take(100)->get();
        $seoTitleTpl = \App\Models\SiteSetting::where('key', 'tpl_seo_title_top100')->value('value') ?: 'Топ-100 популярных книг - Librain';
        $settingsReplacements = \App\Models\SiteSetting::pluck('value', 'key')->mapWithKeys(fn($v, $k) => ["{setting:$k}" => $v])->toArray();
        $title = strtr($seoTitleTpl, $settingsReplacements);
        $bottomTitle = \App\Models\SiteSetting::where('key', 'top100_bottom_title')->value('value');
        $bottomText = \App\Models\SiteSetting::where('key', 'top100_bottom_text')->value('value');
        return view('catalog.top100', compact('books', 'title', 'period', 'bottomTitle', 'bottomText'));
    }
    public function search(Request $request)
    {
        $query = $request->input('q');
        $books = collect();
        if ($query) {
            $searchTerm = "%{$query}%";
            $books = Book::where('is_published', true)
                ->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'like', $searchTerm)
                        ->orWhere('full_text', 'like', $searchTerm)
                        ->orWhereHas('authors', function ($q) use ($searchTerm) {
                            $q->where('name', 'like', $searchTerm);
                        })
                        ->orWhereHas('genres', function ($q) use ($searchTerm) {
                            $q->where('name', 'like', $searchTerm);
                        })
                        ->orWhereHas('chapters', function ($q) use ($searchTerm) {
                            $q->where('content', 'like', $searchTerm);
                        });
                })
                ->select('books.*')
                ->selectRaw('
                    (CASE WHEN title LIKE ? THEN 100 ELSE 0 END) +
                    (CASE WHEN EXISTS (SELECT 1 FROM author_book JOIN authors ON authors.id = author_book.author_id WHERE author_book.book_id = books.id AND authors.name LIKE ?) THEN 50 ELSE 0 END) +
                    (CASE WHEN EXISTS (SELECT 1 FROM book_genre JOIN genres ON genres.id = book_genre.genre_id WHERE book_genre.book_id = books.id AND genres.name LIKE ?) THEN 20 ELSE 0 END) +
                    (CASE WHEN full_text LIKE ? THEN 10 ELSE 0 END) +
                    (CASE WHEN EXISTS (SELECT 1 FROM chapters WHERE chapters.book_id = books.id AND chapters.content LIKE ?) THEN 10 ELSE 0 END)
                    as relevance
                ', [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm])
                ->orderByDesc('relevance')
                ->paginate(24);
        }
        $title = $query ? "Результаты поиска: «{$query}» - Librain" : 'Поиск книг - Librain';
        return view('search.index', compact('books', 'title'));
    }
    public function related(Request $request, $id)
    {
        $book = Book::findOrFail($id);
        $filter = $request->input('filter', 'new');
        $period = $request->input('period', 'all');
        $page = $request->input('page', 1);
        $query = Book::where('is_published', true)
            ->where('id', '!=', $book->id)
            ->with('authors');
        if ($book->genres->isNotEmpty()) {
            $query->whereHas('genres', function ($q) use ($book) {
                $q->whereIn('genres.id', $book->genres->pluck('id'));
            });
        } else {
            $query->doesntHave('genres');
        }
        if ($filter === 'popular') {
            $date = match ($period) {
                'week' => now()->subWeek(),
                'month' => now()->subMonth(),
                'half_year' => now()->subMonths(6),
                'year' => now()->subYear(),
                default => null
            };
            if ($date) {
                $query->withSum([
                    'dailyViews' => function ($q) use ($date) {
                        $q->where('date', '>=', $date);
                    }
                ], 'views')
                    ->orderByDesc('daily_views_sum_views');
            } else {
                $query->orderByDesc('views');
            }
        } elseif ($filter === 'discussed') {
            $query->withCount('reviews')->orderByDesc('reviews_count');
        } else {
            $query->latest();
        }
        $relatedBooks = $query->paginate(12, ['*'], 'page', $page);
        if ($request->ajax()) {
            $html = '';
            foreach ($relatedBooks as $relatedBook) {
                $html .= '<div class="col animate-fade-in-up">' . view('components.book-card-vertical', ['book' => $relatedBook])->render() . '</div>';
            }
            return response()->json([
                'html' => $html,
                'hasMore' => $relatedBooks->hasMorePages()
            ]);
        }
        return abort(404);
    }
    public function downloadPage(Book $book, $format)
    {
        if (!in_array($format, ['txt', 'fb2', 'epub'])) {
            abort(404);
        }
        $field = 'file_' . $format;
        $filePath = $book->$field;
        if (!$filePath || !\Illuminate\Support\Facades\Storage::disk('public')->exists($filePath)) {
            $chapters = $book->chapters()->orderBy('order')->get();
            if ($chapters->isEmpty() && empty($book->full_text)) {
                abort(404, 'Нет текста для скачивания.');
            }
            
            if ($chapters->isEmpty() && !empty($book->full_text)) {
                $text = preg_replace('/(<br\s*\/?>)/i', "\n", $book->full_text);
                $text = preg_replace('/(<\/(p|div|h[1-6]|figure|blockquote|ul|ol)>)/i', "\n", $text);
                $text = strip_tags($text);
                
                $dummyChapter = new \App\Models\Chapter([
                    'title' => $book->title,
                    'content' => trim($text)
                ]);
                $chapters = collect([$dummyChapter]);
            }

            $generatedPath = 'books/files/' . \Illuminate\Support\Str::slug($book->slug) . '_' . time() . '.' . $format;
            $importService = app(\App\Services\BookImportService::class);
            if ($format === 'txt') {
                $content = $importService->generateTxtContent($book, $chapters);
                \Illuminate\Support\Facades\Storage::disk('public')->put($generatedPath, $content);
            } elseif ($format === 'fb2') {
                $importService->generateFb2($book, $chapters, $generatedPath);
            } elseif ($format === 'epub') {
                $success = $importService->generateEpub($book, $chapters, $generatedPath);
                if (!$success) {
                    abort(500, 'Не удалось сгенерировать EPUB.');
                }
            }
            $book->update([$field => $generatedPath]);
            $filePath = $generatedPath;
        }
        $fileUrl = asset('storage/' . $filePath);
        $fileSize = \Illuminate\Support\Facades\Storage::disk('public')->size($filePath);
        if ($fileSize >= 1048576) {
            $formattedSize = number_format($fileSize / 1048576, 2) . ' МБ';
        } elseif ($fileSize >= 1024) {
            $formattedSize = number_format($fileSize / 1024, 0) . ' КБ';
        } else {
            $formattedSize = $fileSize . ' Б';
        }
        $title = 'Скачивание ' . $book->title . ' (' . strtoupper($format) . ') - Librain';
        $genres = Genre::withCount('books')->orderBy('name')->get();
        return view('catalog.books.download', compact('book', 'format', 'fileUrl', 'formattedSize', 'title', 'genres'));
    }
}
