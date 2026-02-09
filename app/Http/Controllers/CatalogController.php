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

class CatalogController extends Controller
{
    public function index()
    {
        $books = Book::where('is_published', true)->orderByDesc('created_at')->paginate(24);
        $title = 'Каталог книг - Читать онлайн на Librain';
        return view('catalog.index', compact('books', 'title'));
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
        $sort = $request->get('sort', 'popular');

        $query = $genre->books()->where('is_published', true);

        if ($sort === 'latest') {
            $query->latest();
        } elseif ($sort === 'rating') {
            $query->orderByDesc('rating');
        } else {
            $query->orderByDesc('views');
        }

        $books = $query->paginate(12)->withQueryString();

        $title = $genre->seo_title ?? $genre->name . ' - Читать онлайн | Librain';
        $description = $genre->seo_description ?? 'Книги в жанре ' . $genre->name . '. Читайте лучшие произведения онлайн на Librain.';
        $keywords = $genre->seo_keywords ?? $genre->name . ', книги, читать онлайн';

        return view('catalog.genres.show', compact('genre', 'books', 'sort', 'title', 'description', 'keywords'));
    }

    public function authors()
    {
        $authors = Author::withCount('books')->orderBy('name')->paginate(20);
        $title = 'Все авторы - Librain';
        return view('catalog.authors.index', compact('authors', 'title'));
    }

    public function author($slug)
    {
        $author = Author::where('slug', $slug)
            ->withCount('books')
            ->with('books')
            ->firstOrFail();


        $author->views_count = $author->books->sum('views');

        $title = $author->seo_title ?? $author->name . ' - Все книги автора | Librain';
        $description = $author->seo_description ?? 'Автор ' . $author->name . '. Читайте лучшие книги автора онлайн на Librain.';
        $keywords = $author->seo_keywords ?? $author->name . ', книги, читать онлайн, автор';

        return view('catalog.authors.show', compact('author', 'title', 'description', 'keywords'));
    }

    public function series()
    {
        $series = Series::withCount('books')
            ->with([
                    'books' => function ($q) {
                        $q->select('books.id', 'books.views')->withCount('reviews');
                    }
                ])
            ->orderBy('name')
            ->paginate(20);
        $title = 'Все книжные серии - Librain';
        return view('catalog.series.index', compact('series', 'title'));
    }

    public function seriesShow($slug)
    {
        $series = Series::where('slug', $slug)->with([
            'books' => function ($q) {
                $q->orderBy('pivot_order');
            }
        ])->firstOrFail();
        $title = 'Серия книг "' . $series->name . '" - Librain';
        return view('catalog.series.show', compact('series', 'title'));
    }

    public function book(Request $request, $slug)
    {
        $reviewsSort = $request->input('reviews_sort', 'newest');

        $book = Book::where('slug', $slug)
            ->with(['author', 'genres', 'series'])
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
                                    'children' => function ($q) {
                                        $q->where('is_approved', true)->with([
                                            'user',
                                            'children' => function ($q) {
                                                $q->where('is_approved', true)->with('user');
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


        $book->increment('views');

        $isFavorite = false;
        $isPlanned = false;
        if (Auth::check()) {
            $entry = Auth::user()->libraryEntries()
                ->where('book_id', $book->id)
                ->first();

            if ($entry) {
                $isFavorite = $entry->is_favorite;
                $isPlanned = $entry->status === 'planned';
            }
        }

        $title = $book->seo_title ?? $book->title . ' - ' . $book->author->name . ' | Librain';
        $description = $book->seo_description ?? Str::limit(strip_tags($book->description), 160);
        $keywords = $book->seo_keywords ?? $book->title . ', ' . $book->author->name . ', читать онлайн';

        return view('catalog.books.show', compact('book', 'isFavorite', 'isPlanned', 'title', 'description', 'keywords'));
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
        $books = Book::where('is_published', true)
            ->orderByDesc('views')
            ->take(100)
            ->get();
        $title = 'Топ-100 популярных книг - Librain';
        return view('catalog.top100', compact('books', 'title'));
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
}
