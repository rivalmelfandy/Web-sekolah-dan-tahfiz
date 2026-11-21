@extends('layouts.admin', ['title' => 'Kelompok Halaqah'])

@section('breadcrumb', 'Kelompok Halaqah')

@section('styles')
<style>
    /* ── Header tabel hijau mint ─────────────────────────────────── */
    .tbl-hlq thead { background: #D1FAE5; }
    .tbl-hlq thead th { color: #065F46; letter-spacing: .045em; font-size: .72rem; font-weight: 700; }

    /* ── Baris data: kode & nama bold dengan latar mint lembut ───── */
    .tbl-hlq tbody tr:hover td { background: #F0FDF4; }
    .td-kode  { background: #ECFDF5; font-weight: 700; color: #065F46; font-size: .82rem; white-space: nowrap; }
    .td-nama  { background: #ECFDF5; font-weight: 700; color: #065F46; font-size: .84rem; }

    /* ── Badge jumlah siswa ──────────────────────────────────────── */
    .badge-siswa {
        background: #D1FAE5; color: #065F46;
        border: 1px solid #A7F3D0;
        font-size: .72rem; font-weight: 700;
        border-radius: 99px; padding: .2em .75em;
        display: inline-flex; align-items: center; gap: .3rem;
        white-space: nowrap;
    }

    /* ── Badge status Aktif / Kosong ─────────────────────────────── */
    .badge-aktif {
        background: #D1FAE5; color: #047857;
        border: 1px solid #6EE7B7;
        font-size: .7rem; font-weight: 600;
        border-radius: 99px; padding: .2em .65em;
    }
    .badge-kosong {
        background: #F1F5F9; color: #64748B;
        border: 1px solid #CBD5E1;
        font-size: .7rem; font-weight: 600;
        border-radius: 99px; padding: .2em .65em;
    }

    /* ── Tombol aksi ─────────────────────────────────────────────── */
    .btn-aksi-edit {
        border: 1.5px solid #047857; color: #047857; background: transparent;
        border-radius: 7px; padding: .28rem .55rem; font-size: .78rem; line-height: 1;
        transition: background .15s, color .15s;
    }
    .btn-aksi-edit:hover { background: #047857; color: #fff; }

    .btn-aksi-hapus {
        border: 1.5px solid #E11D48; color: #E11D48; background: transparent;
        border-radius: 7px; padding: .28rem .55rem; font-size: .78rem; line-height: 1;
        transition: background .15s, color .15s;
    }
    .btn-aksi-hapus:hover { background: #E11D48; color: #fff; }
</style>
@endsection

@section('content')

{{-- ── Header halaman ─────────────────────────────────────────────── --}}
<div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1 h4" style="color:#065F46;">
            Kelompok Halaqah Belajar
        </h2>
        <p class="text-muted mb-0 small">Pembagian siswa dan asatidz pembimbing penanggung jawab mutabaah.</p>
    </div>
    <a href="{{ route('admin.halaqah.create') }}"
       class="btn fw-semibold rounded-pill px-4 py-2"
       style="background:#047857; border:none; color:#fff; font-size:.85rem;">
        <i class="bi bi-plus-lg me-1"></i>Tambah Halaqah
    </a>
</div>

@include('admin._alert')

{{-- ── Tabel ────────────────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 tbl-hlq">
            <thead>
                <tr>
                    <th class="px-4 py-3 text-uppercase">Kode</th>
                    <th class="px-3 py-3 text-uppercase">Nama Halaqah</th>
                    <th class="px-3 py-3 text-uppercase">Ustadz Pembimbing</th>
                    <th class="px-3 py-3 text-uppercase">Target Hafalan</th>
                    <th class="px-3 py-3 text-uppercase text-center">Jumlah Siswa</th>
                    <th class="px-3 py-3 text-uppercase text-center">Status</th>
                    <th class="px-4 py-3 text-uppercase text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($halaqahList as $h)
                <tr>
                    {{-- Kode: bold, latar mint --}}
                    <td class="px-4 py-3 td-kode">{{ $h->kode_halaqah }}</td>

                    {{-- Nama Halaqah: bold, latar mint --}}
                    <td class="px-3 py-3 td-nama">{{ $h->nama_halaqah }}</td>

                    {{-- Ustadz Pembimbing --}}
                    <td class="px-3 py-3 text-muted small">
                        {{ $h->ustadz ? 'Ust. '.$h->ustadz->nama_lengkap : '–' }}
                    </td>

                    {{-- Target Juz --}}
                    <td class="px-3 py-3 text-muted small">{{ $h->target_juz }}</td>

                    {{-- Jumlah Siswa --}}
                    <td class="px-3 py-3 text-center">
                        <span class="badge-siswa">
                            <i class="bi bi-people-fill" style="font-size:.65rem;"></i>
                            {{ $h->siswa_count }} Siswa
                        </span>
                    </td>

                    {{-- Status: Aktif jika ada ≥1 siswa, Kosong jika belum --}}
                    <td class="px-3 py-3 text-center">
                        @if($h->siswa_count > 0)
                            <span class="badge-aktif">Aktif</span>
                        @else
                            <span class="badge-kosong">Kosong</span>
                        @endif
                    </td>

                    {{-- Aksi --}}
                    <td class="px-4 py-3 text-center">
                        <div class="d-flex justify-content-center align-items-center gap-1">
                            <a href="{{ route('admin.halaqah.edit', $h) }}"
                               class="btn-aksi-edit" title="Edit">
                                <i class="bi bi-pencil-fill"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('admin.halaqah.destroy', $h) }}"
                                  onsubmit="return confirm('Hapus halaqah {{ addslashes($h->nama_halaqah) }}?\nSiswa dalam halaqah ini akan dilepas (halaqah_id menjadi null).')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-aksi-hapus" title="Hapus">
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
                        <a href="{{ route('admin.halaqah.create') }}" class="d-block mt-1" style="color:#047857;">
                            Tambahkan halaqah pertama
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Paginasi --}}
@if($halaqahList->hasPages())
<div class="mt-3 d-flex justify-content-end">
    {{ $halaqahList->links() }}
</div>
@endif

@endsection
