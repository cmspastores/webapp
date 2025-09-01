<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    // Show list of all users with optional filtering
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by name
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Filter by email
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        // Filter by creation date (from)
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        // Filter by creation date (to)
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        // Paginate results (adjust per-page count if needed)
        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        // Keep the filter query string in pagination links
        $users->appends($request->only(['name', 'email', 'from', 'to']));

        return view('settings.users', compact('users'));
    }

    // Block a user
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

    // Unblock a user
    public function unblock($id)
    {
        $user = User::findOrFail($id);
        $user->is_blocked = 0;
        $user->save();

        return redirect()->route('settings.users')
                         ->with('success', "User {$user->name} has been unblocked.");
    }

    // Delete a user
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // 🚨 Prevent deleting admins
        if ($user->is_admin) {
            return redirect()->route('settings.users')
                             ->with('error', "You cannot delete an admin account.");
        }

        $user->delete(); // soft delete if model uses SoftDeletes, permanent otherwise

        return redirect()->route('settings.users')
                         ->with('success', "User {$user->name} has been deleted.");
    }
}
