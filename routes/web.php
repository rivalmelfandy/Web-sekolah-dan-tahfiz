<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Ustadz\DashboardController as UstadzDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Tahap 1 — Halaman Publik (Website Profil Sekolah)
|
*/

Route::get('/', [PublicController::class, 'beranda'])->name('public.beranda');
Route::get('/akademik', [PublicController::class, 'akademik'])->name('public.akademik');
Route::get('/spmb', [PublicController::class, 'spmb'])->name('public.spmb');

/*
|--------------------------------------------------------------------------
| Tahap 2 — Autentikasi (login asli dengan nip_nis + password)
|
*/

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])
    ->name('login.attempt')
    ->middleware('throttle:login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Tahap 2 — Route dasar per peran (dilindungi middleware auth + role)
|
*/

Route::middleware('auth')->group(function () {
    Route::get('/admin', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard')
        ->middleware('role:admin');

    Route::get('/ustadz', [UstadzDashboardController::class, 'index'])
        ->name('ustadz.dashboard')
        ->middleware('role:ustadz');

    Route::get('/siswa', [SiswaDashboardController::class, 'index'])
        ->name('siswa.dashboard')
        ->middleware('role:siswa');
});
