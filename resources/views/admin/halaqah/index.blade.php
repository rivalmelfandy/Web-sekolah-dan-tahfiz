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
        background: #DBEAFE; color: #1D4ED8;
        border: 1px solid #93C5FD;
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
    .btn-aksi {
        width: 34px; height: 34px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 8px; border-width: 1.5px; border-style: solid;
        background: none; cursor: pointer;
        transition: background .15s, opacity .15s;
        padding: 0;
    }
    .btn-aksi:hover { opacity: .82; }
    .btn-aksi-read  { background: #EFF6FF; border-color: #BFDBFE; color: #2563EB; }
    .btn-aksi-edit  { background: #FFFBEB; border-color: #FDE68A; color: #D97706; }
    .btn-aksi-hapus { background: #FEF2F2; border-color: #FCA5A5; color: #DC2626; }

    /* ── Modal anggota ───────────────────────────────────────────── */
    .modal-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,.45);
        z-index: 1055; display: flex; align-items: center; justify-content: center;
        padding: 1rem;
    }
    .modal-box {
        background: #fff; border-radius: 16px;
        width: 100%; max-width: 680px; max-height: 86vh;
        overflow-y: auto; display: flex; flex-direction: column;
        box-shadow: 0 20px 60px rgba(0,0,0,.18);
    }
    .modal-box::-webkit-scrollbar { width: 4px; }
    .modal-box::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 99px; }
    .modal-header-hlq {
        background: #047857; border-radius: 16px 16px 0 0;
        padding: .9rem 1.25rem;
        display: flex; align-items: center; justify-content: space-between;
        flex-shrink: 0;
    }
    .modal-close-btn {
        background: none; border: none; color: rgba(255,255,255,.8);
        font-size: 1.1rem; cursor: pointer; border-radius: 6px;
        padding: .2rem .4rem; line-height: 1; transition: background .15s;
    }
    .modal-close-btn:hover { background: rgba(255,255,255,.18); color: #fff; }
    .tbl-modal thead { background: #D1FAE5; }
    .tbl-modal thead th { color: #065F46; font-size: .72rem; font-weight: 700; letter-spacing: .04em; }
</style>
@endsection

@section('content')

{{-- ── Header halaman ─────────────────────────────────────────────── --}}
<div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1 h4" style="color:#000000;">
            <i class="bi bi-collection me-2"></i>Kelompok Halaqah Belajar
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
                    <th class="px-4 py-3 text-uppercase" style="color:#000000;">Kode</th>
                    <th class="px-3 py-3 text-uppercase" style="color:#000000;">Nama Halaqah</th>
                    <th class="px-3 py-3 text-uppercase" style="color:#000000;">Ustadz Pembimbing</th>
                    <th class="px-3 py-3 text-uppercase" style="color:#000000;">Target Hafalan</th>
                    <th class="px-3 py-3 text-uppercase text-center" style="color:#000000;">Jumlah Siswa</th>
                    <th class="px-3 py-3 text-uppercase text-center" style="color:#000000;">Status</th>
                    <th class="px-4 py-3 text-uppercase text-center" style="color:#000000;">Aksi</th>
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

                            {{-- Read: Kaca Pembesar --}}
                            <button type="button"
                                    class="btn-aksi btn-aksi-read"
                                    title="Lihat Anggota Halaqah"
                                    onclick="bukaModal({{ $h->id }})">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            </button>

                            {{-- Edit: Pensil --}}
                            <a href="{{ route('admin.halaqah.edit', $h) }}"
                               class="btn-aksi btn-aksi-edit"
                               title="Edit Halaqah">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>

                            {{-- Hapus: Tempat Sampah --}}
                            <form method="POST" action="{{ route('admin.halaqah.destroy', $h) }}"
                                  onsubmit="return confirm('Hapus halaqah {{ addslashes($h->nama_halaqah) }}?\nSiswa dalam halaqah ini akan dilepas (halaqah_id menjadi null).')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-aksi btn-aksi-hapus" title="Hapus Halaqah">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
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

{{-- ── DATA HALAQAH UNTUK MODAL (JSON) ───────────────────────────── --}}
@php
$halaqahData = $halaqahList->getCollection()->map(fn ($h) => [
    'id'            => $h->id,
    'kode'          => $h->kode_halaqah,
    'nama'          => $h->nama_halaqah,
    'ustadz'        => $h->ustadz ? $h->ustadz->nama_lengkap : '–',
    'siswa'         => $h->siswa->map(fn ($s) => [
        'nama'   => $s->nama_lengkap,
        'nip_nis'=> $s->nip_nis,
    ])->values()->all(),
])->keyBy('id');
@endphp

{{-- ── MODAL DETAIL ANGGOTA ─────────────────────────────────────── --}}
<div class="modal-overlay" id="modalAnggota" style="display:none;" onclick="tutupModalLuar(event)">
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalAnggotaJudul">

        {{-- Header --}}
        <div class="modal-header-hlq">
            <div>
                <div class="text-white fw-bold" id="modalAnggotaJudul" style="font-size:.92rem;">
                    Daftar Anggota Halaqah
                </div>
                <div class="text-white-50 small" id="modalAnggotaSubjudul"></div>
            </div>
            <button type="button" class="modal-close-btn" onclick="tutupModal()" aria-label="Tutup">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-4">
            <div class="table-responsive">
                <table class="table align-middle mb-0 tbl-modal" id="tblAnggota">
                    <thead>
                        <tr>
                            <th class="px-3 py-2 text-uppercase" style="width:6%;">No</th>
                            <th class="px-3 py-2 text-uppercase">Nama Siswa</th>
                            <th class="px-3 py-2 text-uppercase">NIS</th>
                            <th class="px-3 py-2 text-uppercase">Nama Halaqah</th>
                            <th class="px-3 py-2 text-uppercase">Ustadz Pembimbing</th>
                        </tr>
                    </thead>
                    <tbody id="bodyAnggota"></tbody>
                </table>
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-4 pb-4 d-flex justify-content-end">
            <button type="button" onclick="tutupModal()"
                    class="btn btn-sm fw-semibold"
                    style="background:#E2E8F0; border:none; color:#000; border-radius:8px; padding:.4rem 1rem;">
                Tutup
            </button>
        </div>

    </div>
</div>

@endsection

@section('scripts')
<script>
var halaqahData = @json($halaqahData);

function bukaModal(id) {
    var h    = halaqahData[id];
    var body = document.getElementById('bodyAnggota');

    document.getElementById('modalAnggotaSubjudul').textContent =
        '[' + h.kode + '] ' + h.nama;

    body.innerHTML = '';

    if (h.siswa.length === 0) {
        body.innerHTML =
            '<tr><td colspan="5" class="px-3 py-4 text-center text-muted small fst-italic">' +
            'Belum ada siswa yang tergabung dalam halaqah ini.</td></tr>';
    } else {
        h.siswa.forEach(function (s, i) {
            var tr = document.createElement('tr');
            tr.innerHTML =
                '<td class="px-3 py-2 text-muted small">' + (i + 1) + '</td>' +
                '<td class="px-3 py-2 small fw-semibold">' + escHtml(s.nama) + '</td>' +
                '<td class="px-3 py-2 text-muted small">' + escHtml(s.nip_nis) + '</td>' +
                '<td class="px-3 py-2 text-muted small">' + escHtml(h.nama) + '</td>' +
                '<td class="px-3 py-2 text-muted small">' + escHtml(h.ustadz) + '</td>';
            body.appendChild(tr);
        });
    }

    document.getElementById('modalAnggota').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function tutupModal() {
    document.getElementById('modalAnggota').style.display = 'none';
    document.body.style.overflow = '';
}

function tutupModalLuar(e) {
    if (e.target.id === 'modalAnggota') tutupModal();
}

function escHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}
</script>
@endsection
