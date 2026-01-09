<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResepsionisAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Routes untuk Resepsionis
Route::prefix('resepsionis')->name('resepsionis.')->group(function () {
    // Login routes (no middleware, manual check in controller)
    Route::get('/login', [ResepsionisAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [ResepsionisAuthController::class, 'login']);

    // Routes untuk resepsionis yang sudah login
    Route::middleware('auth:resepsionis')->group(function () {
        Route::post('/logout', [ResepsionisAuthController::class, 'logout'])->name('logout');

        // Dashboard resepsionis
        Route::get('/dashboard', function () {
            return view('resepsionis.login');
        })->name('dashboard');
    });
});

require __DIR__ . '/auth.php';
