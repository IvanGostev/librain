<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function store(Request $request, Book $book)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:10',
        ]);

        $rating = Rating::updateOrCreate(
            ['user_id' => Auth::id(), 'book_id' => $book->id],
            ['rating' => $request->rating]
        );

        $book->recalculateRating();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'rating' => $book->rating, // new average
                'user_rating' => $rating->rating
            ]);
        }

        return back()->with('success', 'Ваша оценка учтена!');
    }
}
