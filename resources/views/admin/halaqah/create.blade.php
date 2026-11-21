@extends('layouts.admin', ['title' => 'Tambah Halaqah'])

@section('breadcrumb', 'Tambah Halaqah')

@section('styles')
<style>
    .form-label { font-size: .82rem; font-weight: 600; color: #374151; }
    .form-control:focus, .form-select:focus {
        border-color: #047857;
        box-shadow: 0 0 0 3px rgba(4,120,87,.12);
    }
    .siswa-item:hover { background: #F0FDF4; }
    .form-check-input:checked { background-color: #047857; border-color: #047857; }
    .btn-simpan-hlq {
        background: #047857; border: none; color: #fff;
        border-radius: 8px; padding: .5rem 1.25rem;
        font-size: .85rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: .4rem;
        transition: background .15s;
    }
    .btn-simpan-hlq:hover  { background: #065F46; color: #fff; }
    .btn-simpan-hlq:active { background: #064E3B; color: #fff; }
</style>
@endsection

@section('content')

{{-- ── Breadcrumb / Header ──────────────────────────────────────────── --}}
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
    <div>
        <h2 class="fw-bold mb-0 h4" style="color:#1F4172;">
            <i class="bi bi-plus-circle me-2"></i>Tambah Halaqah
        </h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.halaqah.index') }}" class="text-decoration-none">Kelompok Halaqah</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
    </div>
</div>

@include('admin._alert')

{{-- ── Error validasi ───────────────────────────────────────────────── --}}
@if($errors->any())
<div class="alert alert-danger d-flex align-items-start gap-2 py-2 mb-3" role="alert">
    <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-1"></i>
    <div class="small">
        <div class="fw-semibold mb-1">Mohon perbaiki kesalahan berikut:</div>
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
</div>
@endif

{{-- ── FORM (single form, tombol di luar card agar submit keduanya) ─── --}}
<form method="POST" action="{{ route('admin.halaqah.store') }}" id="formCreateHalaqah">
@csrf

<div class="row g-4 align-items-start">

    {{-- ── Kolom kiri: Detail halaqah ──────────────────────────────── --}}
    <div class="col-12 col-xl-5">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                <h3 class="fw-bold mb-0 h6" style="color:#047857;">
                    <i class="bi bi-collection me-1"></i>Detail Kelompok
                </h3>
            </div>
            <div class="card-body px-4 pb-4 pt-3">

                {{-- Kode Halaqah --}}
                <div class="mb-3">
                    <label for="kode_halaqah" class="form-label">Kode Halaqah <span class="text-danger">*</span></label>
                    <input type="text" id="kode_halaqah" name="kode_halaqah"
                           class="form-control @error('kode_halaqah') is-invalid @enderror"
                           value="{{ old('kode_halaqah') }}" maxlength="20"
                           placeholder="Contoh: HLQ-01" required>
                    @error('kode_halaqah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Nama Halaqah --}}
                <div class="mb-3">
                    <label for="nama_halaqah" class="form-label">Nama Halaqah <span class="text-danger">*</span></label>
                    <input type="text" id="nama_halaqah" name="nama_halaqah"
                           class="form-control @error('nama_halaqah') is-invalid @enderror"
                           value="{{ old('nama_halaqah') }}" maxlength="50"
                           placeholder="Contoh: Halaqah Al-Fatihah" required>
                    @error('nama_halaqah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Ustadz Pembimbing --}}
                <div class="mb-3">
                    <label for="ustadz_id" class="form-label">Ustadz Pembimbing</label>
                    <select id="ustadz_id" name="ustadz_id"
                            class="form-select @error('ustadz_id') is-invalid @enderror">
                        <option value="">— Belum Ditentukan —</option>
                        @foreach($ustadzList as $u)
                            <option value="{{ $u->id }}" {{ old('ustadz_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->nama_lengkap }} ({{ $u->nip_nis }})
                            </option>
                        @endforeach
                    </select>
                    @error('ustadz_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    @if($ustadzList->isEmpty())
                        <div class="form-text text-warning">
                            <i class="bi bi-exclamation-triangle me-1"></i>Belum ada ustadz. Tambahkan ustadz terlebih dahulu.
                        </div>
                    @endif
                </div>

                {{-- Target Juz --}}
                <div class="mb-0">
                    <label for="target_juz" class="form-label">Target Juz</label>
                    <input type="text" id="target_juz" name="target_juz"
                           class="form-control @error('target_juz') is-invalid @enderror"
                           value="{{ old('target_juz', 'Juz 29 - 30') }}" maxlength="50"
                           placeholder="Contoh: Juz 29 - 30">
                    @error('target_juz')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">Default: "Juz 29 - 30" jika dikosongkan.</div>
                </div>

            </div>
        </div>
    </div>

    {{-- ── Kolom kanan: Panel Anggota Siswa ────────────────────────── --}}
    <div class="col-12 col-xl-7">
        @include('admin.halaqah._panel_siswa', [
            'siswaList'         => $siswaList,
            'checkedIds'        => [],
            'currentHalaqahId'  => null,
        ])
    </div>

</div>

{{-- ── Tombol aksi ──────────────────────────────────────────────────── --}}
<div class="d-flex justify-content-end align-items-center gap-3 mt-4">
    <a href="{{ route('admin.halaqah.index') }}"
       class="btn btn-link text-secondary text-decoration-none fw-semibold">
        Batal
    </a>
    <button type="submit" class="btn-simpan-hlq">
        <i class="bi bi-check-circle-fill"></i>Simpan Halaqah
    </button>
</div>

</form>

@endsection

@section('scripts')
<script>
function updateBadge() {
    const count = document.querySelectorAll('.siswa-checkbox:checked').length;
    document.getElementById('badgeSiswa').textContent = count + ' siswa dipilih';
}

function filterSiswa(q) {
    const kata = q.toLowerCase().trim();
    document.querySelectorAll('.siswa-item').forEach(function (el) {
        const cocok = !kata
            || el.dataset.nama.includes(kata)
            || el.dataset.nis.includes(kata);
        el.style.display = cocok ? '' : 'none';
    });
}

// Hitung badge saat halaman dimuat.
document.addEventListener('DOMContentLoaded', updateBadge);
</script>
@endsection
