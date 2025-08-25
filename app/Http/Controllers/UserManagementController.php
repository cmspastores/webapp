<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\User;
use Illuminate\Http\Request;

class LogController extends Controller
{
    // ✅ Show filtered logs
    public function index(Request $request)
    {
        $logs = Log::with('user')
            ->when($request->filled('email'), function ($query) use ($request) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('email', 'like', '%' . $request->email . '%');
                });
            })
            ->when($request->filled('from'), function ($query) use ($request) {
                $query->whereDate('logged_in_at', '>=', $request->from);
            })
            ->when($request->filled('to'), function ($query) use ($request) {
                $query->whereDate('logged_in_at', '<=', $request->to);
            })
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        return view('settings.users', compact('users'));
    }

    // ✅ Block a user
    public function block($id)
    {
        $user = User::findOrFail($id);
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
