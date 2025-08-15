<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use App\Models\LoginLog;

class LogSuccessfulLogout
{
    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        LoginLog::where('user_id', $event->user->id)
            ->whereNull('logged_out_at')
            ->latest('logged_in_at')
            ->first()?->update([
                'logged_out_at' => now(),
            ]);
    }
}
