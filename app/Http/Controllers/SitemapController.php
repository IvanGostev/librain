<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Author;
use App\Models\Series;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    const PER_PAGE = 5000;

    public function index(): Response
    {
        $booksCount = Book::where('is_published', true)->count();
        $bookPages = ceil($booksCount / self::PER_PAGE);

        $latestBook = Book::where('is_published', true)->orderBy('updated_at', 'desc')->first();
        $latestGenre = Genre::orderBy('updated_at', 'desc')->first();
        $latestAuthor = Author::orderBy('updated_at', 'desc')->first();
        $latestSeries = Series::orderBy('updated_at', 'desc')->first();

        return response()->view('sitemap.index', [
            'bookPages' => $bookPages,
            'latestBookDate' => $latestBook ? $latestBook->updated_at->toAtomString() : now()->toAtomString(),
            'latestGenreDate' => $latestGenre ? $latestGenre->updated_at->toAtomString() : now()->toAtomString(),
            'latestAuthorDate' => $latestAuthor ? $latestAuthor->updated_at->toAtomString() : now()->toAtomString(),
            'latestSeriesDate' => $latestSeries ? $latestSeries->updated_at->toAtomString() : now()->toAtomString(),
        ])->header('Content-Type', 'text/xml');
    }

    public function pages(): Response
    {
        return response()->view('sitemap.pages')->header('Content-Type', 'text/xml');
    }

    public function genres(): Response
    {
        $genres = Genre::all();
        return response()->view('sitemap.genres', compact('genres'))->header('Content-Type', 'text/xml');
    }

    public function authors(): Response
    {
        $authors = Author::all();
        return response()->view('sitemap.authors', compact('authors'))->header('Content-Type', 'text/xml');
    }

    public function series(): Response
    {
        $series = Series::all();
        return response()->view('sitemap.series', compact('series'))->header('Content-Type', 'text/xml');
    }

    public function books($index = null): Response
    {
        $page = $index ? (int) $index : 1;
        
        $books = Book::where('is_published', true)
                     ->orderBy('id')
                     ->skip(($page - 1) * self::PER_PAGE)
                     ->take(self::PER_PAGE)
                     ->get();

        if ($books->isEmpty()) {
            abort(404);
        }

        return response()->view('sitemap.books', compact('books'))->header('Content-Type', 'text/xml');
    }
}
