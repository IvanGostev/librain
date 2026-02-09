<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Book $book)
    {
        $request->validate([
            'comment' => 'required|string|min:3',
            'rating' => 'nullable|integer|min:1|max:5',
            'parent_id' => 'nullable|exists:reviews,id',
        ]);

        Review::create([
            'user_id' => Auth::id(),
            'book_id' => $book->id,
            'comment' => $request->comment,
            'rating' => $request->rating,
            'parent_id' => $request->parent_id,
            'is_approved' => false,
        ]);

        return back()->with('success', 'Ваш отзыв отправлен на модерацию. Он появится после проверки.');
    }
}
