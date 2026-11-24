@extends('layouts.siswa', ['title' => 'Dashboard Siswa'])

@section('breadcrumb', 'Dashboard')

@section('content')

{{-- ── Banner Sambutan ─────────────────────────────────────────────── --}}
<div class="rounded-4 p-4 p-md-5 text-white shadow-sm mb-3"
     style="background: linear-gradient(135deg, #0D6B65 0%, #117A74 60%, #138C85 100%);">
    <div class="d-flex align-items-center gap-3">
        <span class="rounded-3 d-flex align-items-center justify-content-center bg-white bg-opacity-25"
              style="width:56px; height:56px;">
            <i class="bi bi-book-half fs-3"></i>
        </span>
        <div>
            <h1 class="fw-bold mb-0 h3">Selamat datang, {{ $user->nama_lengkap }}!</h1>
            <p class="mb-0 opacity-75 small">Dashboard Siswa – SMA IT Al-Fadhilah Pekanbaru</p>
        </div>
    </div>
</div>

{{-- ── Kartu Informasi Peran ───────────────────────────────────────── --}}
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-2" style="color:#1F4172;">
            <i class="bi bi-person me-2"></i>Peran: Siswa
        </h5>
        <p class="text-muted small mb-0">
            Anda masuk sebagai <strong>siswa</strong>. Riwayat setoran hafalan, murajaah,
            dan target hafalan pribadi akan tersedia pada tahap berikutnya.
        </p>
    </div>
</div>

@endsection
