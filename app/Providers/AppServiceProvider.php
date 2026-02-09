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
        Paginator::defaultSimpleView('vendor.pagination.simple-bootstrap-5'); // Keep simple or change if needed
    }
}
