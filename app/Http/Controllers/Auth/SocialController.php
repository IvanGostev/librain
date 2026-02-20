<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;

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

        if ($user) {
            Auth::login($user, true);
            return redirect()->intended('/');
        }

        return $this->registerOrLogin(
            $socialUser->getEmail(),
            $provider,
            $socialUser->getId(),
            $socialUser->getName(),
            $socialUser->getNickname(),
            $socialUser->getAvatar()
        );
    }



    protected function registerOrLogin($email, $provider, $providerId, $name, $nickname, $avatar)
    {
        $user = $email ? User::where('email', $email)->first() : null;

        if ($user) {
            $user->update([
                'provider' => $provider,
                'provider_id' => $providerId,
                'avatar' => $user->avatar ?: $avatar,
            ]);
        } else {
            $username = $this->generateUsername($nickname ?? $name);

            $user = User::create([
                'name' => $name ?? $nickname ?? 'User',
                'email' => $email,
                'username' => $username,
                'provider' => $provider,
                'provider_id' => $providerId,
                'password' => null,
                'avatar' => $avatar ?? null,
                'email_verified_at' => now(),
            ]);
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


        if (User::where('username', $slug)->exists()) {
            $slug .= '-' . Str::random(4);
        }


        while (User::where('username', $slug)->exists()) {
            $slug = Str::slug($name ?? 'user') . '-' . Str::random(4);
        }

        return $slug;
    }
}
