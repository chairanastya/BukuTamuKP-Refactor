<?php

use App\Http\Controllers\TamuController;
use App\Http\Controllers\KunjunganConfirmController;
use App\Http\Controllers\NotulensiController;
use App\Http\Controllers\ResepsionisController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\ResepsionisAccountController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('tamu.form');
});

Route::prefix('tamu')->name('tamu.')->group(function () {
    Route::get('/form', [TamuController::class, 'showForm'])->name('form');
    Route::get('/search-karyawan', [TamuController::class, 'searchKaryawan'])->name('search-karyawan')->middleware('throttle:api');
    Route::post('/submit', [TamuController::class, 'submitForm'])->name('submit')->middleware('throttle:submissions');
});

// API untuk Supabase Realtime - HANYA untuk authenticated users
Route::middleware('auth:resepsionis')->group(function () {
    Route::get('/api/supabase-config', function () {
        return response()->json([
            'url' => env('SUPABASE_URL'),
            'key' => env('SUPABASE_ANON_KEY')
        ]);
    });
});

Route::get('/kunjungan/confirm/{token}', [KunjunganConfirmController::class, 'confirm'])->name('kunjungan.confirm');
Route::post('/kunjungan/process/{token}', [KunjunganConfirmController::class, 'process'])->name('kunjungan.process')->middleware('throttle:submissions');

Route::prefix('notulensi')->name('notulensi.')->group(function () {
    Route::get('/create/{token}', [NotulensiController::class, 'create'])->name('create');
    Route::post('/store/{token}', [NotulensiController::class, 'store'])->name('store')->middleware('throttle:submissions');
    Route::get('/view/{token}', [NotulensiController::class, 'view'])->name('view');
    
    // Stream dokumentasi - memerlukan autentikasi
    Route::get('/dokumentasi/{token}/stream', [ResepsionisController::class, 'streamDokumentasi'])
        ->name('dokumentasi.stream')
        ->middleware('auth:resepsionis');
});

Route::prefix('resepsionis')->name('resepsionis.')->group(function () {
    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store'])->name('login')->middleware('throttle:login');

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email')->middleware('throttle:submissions');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update')->middleware('throttle:submissions');

    // Account setup routes (for new receptionists)
    Route::get('/account/create/{token}', [ResepsionisAccountController::class, 'create'])->name('account.create');
    Route::post('/account/create/{token}', [ResepsionisAccountController::class, 'store'])->name('account.store')->middleware('throttle:submissions');

    Route::middleware('auth:resepsionis')->group(function () {
        Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');

        Route::get('/dashboard', [ResepsionisController::class, 'dashboard'])->name('dashboard');
        Route::get('/kunjungan/data', [ResepsionisController::class, 'getKunjunganData'])->name('kunjungan.data');
        Route::get('/riwayat/data', [ResepsionisController::class, 'getRiwayatData'])->name('riwayat.data');
        Route::get('/karyawan/data', [ResepsionisController::class, 'getKaryawanData'])->name('karyawan.data');
        Route::get('/kunjungan/create', [ResepsionisController::class, 'createKunjungan'])->name('kunjungan.create');
        Route::post('/kunjungan/{id}/accept', [ResepsionisController::class, 'acceptKunjungan'])->name('kunjungan.accept')->middleware('throttle:submissions');
        Route::post('/kunjungan/{id}/reject', [ResepsionisController::class, 'rejectKunjungan'])->name('kunjungan.reject')->middleware('throttle:submissions');
        Route::get('/ktp/{token}/stream', [ResepsionisController::class, 'streamKtp'])->name('ktp.stream');
        Route::get('/riwayat', [ResepsionisController::class, 'riwayat'])->name('riwayat');
        Route::get('/karyawan', [ResepsionisController::class, 'daftarKaryawan'])->name('karyawan');
        Route::get('/karyawan/create', [App\Http\Controllers\KaryawanController::class, 'createKaryawan'])->name('karyawan.create');
        Route::post('/karyawan/store', [App\Http\Controllers\KaryawanController::class, 'store'])->name('karyawan.store')->middleware('throttle:submissions');
        Route::patch('/karyawan/{id}/toggle-status', [App\Http\Controllers\KaryawanController::class, 'toggleStatus'])->name('karyawan.toggle-status')->middleware('throttle:submissions');
        Route::get('/karyawan/search-departemen', [App\Http\Controllers\KaryawanController::class, 'searchDepartemen'])->name('karyawan.search-departemen')->middleware('throttle:api');
        Route::get('/karyawan/search-jabatan', [App\Http\Controllers\KaryawanController::class, 'searchJabatan'])->name('karyawan.search-jabatan')->middleware('throttle:api');
        Route::get('/notulensi/{kunjunganId}/token', [ResepsionisController::class, 'getNotulensiToken'])->name('notulensi.token');
    });
});

// Commented out - using custom SessionController instead
// require __DIR__ . '/auth.php';
