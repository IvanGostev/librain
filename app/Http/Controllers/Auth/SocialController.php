<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)
            ->redirectUrl(route('social.callback', $provider))
            ->redirect();
    }

    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)
                ->redirectUrl(route('social.callback', $provider))
                ->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Ошибка авторизации через ' . $provider . ': ' . $e->getMessage());
        }

        $user = User::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if (!$user) {
            // Check if user with same email exists
            if ($socialUser->getEmail()) {
                $user = User::where('email', $socialUser->getEmail())->first();
            }

            if ($user) {
                // Link account
                $user->update([
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    // If avatar is missing, use social
                    'avatar' => $user->avatar ?: $socialUser->getAvatar(),
                ]);
            } else {
                // Create new user
                $username = $this->generateUsername($socialUser->getNickname() ?? $socialUser->getName());

                $user = User::create([
                    'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                    'email' => $socialUser->getEmail(),
                    'username' => $username,
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'password' => null, // Password is null for social users
                    'avatar' => $socialUser->getAvatar(),
                    'email_verified_at' => now(),
                ]);
            }
        }

        Auth::login($user, true);

        return redirect()->intended('/');
    }

    protected function generateUsername($name)
    {
        $slug = Str::slug($name ?? 'user');
        if (empty($slug)) {
            $slug = 'user-' . Str::random(6);
        }

        // Check availability
        if (User::where('username', $slug)->exists()) {
            $slug .= '-' . Str::random(4);
        }

        // Loop just in case
        while (User::where('username', $slug)->exists()) {
            $slug = Str::slug($name ?? 'user') . '-' . Str::random(4);
        }

        return $slug;
    }
}
