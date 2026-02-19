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

            $query = Book::where('is_published', true)->with('author')->withCount('reviews');

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
        $title = 'Все жанры книг - Librain';
        return view('catalog.genres.index', compact('genres', 'title'));
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
            // For commented sort, we need to fetch reviews instead of books directly if we want to show review cards
            // However, the current view structure for genres expects $books.
            // If the user wants to see reviews like on the main page, we need to pass $reviews.

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

            // We still need $books variable for other sorts or if reviews are empty/not used in the same way
            // But if sort is commented, the view might expect reviews. 
            // Let's keep $books query for structure but maybe not use it if we switch to reviews view.
            // Actually, looking at the main page, it uses $reviews for 'commented' sort.

            // Let's modify the query for books just in case, but we will primarily use $reviews in the view.
            $query->withCount('reviews')->orderByDesc('reviews_count');
        } else {
            $query->orderByDesc('views');
        }

        $books = $query->paginate(12)->withQueryString();

        $title = $genre->seo_title ?? $genre->name . ' - Читать онлайн | Librain';
        $description = $genre->seo_description ?? 'Книги в жанре ' . $genre->name . '. Читайте лучшие произведения онлайн на Librain.';
        $keywords = $genre->seo_keywords ?? $genre->name . ', книги, читать онлайн';

        // Pass reviews if it exists, otherwise null
        $reviews = isset($reviews) ? $reviews : null;

        return view('catalog.genres.show', compact('genre', 'books', 'sort', 'period', 'title', 'description', 'keywords', 'reviews'));
    }

    public function authors()
    {
        $sort = request('sort');
        $currentLetter = request('letter');

        $query = Author::withCount('books')
            ->with([
                'books' => function ($q) {
                    $q->select('id', 'author_id', 'views')->withCount('reviews');
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

        $title = 'Все авторы - Librain';
        return view('catalog.authors.index', compact('authors', 'title', 'sort', 'letters', 'currentLetter'));
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



        $title = $author->seo_title ?? $author->name . ' - Все книги автора | Librain';
        $description = $author->seo_description ?? 'Автор ' . $author->name . '. Читайте лучшие книги автора онлайн на Librain.';
        $keywords = $author->seo_keywords ?? $author->name . ', книги, читать онлайн, автор';

        return view('catalog.authors.show', compact('author', 'books', 'title', 'description', 'keywords', 'filter', 'period'));
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

        $title = 'Все книжные серии - Librain';
        return view('catalog.series.index', compact('series', 'title', 'sort', 'letters', 'currentLetter'));
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

        $title = 'Серия книг "' . $series->name . '" - Librain';
        return view('catalog.series.show', compact('series', 'books', 'title', 'filter', 'period', 'reviews'));
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
                'author',
                'genres',
                'series.books' => function ($q) {
                    $q->select('books.id', 'books.title', 'books.slug', 'books.cover_image', 'books.rating')
                        ->orderBy('pivot_order');
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

        $title = $book->seo_title ?? $book->title . ' - ' . $book->author->name . ' | Librain';
        $description = $book->seo_description ?? Str::limit(strip_tags($book->description), 160);
        $keywords = $book->seo_keywords ?? $book->title . ', ' . $book->author->name . ', читать онлайн';

        return view('catalog.books.show', compact('book', 'isFavorite', 'isPlanned', 'userStatus', 'userRating', 'title', 'description', 'keywords'));
    }

    public function read($slug, $chapterOrder = 1)
    {
        $book = Book::where('slug', $slug)
            ->where('is_published', true)
            ->with([
                'chapters' => function ($q) {
                    $q->orderBy('order');
                }
            ])
            ->firstOrFail();

        $chapter = $book->chapters->where('order', $chapterOrder)->first();


        if (!$chapter && $book->chapters->isNotEmpty()) {
            $chapter = $book->chapters->first();

        }


        if ($book->chapters->isEmpty()) {
            return redirect()->route('books.show', $slug)->with('error', 'В этой книге нет глав для чтения.');
        }


        if (Auth::check()) {
            $totalChapters = $book->chapters->count();

            $progress = $totalChapters > 0 ? round(($chapter->order / $totalChapters) * 100) : 0;

            $progress = min(100, $progress);

            $entry = LibraryEntry::updateOrCreate(
                ['user_id' => Auth::id(), 'book_id' => $book->id],
                [
                    'status' => $progress >= 100 ? 'finished' : 'reading',
                    'progress_percent' => $progress
                ]
            );
        }


        $prevChapter = $book->chapters->where('order', '<', $chapter->order)->sortByDesc('order')->first();
        $nextChapter = $book->chapters->where('order', '>', $chapter->order)->sortBy('order')->first();

        $title = 'Читать ' . $book->title . ' - ' . $chapter->title . ' онлайн | Librain';

        return view('catalog.books.read', compact('book', 'chapter', 'prevChapter', 'nextChapter', 'title'));
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
        $title = 'Топ-100 популярных книг - Librain';

        return view('catalog.top100', compact('books', 'title', 'period'));
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
                        ->orWhereHas('author', function ($q) use ($searchTerm) {
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
                    (CASE WHEN EXISTS (SELECT 1 FROM authors WHERE authors.id = books.author_id AND authors.name LIKE ?) THEN 50 ELSE 0 END) +
                    (CASE WHEN EXISTS (SELECT 1 FROM book_genre JOIN genres ON genres.id = book_genre.genre_id WHERE book_genre.book_id = books.id AND genres.name LIKE ?) THEN 20 ELSE 0 END) +
                    (CASE WHEN EXISTS (SELECT 1 FROM chapters WHERE chapters.book_id = books.id AND chapters.content LIKE ?) THEN 10 ELSE 0 END)
                    as relevance
                ', [$searchTerm, $searchTerm, $searchTerm, $searchTerm])
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
            ->with('author');


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

        if (!$filePath) {
            abort(404);
        }

        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($filePath)) {
            abort(404);
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

        return view('catalog.books.download', compact('book', 'format', 'fileUrl', 'formattedSize', 'title'));
    }
}
