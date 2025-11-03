<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LoginLogController;
use App\Http\Controllers\RoomsController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\RentersController;
use App\Http\Controllers\AgreementController;
use App\Http\Controllers\BillController;

use App\Models\BillCharge;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\SettingsController;
use App\Models\LoginLog;
use App\Http\Controllers\ReservationController;

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

    // Reservation Management
    // Specific reservation actions must be defined BEFORE the resource route
    // so static paths like /reservation/archived do not get captured by
    // the resource show route (reservation/{reservation}).
    Route::post('reservation/{reservation}/confirm', [ReservationController::class, 'confirm'])
        ->name('reservation.confirm');
    Route::post('reservation/{reservation}/archive', [ReservationController::class, 'archive'])
        ->name('reservation.archive');
    Route::get('reservation/archived', [ReservationController::class, 'archived'])
        ->name('reservation.archived');

    Route::resource('reservation', ReservationController::class);

    // Confirm pending reservation (create renter + agreement and link)
    Route::post('reservation/{reservation}/confirm', [ReservationController::class, 'confirm'])
        ->name('reservation.confirm');

    // Archive a pending reservation (available to non-admin users)
    Route::post('reservation/{reservation}/archive', [ReservationController::class, 'archive'])
        ->name('reservation.archive');

    // View archived pending reservations
    Route::get('reservation/archived', [ReservationController::class, 'archived'])
        ->name('reservation.archived');

    // Rental Agreements Management
    Route::get('/agreements/archived', [AgreementController::class, 'archived'])->name('agreements.archived');
    Route::resource('agreements', AgreementController::class);

    Route::post('/agreements/{agreement}/renew', [AgreementController::class, 'renew'])->name('agreements.renew')
        ->middleware('admin');

    Route::post('/agreements/{agreement}/terminate', [AgreementController::class, 'terminate'])->name('agreements.terminate')
        ->middleware('admin');
    
    // ============================
    // Bills Management 
    // ============================

    // 🔹 Reports route first
    Route::get('/bills/reports', [BillController::class, 'reports'])
         ->name('bills.reports');

    // Generate all bills (explicit endpoint)
    Route::post('/bills/generate-all', [\App\Http\Controllers\BillController::class, 'generateAll'])
        ->name('bills.generateAll')
        ->middleware('auth'); // adjust middleware as needed

    // create/destroy bill charges
    Route::post('bills/{bill}/charges', [\App\Http\Controllers\BillChargeController::class, 'store'])
        ->name('bills.charges.store');

    Route::delete('bills/{bill}/charges/{charge}', [\App\Http\Controllers\BillChargeController::class, 'destroy'])
        ->name('bills.charges.destroy');

    // Resource routes for bills (index, create, store, show, destroy etc.)
    Route::resource('bills', \App\Http\Controllers\BillController::class);

    // ============================
    // Payments Management
    // ============================
    Route::get('payments', [PaymentController::class,'index'])->name('payments.index');
    Route::get('payments/create', [PaymentController::class,'create'])->name('payments.create');
    Route::post('payments', [PaymentController::class,'store'])->name('payments.store');
    Route::get('payments/{payment}', [PaymentController::class,'show'])->name('payments.show');
    Route::delete('payments/{payment}', [PaymentController::class,'destroy'])->name('payments.destroy');

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
