<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResepsionisAuthController;
use App\Http\Controllers\TamuController;
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

Route::prefix('tamu')->name('tamu.')->group(function () {
    Route::get('/form', [TamuController::class, 'showForm'])->name('form');
    Route::get('/search-karyawan', [TamuController::class, 'searchKaryawan'])->name('search-karyawan');
    Route::post('/submit', [TamuController::class, 'submitForm'])->name('submit');
});

Route::prefix('resepsionis')->name('resepsionis.')->group(function () {
    Route::get('/login', [ResepsionisAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [ResepsionisAuthController::class, 'login']);

    Route::middleware('auth:resepsionis')->group(function () {
        Route::post('/logout', [ResepsionisAuthController::class, 'logout'])->name('logout');

        Route::get('/dashboard', function () {
            return view('resepsionis.dashboard');
        })->name('dashboard');
    });
});

require __DIR__ . '/auth.php';
