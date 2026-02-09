<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Author;
use App\Models\Series;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $books = Book::where('is_published', true)->orderBy('updated_at', 'desc')->get();
        $genres = Genre::all();
        $authors = Author::all();
        $series = Series::all();

        return response()->view('sitemap', [
            'books' => $books,
            'genres' => $genres,
            'authors' => $authors,
            'series' => $series,
        ])->header('Content-Type', 'text/xml');
    }
}
