<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force Laravel + Carbon to use Asia/Manila timezone
        config(['app.timezone' => 'Asia/Manila']);
        date_default_timezone_set(config('app.timezone'));
        Carbon::setLocale(config('app.locale'));
    }
}
