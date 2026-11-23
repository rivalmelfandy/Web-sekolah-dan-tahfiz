@extends('layouts.admin', ['title' => 'Laporan Tahfiz'])

@section('breadcrumb', 'Laporan Tahfiz')

@section('styles')
<style>
    .tbl-laporan thead { background: #D1FAE5; }
    .tbl-laporan thead th {
        color: #065F46; letter-spacing: .045em;
        font-size: .72rem; font-weight: 700;
    }
    .tbl-laporan tbody tr:hover td { background: #F0FDF4; }

    .filter-bar {
        background: #F0FDF4;
        border: 1px solid #A7F3D0;
        border-radius: 12px;
        padding: .9rem 1.1rem;
    }
    .form-select:focus { border-color: #047857; box-shadow: 0 0 0 3px rgba(4,120,87,.12); }

    .btn-detail {
        background: #047857; border: none; color: #fff;
        border-radius: 7px; padding: .28rem .75rem;
        font-size: .78rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: .35rem;
        transition: background .15s;
        text-decoration: none;
    }
    .btn-detail:hover { background: #065F46; color: #fff; }
</style>
@endsection

@section('content')

{{-- ── Header ──────────────────────────────────────────────────────── --}}
<div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1 h4" style="color:#000000;">
            <i class="bi bi-clipboard2-data me-2"></i>Laporan Tahfiz
        </h2>
        <p class="text-muted mb-0 small">
            Daftar siswa beserta ustadz pembimbing dan halaqah masing-masing.
        </p>
    </div>
</div>

@include('admin._alert')

{{-- ── FILTER DROPDOWN HALAQAH ─────────────────────────────────────── --}}
@php $filterAktif = $halaqahIdFilter ?? request('halaqah_id'); @endphp

<div class="filter-bar mb-4 d-flex flex-wrap align-items-center gap-3">
    <i class="bi bi-funnel-fill" style="color:#047857;"></i>
    <span class="fw-semibold small" style="color:#065F46;">Filter Halaqah:</span>

    <form method="GET" action="{{ route('admin.laporan-tahfiz.index') }}"
          class="d-flex align-items-center gap-2 flex-wrap mb-0">
        <select name="halaqah_id"
                class="form-select form-select-sm"
                style="min-width:200px; max-width:280px;"
                onchange="this.form.submit()">
            <option value="">— Semua Halaqah —</option>
            @foreach($halaqahOptions as $h)
                <option value="{{ $h->id }}" {{ $filterAktif == $h->id ? 'selected' : '' }}>
                    [{{ $h->kode_halaqah }}] {{ $h->nama_halaqah }}
                </option>
            @endforeach
        </select>
    </form>

    @if($filterAktif)
        @php $aktif = $halaqahOptions->firstWhere('id', $filterAktif); @endphp
        <span class="badge rounded-pill fw-semibold px-3 py-1 small"
              style="background:#D1FAE5; color:#065F46; border:1px solid #6EE7B7;">
            <i class="bi bi-check-circle me-1"></i>{{ $aktif?->nama_halaqah ?? 'Halaqah #'.$filterAktif }}
        </span>
        <a href="{{ route('admin.laporan-tahfiz.index') }}"
           class="btn btn-sm fw-semibold"
           style="background:transparent; border:1px solid #DC2626; color:#DC2626; border-radius:8px; font-size:.78rem;">
            <i class="bi bi-x-circle me-1"></i>Reset Filter
        </a>
    @endif
</div>

{{-- ── TABEL SISWA ──────────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 tbl-laporan">
            <thead>
                <tr>
                    <th class="px-4 py-3 text-uppercase" style="color:#000000;">No</th>
                    <th class="px-3 py-3 text-uppercase" style="color:#000000;">NIS</th>
                    <th class="px-3 py-3 text-uppercase" style="color:#000000;">Nama Siswa</th>
                    <th class="px-3 py-3 text-uppercase" style="color:#000000;">Ustadz Pembimbing</th>
                    <th class="px-3 py-3 text-uppercase" style="color:#000000;">Nama Halaqah</th>
                    <th class="px-4 py-3 text-uppercase text-center" style="color:#000000;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswaList as $i => $siswa)
                <tr>
                    <td class="px-4 py-3 text-muted small">{{ $i + 1 }}</td>

                    <td class="px-3 py-3 small" style="color:#111827; font-weight:600;">
                        {{ $siswa->nip_nis }}
                    </td>

                    <td class="px-3 py-3">
                        <div class="fw-semibold small" style="color:#000000;">
                            {{ $siswa->nama_lengkap }}
                        </div>
                    </td>

                    <td class="px-3 py-3 text-muted small">
                        {{ $siswa->halaqah?->ustadz?->nama_lengkap ?? '–' }}
                    </td>

                    <td class="px-3 py-3 text-muted small">
                        {{ $siswa->halaqah?->nama_halaqah ?? '–' }}
                    </td>

                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('admin.laporan-tahfiz.show', $siswa->id) }}"
                           class="btn-detail">
                            <i class="bi bi-eye"></i>Lihat Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-5 text-center text-muted small">
                        <i class="bi bi-people display-6 d-block mb-2 opacity-25"></i>
                        Belum ada siswa terdaftar{{ $filterAktif ? ' di halaqah ini' : '' }}.
                        @if($filterAktif)
                            <a href="{{ route('admin.laporan-tahfiz.index') }}"
                               class="d-block mt-1" style="color:#047857;">
                                Lihat semua siswa
                            </a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
