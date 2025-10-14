<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\GenerateMonthlyBills;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('bills:generate', function () {
    $this->call(GenerateMonthlyBills::class);
})->describe('Generate monthly bills for all active agreements');

Schedule::command('bills:generate')->daily();
