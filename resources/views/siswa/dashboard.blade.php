@extends('layouts.dashboard', ['title' => 'Dashboard Siswa'])

@section('content')

<div class="rounded-4 p-4 p-md-5 text-white shadow-sm" style="background: linear-gradient(135deg, #1F4172 0%, #2563EB 100%);">
    <div class="d-flex align-items-center gap-3 mb-2">
        <span class="rounded-3 d-flex align-items-center justify-content-center bg-white bg-opacity-25" style="width:56px; height:56px;">
            <i class="bi bi-book-half fs-3"></i>
        </span>
        <div>
            <h1 class="fw-bold mb-0 h3">Selamat datang, {{ $user->nama_lengkap }}!</h1>
            <p class="mb-0 opacity-75 small">Dashboard Siswa – SMA IT Al-Fadhilah Pekanbaru</p>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mt-3">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-2" style="color:#1F4172;"><i class="bi bi-person me-2"></i>Peran: Siswa</h5>
        <p class="text-muted small mb-0">
            Anda masuk sebagai <strong>siswa</strong>. Riwayat setoran hafalan, murajaah,
            dan target hafalan pribadi akan tersedia pada tahap berikutnya.
        </p>
    </div>
</div>

@endsection
