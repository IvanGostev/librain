<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale('ru');
        Paginator::defaultView('vendor.pagination.avito');
        Paginator::defaultSimpleView('vendor.pagination.simple-bootstrap-5');



        \Illuminate\Support\Facades\Event::listen(
            \SocialiteProviders\Manager\SocialiteWasCalled::class,
            \SocialiteProviders\Odnoklassniki\OdnoklassnikiExtendSocialite::class . '@handle'
        );

        \Illuminate\Support\Facades\Event::listen(
            \SocialiteProviders\Manager\SocialiteWasCalled::class,
            \SocialiteProviders\Telegram\TelegramExtendSocialite::class . '@handle'
        );

        \Illuminate\Support\Facades\Event::listen(
            \SocialiteProviders\Manager\SocialiteWasCalled::class,
            \SocialiteProviders\VKontakte\VKontakteExtendSocialite::class . '@handle'
        );

        \Illuminate\Auth\Notifications\VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('Активация аккаунта — ' . config('app.name', 'Librain'))
                ->view('emails.verify-email', ['url' => $url, 'user' => $notifiable]);
        });

        \Illuminate\Auth\Notifications\ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('Сброс пароля — ' . config('app.name', 'Librain'))
                ->view('emails.reset-password', ['url' => $url, 'user' => $notifiable]);
        });
    }
}
