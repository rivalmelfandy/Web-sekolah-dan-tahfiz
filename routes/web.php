<?php

use App\Http\Controllers\PublicController;
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
