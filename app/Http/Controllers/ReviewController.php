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
            'rating' => $validated['rating'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
            'is_approved' => false,
        ]);

        return back()->with('success', 'Ваш отзыв отправлен на модерацию. Он появится после проверки.');
    }
    public function vote(Request $request, Review $review)
    {
        $type = $request->input('type');

        if (!in_array($type, ['like', 'dislike'])) {
            return response()->json(['error' => 'Invalid vote type'], 400);
        }

        $userId = Auth::id();
        $ip = $request->ip();

        $vote = \App\Models\ReviewVote::where('review_id', $review->id)
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->when(!$userId, fn($q) => $q->where('ip_address', $ip))
            ->first();

        if ($vote) {
            if ($vote->type === $type) {

                $vote->delete();
                $action = 'removed';
            } else {

                $vote->update(['type' => $type]);
                $action = 'updated';
            }
        } else {
            \App\Models\ReviewVote::create([
                'review_id' => $review->id,
                'user_id' => $userId,
                'ip_address' => $ip,
                'type' => $type,
            ]);
            $action = 'created';
        }


        $likes = \App\Models\ReviewVote::where('review_id', $review->id)->where('type', 'like')->count();
        $dislikes = \App\Models\ReviewVote::where('review_id', $review->id)->where('type', 'dislike')->count();

        return response()->json([
            'success' => true,
            'action' => $action,
            'likes' => $likes,
            'dislikes' => $dislikes,
        ]);
    }
}
