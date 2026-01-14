<?php

use App\Http\Controllers\TamuController;
use App\Http\Controllers\KunjunganConfirmController;
use App\Http\Controllers\NotulensiController;
use App\Http\Controllers\ResepsionisController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('tamu.form');
});

Route::get('/resepsionis/login', function () {
    return redirect('/login');
});

Route::prefix('tamu')->name('tamu.')->group(function () {
    Route::get('/form', [TamuController::class, 'showForm'])->name('form');
    Route::get('/search-karyawan', [TamuController::class, 'searchKaryawan'])->name('search-karyawan');
    Route::post('/submit', [TamuController::class, 'submitForm'])->name('submit');
});

Route::get('/kunjungan/confirm/{token}', [KunjunganConfirmController::class, 'confirm'])->name('kunjungan.confirm');
Route::post('/kunjungan/process/{token}', [KunjunganConfirmController::class, 'process'])->name('kunjungan.process');

Route::prefix('notulensi')->name('notulensi.')->group(function () {
    Route::get('/create/{token}', [NotulensiController::class, 'create'])->name('create');
    Route::post('/store/{token}', [NotulensiController::class, 'store'])->name('store');
    Route::get('/view/{token}', [NotulensiController::class, 'view'])->name('view');
    Route::get('/dokumentasi/{token}/stream', [ResepsionisController::class, 'streamDokumentasi'])->name('dokumentasi.stream');
});

Route::prefix('resepsionis')->name('resepsionis.')->group(function () {
    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store']);

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');

    Route::middleware('auth:resepsionis')->group(function () {
        Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');

        Route::get('/dashboard', [ResepsionisController::class, 'dashboard'])->name('dashboard');
        Route::get('/kunjungan/data', [ResepsionisController::class, 'getKunjunganData'])->name('kunjungan.data');
        Route::get('/kunjungan/create', [ResepsionisController::class, 'createKunjungan'])->name('kunjungan.create');
        Route::post('/kunjungan/{id}/accept', [ResepsionisController::class, 'acceptKunjungan'])->name('kunjungan.accept');
        Route::post('/kunjungan/{id}/reject', [ResepsionisController::class, 'rejectKunjungan'])->name('kunjungan.reject');
        Route::get('/ktp/{tamuId}/signed-url', [ResepsionisController::class, 'getKtpSignedUrl'])->name('ktp.signed-url');
        Route::get('/ktp/{token}/stream', [ResepsionisController::class, 'streamKtp'])->name('ktp.stream');
        Route::get('/riwayat', [ResepsionisController::class, 'riwayat'])->name('riwayat');
        Route::get('/karyawan', [ResepsionisController::class, 'daftarKaryawan'])->name('karyawan');
        Route::get('/karyawan/data', [ResepsionisController::class, 'getKaryawanData'])->name('karyawan.data');
    });
});

require __DIR__ . '/auth.php';
