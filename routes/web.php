<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\HalaqahController as AdminHalaqahController;
use App\Http\Controllers\Admin\LaporanTahfizController as AdminLaporanTahfizController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\RekapTahfidzController as AdminRekapTahfidzController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\ProfileController as SiswaProfileController;
use App\Http\Controllers\Ustadz\DashboardController as UstadzDashboardController;
use App\Http\Controllers\Ustadz\ProfileController as UstadzProfileController;
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

    Route::middleware(['role:ustadz'])->prefix('ustadz')->name('ustadz.')->group(function () {
        Route::get('profil', [UstadzProfileController::class, 'index'])
            ->name('profil');
        Route::put('profil', [UstadzProfileController::class, 'updateProfile'])
            ->name('profil.update');
        Route::put('profil/password', [UstadzProfileController::class, 'updatePassword'])
            ->name('profil.password');
    });

    Route::get('/siswa', [SiswaDashboardController::class, 'index'])
        ->name('siswa.dashboard')
        ->middleware('role:siswa');

    Route::middleware(['role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
        Route::get('profil', [SiswaProfileController::class, 'index'])
            ->name('profil');
        Route::put('profil', [SiswaProfileController::class, 'updateProfile'])
            ->name('profil.update');
        Route::put('profil/password', [SiswaProfileController::class, 'updatePassword'])
            ->name('profil.password');
    });

    /*
    |--------------------------------------------------------------------------
    | Tahap 3 — CRUD Admin: Pengguna & Halaqah
    |
    */

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', AdminUserController::class)->except(['show']);
        Route::resource('halaqah', AdminHalaqahController::class)->except(['show']);
        Route::get('rekap-tahfidz', [AdminRekapTahfidzController::class, 'index'])
            ->name('rekap-tahfidz');
        Route::get('laporan-tahfiz', [AdminLaporanTahfizController::class, 'index'])
            ->name('laporan-tahfiz.index');
        Route::get('laporan-tahfiz/{siswa}', [AdminLaporanTahfizController::class, 'show'])
            ->name('laporan-tahfiz.show');
        Route::get('profil', [AdminProfileController::class, 'index'])
            ->name('profil');
        Route::put('profil', [AdminProfileController::class, 'updateProfile'])
            ->name('profil.update');
        Route::put('profil/password', [AdminProfileController::class, 'updatePassword'])
            ->name('profil.password');
    });
});
