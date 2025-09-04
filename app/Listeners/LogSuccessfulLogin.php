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
        // Check if the user already has a login record without a logout
        $existingLog = LoginLog::where('user_id', $event->user->id)
            ->whereNull('logged_out_at')
            ->latest('logged_in_at')
            ->first();

        // Only create a new login log if there is no active session
        if (!$existingLog) {
            LoginLog::create([
                'user_id' => $event->user->id,
                'email' => $event->user->email,
                'logged_in_at' => now(),
            ]);
        }
    }
}

