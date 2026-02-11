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
        $rules = [
            'comment' => 'required|string|min:3',
            'rating' => 'nullable|integer|min:1|max:5',
            'parent_id' => 'nullable|exists:reviews,id',
        ];

        if (!Auth::check()) {
            $rules['guest_name'] = 'required|string|max:255';
        }

        $validated = $request->validate($rules);

        Review::create([
            'user_id' => Auth::id(),
            'guest_name' => Auth::check() ? null : $validated['guest_name'],
            'book_id' => $book->id,
            'comment' => $validated['comment'],
            'rating' => $validated['rating'] ?? null, // FIXED: removed extra 'rating' key if duplicated
            'parent_id' => $validated['parent_id'] ?? null,
            'is_approved' => false,
        ]);

        return back()->with('success', 'Ваш отзыв отправлен на модерацию. Он появится после проверки.');
    }
}
