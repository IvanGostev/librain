<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    public function show(User $user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }

        if (!$user) {
            abort(404);
        }

        $stats = [
            'finished_books' => $user->libraryEntries()->where('status', 'finished')->count(),
            'reviews_count' => $user->reviews()->count(),
            'days_on_site' => max(1, now()->diffInDays($user->created_at) + 1),
        ];

        $latestReviews = $user->reviews()
            ->with('book')
            ->latest()
            ->take(5)
            ->get();

        $title = $user->name . ' - Профиль пользователя | Librain';

        return view('profile.show', compact('user', 'stats', 'latestReviews', 'title'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => 'sometimes|required|string|max:255',
            'gender' => 'nullable|string|in:male,female,other',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|max:2048',
        ];

        $validated = $request->validate($rules);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($validated);

        return redirect()->route('profile.show')->with('status', 'Профиль обновлен!');
    }
}