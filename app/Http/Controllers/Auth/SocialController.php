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

        if (!$socialUser->getEmail()) {
            session([
                'social_user' => [
                    'provider' => $provider,
                    'id' => $socialUser->getId(),
                    'name' => $socialUser->getName(),
                    'nickname' => $socialUser->getNickname(),
                    'avatar' => $socialUser->getAvatar(),
                ]
            ]);
            return redirect()->route('social.email');
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

    public function showEmailForm()
    {
        if (!session()->has('social_user')) {
            return redirect()->route('login');
        }
        return view('auth.social_email');
    }

    public function storeEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        if (!session()->has('social_user')) {
            return redirect()->route('login');
        }

        $socialData = session('social_user');

        $response = $this->registerOrLogin(
            $request->email,
            $socialData['provider'],
            $socialData['id'],
            $socialData['name'],
            $socialData['nickname'],
            $socialData['avatar']
        );

        session()->forget('social_user');

        return $response;
    }

    protected function registerOrLogin($email, $provider, $providerId, $name, $nickname, $avatar)
    {
        $user = User::where('email', $email)->first();

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
