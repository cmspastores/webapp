<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LoginLogController;
use App\Http\Controllers\RoomtypesController;

use App\Http\Controllers\RentersController;

use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\SettingsController;
use App\Models\LoginLog;

// Public Route
Route::get('/', function () {
    return view('welcome');
});

// Dashboard Route (with login logs)
Route::get('/dashboard', function () {
    $logs = LoginLog::latest()->paginate(5);
    return view('dashboard', compact('logs'));
})->middleware(['auth', 'verified'])->name('dashboard');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy')->middleware('auth');


    // Login Logs View (optional)
    Route::get('/login-logs', [LoginLogController::class, 'logstable']);

    // Room Types UI (custom page)
    Route::get('/Roomtypes', [RoomtypesController::class, 'showManager'])->name('roomtypes.manager');

    // Room Types CRUD (inline handled via task-manager blade)
    Route::resource('roomtypes', RoomTypesController::class)->except(['show']);

    // Renters Management
    Route::get('renters/create', [RentersController::class, 'create'])->name('renters.create');
    Route::post('renters', [RentersController::class, 'store'])->name('renters.store');
    Route::get('renters/{renter}', [RentersController::class, 'show'])->name('renters.show');
    Route::get('renters/{renter}/edit', [RentersController::class, 'edit'])->name('renters.edit');
    Route::put('renters/{renter}', [RentersController::class, 'update'])->name('renters.update');
    Route::delete('renters/{renter}', [RentersController::class, 'destroy'])->name('renters.destroy');
    Route::get('renters', [RentersController::class, 'index'])->name('renters.index');




    
    // ============================
    // Settings (All users)
    // ============================
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');

    // ============================
    // User Management (Admin Only)
    // ============================
    Route::middleware(['admin'])->group(function () {
        Route::get('/settings/users', [UserManagementController::class, 'index'])->name('settings.users');
        Route::post('/settings/users/{id}/block', [UserManagementController::class, 'block'])->name('settings.users.block');
        Route::post('/settings/users/{id}/unblock', [UserManagementController::class, 'unblock'])->name('settings.users.unblock');

    });
});

require __DIR__.'/auth.php';
