<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/resepsionis/login', function () {
    return redirect('/login');
});

// Routes untuk Resepsionis
Route::prefix('resepsionis')->name('resepsionis.')->group(function () {
    // Login routes
    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store']);

    // Forgot Password Routes
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');

    // Routes untuk resepsionis yang sudah login
    Route::middleware('auth:resepsionis')->group(function () {
        Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');

        // Dashboard resepsionis
        Route::get('/dashboard', function () {
            return view('resepsionis.dashboard');
        })->name('dashboard');
    });
});

require __DIR__ . '/auth.php';
