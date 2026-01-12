<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/resepsionis/login', function () {
    return redirect('/login');
});

Route::get('/resepsionis/dashboard', function () {
    return view('resepsionis.dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__ . '/auth.php';
