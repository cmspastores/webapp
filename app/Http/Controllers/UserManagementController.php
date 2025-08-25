<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    // Show list of all users
    public function index()
    {
        $users = User::all();
        return view('settings.users', compact('users'));
    }

    // Block a user
    public function block($id)
    {
        $user = User::findOrFail($id);
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
    
}