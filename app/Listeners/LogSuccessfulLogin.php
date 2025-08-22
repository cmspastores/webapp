<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\LoginLog;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        LoginLog::create([
            'user_id' => $event->user->id,
            'email' => $event->user->email,
            'logged_in_at' => now(),
        ]);
    }
}

