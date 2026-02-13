<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LibraryEntry;
use App\Models\Book;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $sort = $request->get('sort', 'latest');
        $activeTab = $request->get('tab', 'reading');

        $query = function ($q) use ($sort) {
            $q->with('book.author')
                ->join('books', 'library_entries.book_id', '=', 'books.id')
                ->select('library_entries.*');

            if ($sort === 'title') {
                $q->orderBy('books.title', 'asc');
            } elseif ($sort === 'author') {
                $q->join('authors', 'books.author_id', '=', 'authors.id')
                    ->orderBy('authors.name', 'asc');
            } else {
                $q->orderByDesc('library_entries.created_at');
            }
            return $q;
        };

        $reading = $query($user->libraryEntries()->where('library_entries.status', 'reading'))->get();
        $wantToRead = $query($user->libraryEntries()->where('library_entries.status', 'planned'))->get();
        $completed = $query($user->libraryEntries()->where('library_entries.status', 'finished'))->get();
        $favorites = $query($user->libraryEntries()->where('library_entries.is_favorite', true))->get();
        $writing = $query($user->libraryEntries()->where('books.status', 'writing'))->get();
        $hidden = $query($user->libraryEntries()->where('books.is_published', false))->get();

        $title = 'Моя библиотека - Librain';

        return view('library.index', compact('reading', 'wantToRead', 'completed', 'favorites', 'writing', 'hidden', 'sort', 'activeTab', 'title'));
    }

    public function toggleFavorite(Request $request, Book $book)
    {
        $user = Auth::user();

        $entry = LibraryEntry::firstOrCreate(
            ['user_id' => $user->id, 'book_id' => $book->id],
            ['progress_percent' => 0]
        );

        $entry->is_favorite = !$entry->is_favorite;
        $entry->save();

        if (!$entry->is_favorite && is_null($entry->status)) {
            $entry->delete();
        }

        $message = $entry->is_favorite ? 'Книга добавлена в избранное!' : 'Книга удалена из избранного.';

        return back()->with('success', $message);
    }

    public function togglePlanned(Request $request, Book $book)
    {
        $user = Auth::user();

        $entry = LibraryEntry::firstOrCreate(
            ['user_id' => $user->id, 'book_id' => $book->id],
            ['progress_percent' => 0]
        );

        if ($entry->status === 'planned') {
            $entry->status = null;
            $message = 'Книга удалена из списка "Хочу прочитать".';
        } else {
            $entry->status = 'planned';
            $message = 'Книга добавлена в список "Хочу прочитать"!';
        }

        $entry->save();

        if (!$entry->is_favorite && is_null($entry->status)) {
            $entry->delete();
        }

        return back()->with('success', $message);
    }
}