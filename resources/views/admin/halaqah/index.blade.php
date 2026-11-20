@extends('layouts.admin', ['title' => 'Kelola Halaqah'])

@section('breadcrumb', 'Kelompok Halaqah')

@section('content')

{{-- Header --}}
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <div>
        <h2 class="fw-bold mb-0 h4" style="color:#1F4172;">
            <i class="bi bi-collection me-2"></i>Kelola Halaqah
        </h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active">Halaqah</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('admin.halaqah.create') }}" class="btn btn-primary btn-sm fw-semibold">
        <i class="bi bi-plus-lg me-1"></i>Tambah Halaqah
    </a>
</div>

@include('admin._alert')

{{-- Tabel --}}
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background:#F1F5F9;">
                <tr>
                    <th class="px-4 py-3 small fw-semibold text-uppercase" style="color:#64748B; letter-spacing:.04em;">#</th>
                    <th class="px-3 py-3 small fw-semibold text-uppercase" style="color:#64748B; letter-spacing:.04em;">Kode</th>
                    <th class="px-3 py-3 small fw-semibold text-uppercase" style="color:#64748B; letter-spacing:.04em;">Nama Halaqah</th>
                    <th class="px-3 py-3 small fw-semibold text-uppercase" style="color:#64748B; letter-spacing:.04em;">Ustadz Pembimbing</th>
                    <th class="px-3 py-3 small fw-semibold text-uppercase" style="color:#64748B; letter-spacing:.04em;">Target Juz</th>
                    <th class="px-3 py-3 small fw-semibold text-uppercase text-center" style="color:#64748B; letter-spacing:.04em;">Siswa</th>
                    <th class="px-4 py-3 small fw-semibold text-uppercase text-center" style="color:#64748B; letter-spacing:.04em;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($halaqahList as $h)
                <tr>
                    <td class="px-4 py-3 text-muted small">{{ $halaqahList->firstItem() + $loop->index }}</td>
                    <td class="px-3 py-3">
                        <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold px-2 py-1 rounded-2 small">
                            {{ $h->kode_halaqah }}
                        </span>
                    </td>
                    <td class="px-3 py-3 fw-semibold small" style="color:#1F4172;">{{ $h->nama_halaqah }}</td>
                    <td class="px-3 py-3 text-muted small">
                        {{ $h->ustadz?->nama_lengkap ?? '–' }}
                    </td>
                    <td class="px-3 py-3 text-muted small">{{ $h->target_juz }}</td>
                    <td class="px-3 py-3 text-center">
                        <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-1 rounded-pill">
                            {{ $h->siswa_count }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('admin.halaqah.edit', $h) }}"
                               class="btn btn-sm btn-outline-primary py-1 px-2" title="Edit">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.halaqah.destroy', $h) }}"
                                  onsubmit="return confirm('Hapus halaqah {{ addslashes($h->nama_halaqah) }}? Siswa dalam halaqah ini akan dilepas (halaqah_id menjadi null).')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" title="Hapus">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-5 text-center text-muted small">
                        <i class="bi bi-collection display-6 d-block mb-2 opacity-25"></i>
                        Belum ada halaqah terdaftar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($halaqahList->hasPages())
<div class="mt-3 d-flex justify-content-end">
    {{ $halaqahList->links() }}
</div>
@endif

@endsection
