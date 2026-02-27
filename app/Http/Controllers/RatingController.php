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

        $userId = Auth::id();
        $ip = $request->ip();

        if ($userId) {
            if (!Auth::user()->hasVerifiedEmail()) {
                if ($request->wantsJson()) {
                    return response()->json(['error' => 'Пожалуйста, подтвердите email для этого действия.'], 403);
                }
                return back()->with('error', 'Пожалуйста, подтвердите email для этого действия.');
            }
            $rating = Rating::updateOrCreate(
                ['user_id' => $userId, 'book_id' => $book->id],
                ['rating' => $request->rating, 'ip_address' => $ip]
            );
        } else {
            $rating = Rating::updateOrCreate(
                ['ip_address' => $ip, 'book_id' => $book->id, 'user_id' => null],
                ['rating' => $request->rating]
            );
        }

        $book->recalculateRating();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'rating' => $book->rating,
                'user_rating' => $rating->rating
            ]);
        }

        return back()->with('success', 'Ваша оценка учтена!');
    }
}
