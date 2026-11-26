@extends('layouts.ustadz', ['title' => 'Evaluasi Murajaah'])

@section('breadcrumb', 'Murajaah')

@section('content')

{{-- ── Notifikasi Alert ─────────────────────────────────────────────── --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible d-flex align-items-center gap-2 py-2 mb-4" role="alert">
        <i class="bi bi-check-circle-fill flex-shrink-0"></i>
        <span class="small">{{ session('success') }}</span>
        <button type="button" class="btn-close btn-sm ms-auto" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger d-flex align-items-start gap-2 py-2 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-1"></i>
        <div class="small">
            <div class="fw-semibold mb-1">Gagal menyimpan evaluasi murajaah:</div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    </div>
@endif

{{-- ── Header Banner ────────────────────────────────────────────────── --}}
<div class="mb-4">
    <span class="badge px-3 py-1 text-white mb-2 fw-bold" style="background-color: #117A74;">PENGUATAN HAFALAN</span>
    <h2 class="fw-bold mb-1" style="color: #1E293B; font-size: 1.65rem;">Evaluasi Murajaah Siswa</h2>
    <p class="text-muted small mb-0">Uji dan perkuat hafalan lama siswa binaan agar tetap mutqin dan terjaga kelancarannya.</p>
</div>

{{-- ── Layout 2 Kolom (Sesuai Referensi RPLBO) ───────────────────────── --}}
<div class="hafalan-layout">

    {{-- ══ PANEL KIRI: DAFTAR PILIH SISWA ══════════════════════════════ --}}
    <section class="hafalan-panel hafalan-panel-siswa" aria-label="Pilih siswa murajaah">
        <h2 class="hafalan-panel-title">
            <i class="bi bi-person-check-fill" style="color:#117A74;"></i>
            <span>Pilih Siswa ({{ $santriList->count() }})</span>
        </h2>
        <div class="hafalan-search-wrap">
            <i class="bi bi-search"></i>
            <input type="search" id="searchMurajaahSiswa" placeholder="Cari nama atau NIS..." autocomplete="off">
        </div>
        <div id="listMurajaahSiswa" class="d-flex flex-column gap-1 overflow-y-auto" style="max-height: 580px;">
            @forelse($santriList as $s)
                @php
                    $kata = explode(' ', trim($s->nama_lengkap));
                    $inisial = strtoupper(substr($kata[0], 0, 1));
                    if (isset($kata[1])) $inisial .= strtoupper(substr($kata[1], 0, 1));
                    $isAktif = $selectedSiswa && $selectedSiswa->id === $s->id;
                @endphp
                <a href="{{ route('ustadz.murajaah.index', ['siswa_id' => $s->id]) }}"
                   class="hafalan-siswa-card text-decoration-none {{ $isAktif ? 'aktif active' : '' }}"
                   data-nama="{{ strtolower($s->nama_lengkap) }}"
                   data-nis="{{ strtolower($s->nip_nis) }}">
                    <span class="hafalan-avatar" aria-hidden="true"
                          style="{{ $isAktif ? 'background:#117A74; color:#fff;' : '' }}">
                        {{ $inisial }}
                    </span>
                    <span class="hafalan-siswa-meta">
                        <span class="hafalan-siswa-nama text-truncate" style="color: #1E293B;">{{ $s->nama_lengkap }}</span>
                        <span class="hafalan-siswa-nis">NIS: {{ $s->nip_nis }}</span>
                    </span>
                    @if($isAktif)
                        <i class="bi bi-chevron-right ms-auto" style="color:#117A74; font-size:12px;"></i>
                    @endif
                </a>
            @empty
                <div class="text-center py-4 text-muted small">
                    Belum ada siswa dalam halaqah.
                </div>
            @endforelse
        </div>
    </section>

    {{-- ══ PANEL KANAN: TABEL RIWAYAT & FORM MURAJAAH ══════════════════ --}}
    <section class="hafalan-panel hafalan-panel-surah" aria-label="Detail murajaah siswa">
        @if(!$selectedSiswa)
            <div class="hafalan-empty">
                <i class="bi bi-arrow-repeat fs-1 text-muted d-block mb-3"></i>
                <p>Pilih siswa terlebih dahulu di panel sebelah kiri untuk mencatat evaluasi murajaah.</p>
            </div>
        @else
            <div>
                {{-- Header Siswa Terpilih --}}
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 pb-3 mb-3"
                     style="border-bottom: 1px solid #E2E8F0;">
                    <div>
                        <span class="badge px-2 py-1 rounded-pill mb-1" style="background:#E6F4F1; color:#117A74; font-size:11px;">
                            Siswa Terpilih
                        </span>
                        <h4 class="fw-bold mb-0 text-dark">{{ $selectedSiswa->nama_lengkap }}</h4>
                        <div class="text-muted small">NIS: {{ $selectedSiswa->nip_nis }} &bull; Halaqah: {{ $halaqah?->nama_halaqah ?? '–' }}</div>
                    </div>
                    <div>
                        <button type="button" class="btn text-white px-3 py-2 rounded-3 fw-semibold shadow-sm"
                                style="background:#117A74; font-size:13.5px;"
                                onclick="openModalMurajaahBebas()">
                            <i class="bi bi-plus-circle me-1"></i>Murajaah Mandiri
                        </button>
                    </div>
                </div>

                {{-- Tabel 1: Setoran Hafalan yang Dapat Dimurajaah --}}
                <div class="mb-4">
                    <h6 class="fw-bold text-dark mb-2" style="font-size:14px;">
                        <i class="bi bi-journal-text me-1 text-primary"></i>Setoran Hafalan yang Dapat Diulang / Diuji
                    </h6>
                    <div class="table-responsive" style="border:1px solid #E2E8F0; border-radius:12px; overflow:hidden;">
                        <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                            <thead style="background:#F8FAFC;">
                                <tr style="font-size:12px; color:#64748B; text-transform:uppercase; letter-spacing:0.03em;">
                                    <th class="ps-3 py-2.5 fw-semibold" style="width:40px;">No</th>
                                    <th class="py-2.5 fw-semibold" style="width:120px;">Tgl Setor</th>
                                    <th class="py-2.5 fw-semibold">Surah &amp; Ayat</th>
                                    <th class="py-2.5 fw-semibold" style="width:130px;">Kualitas Setor</th>
                                    <th class="py-2.5 pe-3 fw-semibold text-center" style="width:150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayatSetoran as $idx => $setor)
                                    <tr>
                                        <td class="ps-3 text-muted small">{{ $idx + 1 }}</td>
                                        <td class="text-nowrap small text-secondary fw-semibold">
                                            {{ $setor->tanggal_setor?->translatedFormat('d M Y') }}
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $setor->surah }}</div>
                                            <div class="text-muted small">Ayat {{ $setor->ayat_awal }} – {{ $setor->ayat_akhir }}</div>
                                        </td>
                                        <td>
                                            @if($setor->kualitas?->value === 'Lancar')
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                                                    Lancar
                                                </span>
                                            @elseif($setor->kualitas?->value === 'Kurang Lancar')
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 rounded-pill">
                                                    Kurang Lancar
                                                </span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill">
                                                    Tidak Lancar
                                                </span>
                                            @endif
                                        </td>
                                        <td class="pe-3 text-center">
                                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-semibold"
                                                    style="font-size:12px;"
                                                    onclick="openModalEvaluasiSetoran({{ $setor->id }}, '{{ addslashes($setor->surah) }}', 'Ayat {{ $setor->ayat_awal }}–{{ $setor->ayat_akhir }}')">
                                                <i class="bi bi-patch-check me-1"></i>Evaluasi
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted small">
                                            Belum ada setoran hafalan yang tercatat untuk siswa ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tabel 2: Riwayat Evaluasi Murajaah Sebelumnya --}}
                <div>
                    <h6 class="fw-bold text-dark mb-2" style="font-size:14px;">
                        <i class="bi bi-clock-history me-1 text-success"></i>Riwayat Evaluasi Murajaah Santri ({{ $riwayatMurajaah->count() }})
                    </h6>
                    <div class="table-responsive" style="border:1px solid #E2E8F0; border-radius:12px; overflow:hidden;">
                        <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                            <thead style="background:#F8FAFC;">
                                <tr style="font-size:12px; color:#64748B; text-transform:uppercase; letter-spacing:0.03em;">
                                    <th class="ps-3 py-2.5 fw-semibold" style="width:40px;">No</th>
                                    <th class="py-2.5 fw-semibold" style="width:120px;">Tgl Murajaah</th>
                                    <th class="py-2.5 fw-semibold">Surah &amp; Rentang</th>
                                    <th class="py-2.5 fw-semibold" style="width:130px;">Status Mutqin</th>
                                    <th class="py-2.5 pe-3 fw-semibold">Catatan Ustadz</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayatMurajaah as $idx => $mur)
                                    <tr>
                                        <td class="ps-3 text-muted small">{{ $idx + 1 }}</td>
                                        <td class="text-nowrap small text-secondary fw-semibold">
                                            {{ $mur->tanggal_murajaah?->translatedFormat('d M Y') }}
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $mur->surah }}</div>
                                            <div class="text-muted small">{{ $mur->ayat_range }}</div>
                                        </td>
                                        <td>
                                            @if($mur->status_mutqin?->value === 'Lancar')
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                                                    Mutqin / Lancar
                                                </span>
                                            @elseif($mur->status_mutqin?->value === 'Kurang Lancar')
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 rounded-pill">
                                                    Kurang Lancar
                                                </span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill">
                                                    Tidak Lancar
                                                </span>
                                            @endif
                                        </td>
                                        <td class="pe-3 text-muted small">
                                            {{ $mur->catatan ?: '–' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted small">
                                            Belum ada catatan evaluasi murajaah untuk siswa ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        @endif
    </section>

</div>

{{-- ══ MODAL EVALUASI MURAJAAH ════════════════════════════════════════ --}}
@if($selectedSiswa)
<div class="modal fade" id="modalEvaluasiMurajaah" tabindex="-1" aria-labelledby="modalEvaluasiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <form action="{{ route('ustadz.murajaah.store') }}" method="POST">
                @csrf
                <input type="hidden" name="siswa_id" value="{{ $selectedSiswa->id }}">
                <input type="hidden" name="setoran_id" id="modalSetoranId" value="">

                <div class="modal-header text-white" style="background:#117A74;">
                    <div>
                        <h6 class="modal-title fw-bold mb-0" id="modalEvaluasiLabel">
                            <i class="bi bi-patch-check me-1"></i>Form Evaluasi Murajaah
                        </h6>
                        <small class="opacity-75">Siswa: {{ $selectedSiswa->nama_lengkap }} ({{ $selectedSiswa->nip_nis }})</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body p-4">
                    {{-- Nama Surah --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Surah yang Diulang <span class="text-danger">*</span></label>
                        <input type="text" name="surah" id="modalSurah" class="form-control form-control-sm"
                               placeholder="Contoh: An-Naba', Al-Mulk" required style="border-radius:8px;">
                    </div>

                    {{-- Rentang Ayat --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Rentang Ayat / Keterangan <span class="text-danger">*</span></label>
                        <input type="text" name="ayat_range" id="modalAyatRange" class="form-control form-control-sm"
                               placeholder="Contoh: Ayat 1–20, Seluruh Ayat" required style="border-radius:8px;">
                    </div>

                    {{-- Status Mutqin / Kelancaran --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Hasil Evaluasi Kelancaran <span class="text-danger">*</span></label>
                        <select name="status_mutqin" id="modalStatusMutqin" class="form-select form-select-sm" required style="border-radius:8px;">
                            <option value="Lancar" selected>Lancar (Mutqin)</option>
                            <option value="Kurang Lancar">Kurang Lancar (Perlu Diulang Sekali Lagi)</option>
                            <option value="Tidak Lancar">Tidak Lancar (Belum Menguasai Sambungan Ayat)</option>
                        </select>
                    </div>

                    {{-- Tanggal Murajaah --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Tanggal Murajaah <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_murajaah" class="form-control form-control-sm"
                               value="{{ date('Y-m-d') }}" required style="border-radius:8px;">
                    </div>

                    {{-- Catatan --}}
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-dark">Catatan Ustadz (Opsional)</label>
                        <textarea name="catatan" rows="3" class="form-control form-control-sm"
                                  placeholder="Catatan pelafalan makhraj, wakaf, atau pesan untuk santri..."
                                  style="border-radius:8px;"></textarea>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm text-white rounded-pill px-4 fw-semibold" style="background:#117A74;">
                        <i class="bi bi-save me-1"></i>Simpan Evaluasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
// Filter pencarian santri di panel kiri
document.getElementById('searchMurajaahSiswa')?.addEventListener('input', function() {
    const keyword = this.value.toLowerCase().trim();
    const cards = document.querySelectorAll('#listMurajaahSiswa .hafalan-siswa-card');

    cards.forEach(card => {
        const nama = card.getAttribute('data-nama') || '';
        const nis = card.getAttribute('data-nis') || '';
        if (nama.includes(keyword) || nis.includes(keyword)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
});

// Buka modal dari tombol baris setoran tertentu
function openModalEvaluasiSetoran(setoranId, surah, ayatRange) {
    document.getElementById('modalSetoranId').value = setoranId;
    document.getElementById('modalSurah').value = surah;
    document.getElementById('modalAyatRange').value = ayatRange;
    new bootstrap.Modal(document.getElementById('modalEvaluasiMurajaah')).show();
}

// Buka modal untuk murajaah mandiri (tanpa id setoran)
function openModalMurajaahBebas() {
    document.getElementById('modalSetoranId').value = '';
    document.getElementById('modalSurah').value = '';
    document.getElementById('modalAyatRange').value = 'Semua Ayat';
    new bootstrap.Modal(document.getElementById('modalEvaluasiMurajaah')).show();
}
</script>
@endsection
