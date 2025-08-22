<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LoginLogController;
use App\Http\Controllers\TaskController;
use App\Models\LoginLog;

// Public Route
Route::get('/', function () {
    return view('welcome');
});

// Dashboard Route (with login logs)
Route::get('/dashboard', function () {
    $logs = LoginLog::latest()->paginate(10);
    return view('dashboard', compact('logs'));
})->middleware(['auth', 'verified'])->name('dashboard');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Login Logs View (optional)
    Route::get('/login-logs', [LoginLogController::class, 'index']);

    // Task Manager UI (custom page)
    Route::get('/task-manager', [TaskController::class, 'showManager'])->name('task.manager');

    // Task CRUD (inline handled via task-manager blade)
    Route::resource('tasks', TaskController::class)->except(['index', 'show']);
});

require __DIR__.'/auth.php';
