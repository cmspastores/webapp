<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LoginLogController;
use App\Http\Controllers\RoomsController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\RentersController;
use App\Http\Controllers\AgreementController;
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

    // Rooms Management
    Route::resource('rooms', RoomsController::class);

    // Room Types Management
    Route::resource('roomtypes', RoomTypeController::class);

    // ============================
    // Renters Management
    // ============================
    // Full RESTful routes, including destroy
    Route::get('/renters/deleted', [RentersController::class, 'deleted'])->name('renters.deleted');
    Route::put('/renters/{renter}/restore', [RentersController::class, 'restore'])->name('renters.restore');

    Route::resource('renters', RentersController::class);

    // This defines:
    // GET /renters -> index (renters.index)
    // GET /renters/create -> create (renters.create)
    // POST /renters -> store (renters.store)
    // GET /renters/{renter} -> show (renters.show)
    // GET /renters/{renter}/edit -> edit (renters.edit)
    // PUT /renters/{renter} -> update (renters.update)
    // DELETE /renters/{renter} -> destroy (renters.destroy) ✅

    // Rental Agreements Management
    Route::resource('agreements', AgreementController::class);

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
