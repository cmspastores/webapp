<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Carbon;

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
        // ✅ Enforce Asia/Manila timezone across the app
        $timezone = config('app.timezone', 'Asia/Manila');
        date_default_timezone_set($timezone);

        // ✅ Make sure Carbon respects the timezone + locale
        Carbon::setLocale(config('app.locale', 'en'));
        Carbon::now($timezone);
    }
}
