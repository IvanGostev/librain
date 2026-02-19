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
        $hidden = $query($user->libraryEntries()->where('library_entries.status', 'blacklist'))->get();

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

    public function updateStatus(Request $request, Book $book)
    {
        $status = $request->input('status');

        if (!in_array($status, ['planned', 'reading', 'finished', 'dropped', 'none', 'blacklist'])) {
            return back()->with('error', 'Некорректный статус');
        }

        $user = Auth::user();

        $entry = LibraryEntry::firstOrCreate(
            ['user_id' => $user->id, 'book_id' => $book->id],
            ['progress_percent' => 0]
        );

        if ($status === 'none') {
            $entry->status = null;
            if (!$entry->is_favorite) {
                $entry->delete();
            } else {
                $entry->save();
            }
            return back()->with('success', 'Книга удалена из библиотеки');
        } else {
            $entry->status = $status;
            $entry->save();

            $statusName = match ($status) {
                'planned' => 'Хочу прочитать',
                'reading' => 'Читаю',
                'finished' => 'Прочитано',
                'dropped' => 'Брошено',
                'blacklist' => 'В черном списке',
                default => ''
            };

            return back()->with('success', 'Статус обновлен: ' . $statusName);
        }
    }
}