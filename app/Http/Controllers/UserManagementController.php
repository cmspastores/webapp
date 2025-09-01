<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\User;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('settings.users', compact('users'));
    }

    // ✅ Block a user
    public function block($id)
    {
        $user = User::findOrFail($id);

        // 🚨 Prevent blocking admins
        if ($user->is_admin) {
            return redirect()->route('settings.users')
                             ->with('error', "You cannot block an admin account.");
        }

        $user->is_blocked = 1;
        $user->save();

        return redirect()->route('settings.users')
                         ->with('success', "User {$user->name} has been blocked.");
    }

    // ✅ Unblock a user
    public function unblock($id)
    {
        $user = User::findOrFail($id);
        $user->is_blocked = 0;
        $user->save();

        return redirect()->route('settings.users')
                         ->with('success', "User {$user->name} has been unblocked.");
    }
}