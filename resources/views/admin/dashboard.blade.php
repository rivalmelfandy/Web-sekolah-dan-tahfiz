@extends('layouts.dashboard', ['title' => 'Dashboard Admin'])

@section('content')

<div class="rounded-4 p-4 p-md-5 text-white shadow-sm" style="background: linear-gradient(135deg, #1F4172 0%, #2563EB 100%);">
    <div class="d-flex align-items-center gap-3 mb-2">
        <span class="rounded-3 d-flex align-items-center justify-content-center bg-white bg-opacity-25" style="width:56px; height:56px;">
            <i class="bi bi-person-gear fs-3"></i>
        </span>
        <div>
            <h1 class="fw-bold mb-0 h3">Selamat datang, {{ $user->nama_lengkap }}!</h1>
            <p class="mb-0 opacity-75 small">Dashboard Administrator – SMA IT Al-Fadhilah Pekanbaru</p>
        </div>
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-2" style="color:#1F4172;"><i class="bi bi-shield-check me-2"></i>Peran: Administrator</h5>
                <p class="text-muted small mb-0">
                    Anda masuk sebagai <strong>admin</strong>. Pengelolaan data asli
                    (pengguna, halaqah, setoran, murajaah, dan target) akan tersedia pada tahap berikutnya.
                </p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-2" style="color:#1F4172;"><i class="bi bi-clipboard-data me-2"></i>Ringkasan Sistem</h5>
                <p class="text-muted small mb-0">
                    Tahap 2 telah membangun fondasi: 5 tabel sesuai skema DBML, model Eloquent
                    lengkap, serta autentikasi &amp; middleware peran.
                </p>
            </div>
        </div>
    </div>
</div>

@endsection
