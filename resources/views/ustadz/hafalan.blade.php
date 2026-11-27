@extends('layouts.ustadz', ['title' => 'Setoran Hafalan'])

@section('breadcrumb', 'Setoran Hafalan')

@section('styles')
<style>
.hafalan-modal-mushaf {
    max-width: 920px !important;
    width: 95% !important;
}
.mushaf-header-mewah {
    background: linear-gradient(135deg, #064E3B 0%, #0F766E 100%) !important;
    color: #FFFFFF !important;
    padding: 20px 26px !important;
    border-bottom: 0 !important;
}
.mushaf-header-mewah h5 {
    color: #FFFFFF !important;
    font-size: 1.3rem !important;
    letter-spacing: -0.01em;
}
.mushaf-judul-arab {
    font-family: 'Amiri', 'Traditional Arabic', serif !important;
    font-size: 26px !important;
    color: #FDE047 !important;
    line-height: 1.2;
}
.mushaf-body-mewah {
    background: #F8FAFC !important;
    max-height: 68vh !important;
    overflow-y: auto !important;
    padding: 24px !important;
}
.card-ayat-mushaf {
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    border-radius: 14px;
    padding: 20px 24px;
    margin-bottom: 16px;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}
.card-ayat-mushaf:hover {
    border-color: #0D9488;
    box-shadow: 0 6px 18px rgba(13, 148, 136, 0.08);
}
.card-ayat-mushaf.aktif {
    border-color: #0D9488 !important;
    background: #F0FDF9 !important;
    box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.2) !important;
}
.nomor-ayat-mushaf {
    width: 38px;
    height: 38px;
    min-width: 38px;
    border-radius: 50%;
    background: #E6F4F1;
    color: #0F766E;
    font-weight: 700;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1.5px solid #99F6E4;
}
.teks-arab-mushaf {
    font-family: 'Amiri', 'Traditional Arabic', serif;
    font-size: 28px;
    line-height: 2.2;
    color: #0F172A;
    text-align: right;
    direction: rtl;
    margin: 0;
}
.cell-baca-surah {
    cursor: pointer;
    transition: all 0.15s ease;
}
tr:hover .cell-baca-surah {
    background-color: #F0FDF9 !important;
}
tr:hover .cell-baca-surah .hafalan-surah-nama {
    color: #0D9488 !important;
}
.badge-baca-mushaf {
    font-size: 11px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 20px;
    background-color: #ECFDF5;
    color: #047857;
    border: 1px solid #A7F3D0;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
}
tr:hover .badge-baca-mushaf {
    background-color: #047857;
    color: #FFFFFF;
    border-color: #047857;
}
</style>
@endsection

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
            <div class="fw-semibold mb-1">Gagal menyimpan setoran hafalan:</div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    </div>
@endif

{{-- ── Header Banner ────────────────────────────────────────────────── --}}
<div class="mb-4">
    <span class="badge px-3 py-1 text-white mb-2 fw-bold" style="background-color: #117A74;">SETORAN HAFALAN</span>
    <h2 class="fw-bold mb-1" style="color: #1E293B; font-size: 1.65rem;">Penerimaan Setoran Siswa</h2>
    <p class="text-muted small mb-0">Pilih siswa di panel kiri untuk membuka daftar surah, membaca mushaf, dan mencatat setoran hafalan.</p>
</div>

{{-- ── Layout 2 Kolom (Sesuai Desain RPLBO) ─────────────────────────── --}}
<div class="hafalan-layout">

    {{-- ══ PANEL KIRI: DAFTAR PILIH SISWA ══════════════════════════════ --}}
    <section class="hafalan-panel hafalan-panel-siswa" aria-label="Pilih siswa">
        <h2 class="hafalan-panel-title">
            <i class="bi bi-person"></i>
            <span>Pilih Siswa ({{ $santriList->count() }})</span>
        </h2>
        <div class="hafalan-search-wrap">
            <i class="bi bi-search"></i>
            <input type="search" id="searchHafalanSiswa" placeholder="Cari nama atau NIS..." autocomplete="off">
        </div>
        <div id="listHafalanSiswa">
            @forelse($santriList as $s)
                @php
                    $kata = explode(' ', trim($s->nama_lengkap));
                    $inisial = strtoupper(substr($kata[0], 0, 1));
                    if (isset($kata[1])) $inisial .= strtoupper(substr($kata[1], 0, 1));
                    $hasTgt = isset($targetAktifMap[$s->id]);
                @endphp
                <button type="button"
                        class="hafalan-siswa-card"
                        data-id="{{ $s->id }}"
                        data-nama="{{ $s->nama_lengkap }}"
                        data-nis="{{ $s->nip_nis }}"
                        data-inisial="{{ $inisial }}">
                    <span class="hafalan-avatar" aria-hidden="true">{{ $inisial }}</span>
                    <span class="hafalan-siswa-meta">
                        <span class="hafalan-siswa-nama">{{ $s->nama_lengkap }}</span>
                        <span class="hafalan-siswa-nis">NIS: {{ $s->nip_nis }}</span>
                    </span>
                    @if($hasTgt)
                        <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2 py-1 ms-auto" style="font-size:10px;">Target</span>
                    @endif
                </button>
            @empty
                <div class="text-center py-4 text-muted small">
                    Belum ada siswa dalam halaqah ini.
                </div>
            @endforelse
        </div>
    </section>

    {{-- ══ PANEL KANAN: DAFTAR SURAH TARGET (MUNCUL SETELAH SISWA DI-KLIK) ══ --}}
    <section class="hafalan-panel hafalan-panel-surah" aria-label="Daftar surah target hafalan">

        {{-- State Awal: Belum Memilih Siswa --}}
        <div id="hafalanEmpty" class="hafalan-empty">
            <i class="bi bi-book fs-1 text-muted d-block mb-3"></i>
            <p>Pilih siswa terlebih dahulu di panel kiri untuk melihat daftar surah target hafalan.</p>
        </div>

        {{-- State Aktif: Muncul Otomatis Saat Nama Siswa Diklik --}}
        <div id="hafalanSurahArea" style="display: none;">
            {{-- Header Ringkasan & Filter Target Juz --}}
            <div class="d-flex flex-wrap justify-content-between align-items-center pb-3 mb-3 border-bottom gap-2">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">Daftar Surah Target Hafalan</h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="small text-muted">Target Aktif:</span>
                        <select class="form-select form-select-sm fw-bold border-0 bg-light text-dark"
                                style="width: auto; cursor: pointer; border-radius:8px;" id="selectJuzHafalan">
                            <option value="30_dhuha" selected>Juz 30 (Adh-Dhuha – An-Nas) — 22 Surah</option>
                            <option value="30">Juz 30 (An-Naba – An-Nas) — 37 Surah</option>
                            <option value="1">Juz 1 (Al-Fatihah &amp; Al-Baqarah 1–141)</option>
                            <option value="2">Juz 2 (Al-Baqarah 142–252)</option>
                            <option value="3">Juz 3 (Al-Baqarah 253–286 &amp; Ali 'Imran 1–92)</option>
                            <option value="4">Juz 4 (Ali 'Imran 93–200 &amp; An-Nisa' 1–23)</option>
                            <option value="5">Juz 5 (An-Nisa' 24–147)</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fw-semibold rounded-pill" style="font-size: 0.85rem;">
                        <i class="bi bi-person-fill me-1"></i>Siswa: <span id="displayNamaSantriHafalan" class="fw-bold">-</span>
                    </span>
                    <button type="button" class="btn text-white btn-sm px-3 py-1.5 rounded-pill fw-semibold shadow-sm"
                            style="background:#117A74; font-size:12px;" onclick="bukaSetoranBebas()">
                        <i class="bi bi-plus-circle me-1"></i>Setoran Bebas
                    </button>
                </div>
            </div>

            {{-- Bar Keterangan Status & Petunjuk Interaksi --}}
            <div class="d-flex flex-wrap align-items-center justify-content-between p-2.5 px-3 rounded-3 mb-3 gap-2"
                 style="background-color: #F0FDF9; border: 1px solid #CCFBF1;">
                <div class="small text-dark">
                    <span class="badge rounded-pill bg-white text-success border border-success-subtle px-2 py-0.5 me-1 fw-bold">Tips</span>
                    Klik <strong>Nama Surah</strong> atau <strong>Target Ayat</strong> untuk membuka <strong>Mushaf Al-Qur'an</strong>. Klik tombol <strong><i class="bi bi-plus-lg text-success"></i></strong> di kolom Aksi untuk mencatat setoran santri.
                </div>
                <div class="d-flex align-items-center gap-2 small">
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1">Selesai</span>
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1">Sedang Berjalan</span>
                    <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1">Belum Setor</span>
                </div>
            </div>

            {{-- Tabel Daftar Surah Target (SCROLL CONTAINER + STICKY HEADER) --}}
            <div class="table-responsive" style="max-height: 480px; overflow-y: auto; border: 1px solid #E2E8F0; border-radius: 12px; margin-bottom: 24px;">
                <table class="hafalan-table mb-0" id="tabelSurahHafalan">
                    <thead style="position: sticky; top: 0; background: #FFFFFF; z-index: 2;">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama Surah</th>
                            <th style="width: 140px;">Target Ayat</th>
                            <th style="width: 170px;" class="text-center">Status</th>
                            <th class="col-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbodySurahHafalan">
                        {{-- Diisi secara dinamis via JavaScript sesuai santri & juz yang dipilih --}}
                    </tbody>
                </table>
            </div>

            {{-- Tabel Riwayat Setoran Nyata dari Database (dirender ulang via JS) --}}
            <div class="pt-3 border-top">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold mb-0 text-dark" style="font-size:13.5px;">
                        <i class="bi bi-clock-history me-1" style="color:#117A74;"></i>Riwayat Setoran Tercatat di Database (<span id="totalSetoranCount">0</span>)
                    </h6>
                </div>
                <div class="table-responsive" style="border:1px solid #E2E8F0; border-radius:10px; overflow:hidden;">
                    <table class="table table-hover align-middle mb-0" style="font-size:12.5px;">
                        <thead style="background:#F8FAFC;">
                            <tr style="font-size:11.5px; color:#64748B; text-transform:uppercase; letter-spacing:0.03em;">
                                <th class="ps-3 py-2 fw-semibold" style="width:40px;">No</th>
                                <th class="py-2 fw-semibold" style="width:110px;">Tanggal</th>
                                <th class="py-2 fw-semibold">Surah &amp; Ayat</th>
                                <th class="py-2 fw-semibold" style="width:120px;">Nilai</th>
                                <th class="py-2 pe-3 fw-semibold">Catatan</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyRiwayatSetoran">
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted small">
                                    Pilih siswa untuk melihat riwayat setoran.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

</div>

{{-- ═══════════════════════════════════════════════════════════════════
     MODAL POP-UP: INPUT SETORAN HAFALAN
════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalInputSetoran" tabindex="-1" aria-labelledby="modalSetoranLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 540px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Modal Header -->
            <div class="modal-header border-0 text-white py-3 px-4"
                 style="background: linear-gradient(135deg, #0A2E2B 0%, #11756D 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center"
                         style="background: rgba(255,255,255,0.15); width: 42px; height: 42px;">
                        <i class="bi bi-journal-bookmark fs-4 text-warning"></i>
                    </div>
                    <div>
                        <span class="badge bg-warning text-dark fw-bold px-2 py-0.5 mb-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                            SETORAN HAFALAN
                        </span>
                        <h5 class="modal-title fw-bold mb-0 text-white" id="modalJudulSurah">Surah Adh-Dhuha</h5>
                        <div class="small text-white-50 mt-0.5" id="modalSubInfo">Target: Ayat 1 s/d 11</div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-4">
                <!-- Card Ringkasan Santri -->
                <div class="d-flex align-items-center justify-content-between p-3 rounded-3 mb-3"
                     style="background-color: #F0FDF9; border: 1px solid #CCFBF1;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                             style="width: 36px; height: 36px; background: #11756D; font-size: 0.82rem;" id="modalAvatarSantri">
                            MF
                        </div>
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.7rem;">Santri yang Menyetor:</small>
                            <span class="fw-bold text-dark" style="font-size: 13.5px;" id="modalNamaSantri">Nama Santri</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-1.5">
                        <span class="badge bg-white text-muted border px-2 py-1" style="font-size:11px;" id="modalNisSantri">NIS: -</span>
                        <span class="badge rounded-pill text-white px-2.5 py-1" style="background-color: #11756D; font-size:11px;" id="modalBadgeTotalAyat">11 Ayat</span>
                    </div>
                </div>

                <!-- Formulir Lembar Setoran -->
                <form action="{{ route('ustadz.hafalan.store') }}" method="POST" id="formSetoranModal">
                    @csrf
                    <input type="hidden" name="siswa_id" id="modalInputSiswaId" value="">
                    <input type="hidden" name="surah" id="modalInputSurahNama" value="">

                    {{-- Opsi Pilih Surah Jika Mode Bebas --}}
                    <div class="mb-3" id="wrapPilihSurahBebas" style="display:none;">
                        <label class="form-label small fw-bold text-dark mb-1">Pilih Surah Al-Qur'an <span class="text-danger">*</span></label>
                        <select id="selectSurahBebas" class="form-select" style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px; height:42px; font-size:14px;">
                            <option value="">-- Pilih Surah --</option>
                            @foreach($surahList as $sr)
                                <option value="{{ $sr['nama'] }}" data-ayat="{{ $sr['ayat'] }}">
                                    {{ $sr['nomor'] }}. {{ $sr['nama'] }} ({{ $sr['ayat'] }} Ayat)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark mb-1">
                                Dari Ayat <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="ayat_awal" id="modalInputAyatAwal" class="form-control fw-bold" value="1" min="1" required
                                   style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px; height:42px; font-size:14px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark mb-1">
                                Sampai Ayat <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="ayat_akhir" id="modalInputAyatAkhir" class="form-control fw-bold" value="11" min="1" required
                                   style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px; height:42px; font-size:14px; color:#11756D;">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark mb-1">Nilai Hafalan (0–100) <span class="text-danger">*</span></label>
                            <input type="number" name="nilai_hafalan" id="modalNilaiHafalan" class="form-control"
                                   value="80" min="0" max="100" required
                                   style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px; height:42px; font-size:14px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark mb-1">Tanggal Setor <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_setor" id="modalInputTanggal" class="form-control" value="{{ date('Y-m-d') }}" required
                                   style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px; height:42px; font-size:14px;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Catatan Ustadz (Opsional)</label>
                        <textarea name="catatan" id="modalTextareaCatatan" class="form-control" rows="3"
                                  placeholder="Catatan makhraj, tajwid, atau kelancaran hafalan..."
                                  style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px; font-size:13.5px;"></textarea>
                    </div>

                    <div id="modalAyatError" class="alert alert-danger small py-2 mb-3" style="display:none;"></div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold text-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn text-white rounded-pill px-4 fw-bold shadow-sm" style="background:#11756D;" id="modalBtnSubmit">
                            <i class="bi bi-check2-circle me-1"></i>Simpan Setoran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ══ MODAL BACA SURAH (MUSHAF INTERAKTIF) ═════════════════════════════ --}}
<div class="modal fade" id="modalBacaSurah" tabindex="-1" aria-labelledby="mushafJudul" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl hafalan-modal-mushaf" style="max-width: 920px !important;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="mushaf-header mushaf-header-mewah d-flex justify-content-between align-items-center">
                <div>
                    <h5 id="mushafJudul" class="mb-1 fw-bold text-white d-flex align-items-center flex-wrap gap-2"></h5>
                    <p id="mushafSubjudul" class="mb-0 text-white-50 small"></p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center gap-1.5 p-1.5 px-2.5 rounded-3" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25);">
                        <label for="filterAyatMushaf" class="small text-white text-nowrap mb-0 fw-semibold" style="font-size:12px;">Lompat Ayat:</label>
                        <select id="filterAyatMushaf" class="form-select form-select-sm py-1 border-0 fw-semibold" style="width:130px; border-radius:8px; font-size:12px;"></select>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
            </div>
            <div class="mushaf-body mushaf-body-mewah" id="mushafBody">
                {{-- Diisi secara dinamis oleh JavaScript / API --}}
            </div>
            <div class="modal-footer bg-white border-top py-2.5 px-4 d-flex justify-content-between align-items-center">
                <span class="text-muted small">
                    <i class="bi bi-info-circle me-1 text-success"></i>Teks Arab Utsmani &amp; Terjemahan resmi Kementerian Agama RI
                </span>
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">
                    Tutup Mushaf
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
(function () {
    // ── Peta setoran per siswa dari database (dari controller) ──────────
    const SETORAN_PER_SISWA = @json($setoranPerSiswa);
    const TARGET_AKTIF_MAP  = @json($targetAktifMap);

    // ── Database Kamus Surah per Juz ────────────────────────────────────
    const QURAN_JUZ = {
        '30_dhuha': [
            { no: 93, nama: 'Adh-Dhuha', arab: 'الضحى', ayat: 11, key: 'adh-dhuha' },
            { no: 94, nama: 'Al-Insyirah', arab: 'الشرح', ayat: 8, key: 'al-insyirah' },
            { no: 95, nama: 'At-Tin', arab: 'التين', ayat: 8, key: 'at-tin' },
            { no: 96, nama: "Al-'Alaq", arab: 'العلق', ayat: 19, key: 'al-alaq' },
            { no: 97, nama: 'Al-Qadr', arab: 'القدر', ayat: 5, key: 'al-qadr' },
            { no: 98, nama: 'Al-Bayyinah', arab: 'البينة', ayat: 8, key: 'al-bayyinah' },
            { no: 99, nama: 'Az-Zalzalah', arab: 'الزلزلة', ayat: 8, key: 'az-zalzalah' },
            { no: 100, nama: "Al-'Adiyat", arab: 'العاديات', ayat: 11, key: 'al-adiyat' },
            { no: 101, nama: "Al-Qari'ah", arab: 'القارعة', ayat: 11, key: 'al-qariah' },
            { no: 102, nama: 'At-Takatsur', arab: 'التكاثر', ayat: 8, key: 'at-takatsur' },
            { no: 103, nama: "Al-'Asr", arab: 'العصر', ayat: 3, key: 'al-asr' },
            { no: 104, nama: 'Al-Humazah', arab: 'الهمزة', ayat: 9, key: 'al-humazah' },
            { no: 105, nama: 'Al-Fil', arab: 'الفيل', ayat: 5, key: 'al-fil' },
            { no: 106, nama: 'Quraisy', arab: 'قريش', ayat: 4, key: 'quraisy' },
            { no: 107, nama: "Al-Ma'un", arab: 'الماعون', ayat: 7, key: 'al-maun' },
            { no: 108, nama: 'Al-Kautsar', arab: 'الكوثر', ayat: 3, key: 'al-kautsar' },
            { no: 109, nama: 'Al-Kafirun', arab: 'الكافرون', ayat: 6, key: 'al-kafirun' },
            { no: 110, nama: 'An-Nasr', arab: 'النصر', ayat: 3, key: 'an-nasr' },
            { no: 111, nama: 'Al-Lahab', arab: 'المسد', ayat: 5, key: 'al-lahab' },
            { no: 112, nama: 'Al-Ikhlas', arab: 'الإخلاص', ayat: 4, key: 'al-ikhlas' },
            { no: 113, nama: 'Al-Falaq', arab: 'الفلق', ayat: 5, key: 'al-falaq' },
            { no: 114, nama: 'An-Nas', arab: 'الناس', ayat: 6, key: 'an-nas' }
        ],
        '30': [
            { no: 78, nama: "An-Naba'", arab: 'النبأ', ayat: 40, key: 'an-naba' },
            { no: 79, nama: "An-Nazi'at", arab: 'النازعات', ayat: 46, key: 'an-naziat' },
            { no: 80, nama: "'Abasa", arab: 'عبس', ayat: 42, key: 'abasa' },
            { no: 81, nama: 'At-Takwir', arab: 'التكوير', ayat: 29, key: 'at-takwir' },
            { no: 82, nama: 'Al-Infitar', arab: 'الانفطار', ayat: 19, key: 'al-infitar' },
            { no: 83, nama: 'Al-Muthaffifin', arab: 'المطففين', ayat: 36, key: 'al-muthaffifin' },
            { no: 84, nama: 'Al-Insyiqaq', arab: 'الانشقاق', ayat: 25, key: 'al-insyiqaq' },
            { no: 85, nama: 'Al-Buruj', arab: 'البروج', ayat: 22, key: 'al-buruj' },
            { no: 86, nama: 'At-Tariq', arab: 'الطارق', ayat: 17, key: 'at-tariq' },
            { no: 87, nama: "Al-A'la", arab: 'الأعلى', ayat: 19, key: 'al-ala' },
            { no: 88, nama: 'Al-Ghasyiyah', arab: 'الغاشية', ayat: 26, key: 'al-ghasyiyah' },
            { no: 89, nama: 'Al-Fajr', arab: 'الفجر', ayat: 30, key: 'al-fajr' },
            { no: 90, nama: 'Al-Balad', arab: 'البلد', ayat: 20, key: 'al-balad' },
            { no: 91, nama: 'Asy-Syams', arab: 'الشمس', ayat: 15, key: 'asy-syams' },
            { no: 92, nama: 'Al-Lail', arab: 'الليل', ayat: 21, key: 'al-lail' },
            { no: 93, nama: 'Adh-Dhuha', arab: 'الضحى', ayat: 11, key: 'adh-dhuha' },
            { no: 94, nama: 'Al-Insyirah', arab: 'الشرح', ayat: 8, key: 'al-insyirah' },
            { no: 95, nama: 'At-Tin', arab: 'التين', ayat: 8, key: 'at-tin' },
            { no: 96, nama: "Al-'Alaq", arab: 'العلق', ayat: 19, key: 'al-alaq' },
            { no: 97, nama: 'Al-Qadr', arab: 'القدر', ayat: 5, key: 'al-qadr' },
            { no: 98, nama: 'Al-Bayyinah', arab: 'البينة', ayat: 8, key: 'al-bayyinah' },
            { no: 99, nama: 'Az-Zalzalah', arab: 'الزلزلة', ayat: 8, key: 'az-zalzalah' },
            { no: 100, nama: "Al-'Adiyat", arab: 'العاديات', ayat: 11, key: 'al-adiyat' },
            { no: 101, nama: "Al-Qari'ah", arab: 'القارعة', ayat: 11, key: 'al-qariah' },
            { no: 102, nama: 'At-Takatsur', arab: 'التكاثر', ayat: 8, key: 'at-takatsur' },
            { no: 103, nama: "Al-'Asr", arab: 'العصر', ayat: 3, key: 'al-asr' },
            { no: 104, nama: 'Al-Humazah', arab: 'الهمزة', ayat: 9, key: 'al-humazah' },
            { no: 105, nama: 'Al-Fil', arab: 'الفيل', ayat: 5, key: 'al-fil' },
            { no: 106, nama: 'Quraisy', arab: 'قريش', ayat: 4, key: 'quraisy' },
            { no: 107, nama: "Al-Ma'un", arab: 'الماعون', ayat: 7, key: 'al-maun' },
            { no: 108, nama: 'Al-Kautsar', arab: 'الكوثر', ayat: 3, key: 'al-kautsar' },
            { no: 109, nama: 'Al-Kafirun', arab: 'الكافرون', ayat: 6, key: 'al-kafirun' },
            { no: 110, nama: 'An-Nasr', arab: 'النصر', ayat: 3, key: 'an-nasr' },
            { no: 111, nama: 'Al-Lahab', arab: 'المسد', ayat: 5, key: 'al-lahab' },
            { no: 112, nama: 'Al-Ikhlas', arab: 'الإخلاص', ayat: 4, key: 'al-ikhlas' },
            { no: 113, nama: 'Al-Falaq', arab: 'الفلق', ayat: 5, key: 'al-falaq' },
            { no: 114, nama: 'An-Nas', arab: 'الناس', ayat: 6, key: 'an-nas' }
        ],
        '1': [
            { no: 1,  nama: 'Al-Fatihah',  arab: 'الفاتحة',  ayat: 7,   key: 'al-fatihah' },
            { no: 2,  nama: 'Al-Baqarah',  arab: 'البقرة',   ayat: 141, key: 'al-baqarah-1' }
        ],
        '2': [
            { no: 2,  keterangan: 'Ayat 142-252', nama: 'Al-Baqarah', arab: 'البقرة', ayat: 252, key: 'al-baqarah-2' }
        ],
        '3': [
            { no: 2,  keterangan: 'Ayat 253-286', nama: 'Al-Baqarah', arab: 'البقرة', ayat: 286, key: 'al-baqarah-3' },
            { no: 3,  keterangan: 'Ayat 1-92', nama: "Ali 'Imran",    arab: 'آل عمران', ayat: 92,  key: 'ali-imran-1' }
        ],
        '4': [
            { no: 3,  keterangan: 'Ayat 93-200', nama: "Ali 'Imran",  arab: 'آل عمران', ayat: 200, key: 'ali-imran-2' },
            { no: 4,  keterangan: 'Ayat 1-23', nama: 'An-Nisa',       arab: 'النساء',   ayat: 23,  key: 'an-nisa-1' }
        ],
        '5': [
            { no: 4,  keterangan: 'Ayat 24-147', nama: 'An-Nisa',     arab: 'النساء',   ayat: 147, key: 'an-nisa-2' }
        ],
    };

    // ── Dataset Teks Mushaf (5 Surah) ────────────────────────────────────
    const MUSHAF_MOCK = {
        'adh-dhuha': {
            no: 93, nama: 'Adh-Dhuha', arab: 'الضحى', arti: 'Duha', jumlah: 11, golongan: 'Makkiyah',
            ayat: [
                { arab: 'وَالضُّحَىٰ', latin: 'Waḍ-ḍuḥā.', arti: 'Demi waktu duha' },
                { arab: 'وَاللَّيْلِ إِذَا سَجَىٰ', latin: 'Wal-laili izā sajā.', arti: 'dan demi waktu malam apabila telah sunyi' },
                { arab: 'مَا وَدَّعَكَ رَبُّكَ وَمَا قَلَىٰ', latin: 'Mā wadda\'aka rabbuka wa mā qalā.', arti: 'Tuhanmu tidak meninggalkan engkau dan tidak (pula) membencimu' },
                { arab: 'وَلَلْآخِرَةُ خَيْرٌ لَّكَ مِنَ الْأُولَىٰ', latin: 'Wa lal-ākhiratu khairul laka minal-ūlā.', arti: 'Dan sungguh, akhirat itu lebih baik bagimu daripada yang permulaan' },
                { arab: 'وَلَسَوْفَ يُعْطِيكَ رَبُّكَ فَتَرْضَىٰ', latin: 'Wa lasaufa yu\'ṭīka rabbuka fa tarḍā.', arti: 'Dan sungguh, kelak Tuhanmu pasti memberikan karunia-Nya kepadamu' },
                { arab: 'أَلَمْ يَجِدْكَ يَتِيمًا فَآوَىٰ', latin: 'Alam yajidka yatīman fa āwā.', arti: 'Bukankah Dia mendapatimu sebagai seorang yatim, lalu Dia melindungi(-mu)' },
                { arab: 'وَوَجَدَكَ ضَالًّا فَهَدَىٰ', latin: 'Wa wajadaka ḍāllan fa hadā.', arti: 'Dan Dia mendapatimu sebagai seorang yang bingung, lalu Dia memberikan petunjuk' },
                { arab: 'وَوَجَدَكَ عَائِلًا فَأَغْنَىٰ', latin: 'Wa wajadaka \'ā\'ilan fa aghnā.', arti: 'Dan Dia mendapatimu sebagai seorang yang kekurangan, lalu Dia memberikan kecukupan' },
                { arab: 'فَأَمَّا الْيَتِيمَ فَلَا تَقْهَرْ', latin: 'Fa ammal-yatīma fa lā taqhar.', arti: 'Maka terhadap anak yatim janganlah engkau berlaku sewenang-wenang' },
                { arab: 'وَأَمَّا السَّائِلَ فَلَا تَنْهَرْ', latin: 'Wa ammas-sā\'ila fa lā tanhar.', arti: 'Dan terhadap orang yang meminta-minta janganlah engkau menghardik(-nya)' },
                { arab: 'وَأَمَّا بِنِعْمَةِ رَبِّكَ فَحَدِّثْ', latin: 'Wa ammā bini\'mati rabbika fa ḥaddiṡ.', arti: 'Dan terhadap nikmat Tuhanmu hendaklah engkau nyatakan (dengan bersyukur)' }
            ]
        },
        'al-insyirah': {
            no: 94, nama: 'Al-Insyirah', arab: 'الشرح', arti: 'Kelapangan', jumlah: 8, golongan: 'Makkiyah',
            ayat: [
                { arab: 'أَلَمْ نَشْرَحْ لَكَ صَدْرَكَ', latin: 'Alam nasyraḥ laka ṣadrak.', arti: 'Bukankah Kami telah melapangkan dadamu (Muhammad)?' },
                { arab: 'وَوَضَعْنَا عَنكَ وِزْرَكَ', latin: 'Wa waḍa\'nā \'anka wizrak.', arti: 'dan Kami pun telah menurunkan bebanmu darimu' },
                { arab: 'الَّذِي أَنقَضَ ظَهْرَكَ', latin: 'Allażī anqaḍa ẓahrak.', arti: 'yang memberatkan punggungmu' },
                { arab: 'وَرَفَعْنَا لَكَ ذِكْرَكَ', latin: 'Wa rafa\'nā laka żikrak.', arti: 'dan Kami tinggikan sebutan (nama)mu bagimu' },
                { arab: 'فَإِنَّ مَعَ الْعُسْرِ يُسْرًا', latin: 'Fa inna ma\'al-\'usri yusrā.', arti: 'Maka sesungguhnya beserta kesulitan ada kemudahan' },
                { arab: 'إِنَّ مَعَ الْعُسْرِ يُسْرًا', latin: 'Inna ma\'al-\'usri yusrā.', arti: 'sesungguhnya beserta kesulitan ada kemudahan' },
                { arab: 'فَإِذَا فَرَغْتَ فَانصَبْ', latin: 'Fa iżā faraghta fanṣab.', arti: 'Maka apabila engkau telah selesai (dari sesuatu urusan), tetaplah bekerja keras' },
                { arab: 'وَإِلَىٰ رَبِّكَ فَارْغَبْ', latin: 'Wa ilā rabbika farghab.', arti: 'dan hanya kepada Tuhanmulah engkau berharap' }
            ]
        },
        'at-tin': {
            no: 95, nama: 'At-Tin', arab: 'التين', arti: 'Buah Tin', jumlah: 8, golongan: 'Makkiyah',
            ayat: [
                { arab: 'وَالتِّينِ وَالزَّيْتُونِ', latin: 'Wat-tīni waz-zaitūn.', arti: 'Demi (buah) Tin dan (buah) Zaitun' },
                { arab: 'وَطُورِ سِينِينَ', latin: 'Wa ṭūri sīnīn.', arti: 'demi gunung Sinai' },
                { arab: 'وَهَٰذَا الْبَلَدِ الْأَمِينِ', latin: 'Wa hażal-baladil-amīn.', arti: 'dan demi negeri (Mekah) yang aman ini' },
                { arab: 'لَقَدْ خَلَقْنَا الْإِنسَانَ فِي أَحْسَنِ تَقْوِيمٍ', latin: 'Laqad khalaqnal-insāna fī aḥsani taqwīm.', arti: 'Sungguh, Kami telah menciptakan manusia dalam bentuk yang sebaik-baiknya' },
                { arab: 'ثُمَّ رَدَدْنَاهُ أَسْفَلَ سَافِلِينَ', latin: 'Ṡumma radadnāhu asfala sāfilīn.', arti: 'kemudian Kami kembalikan dia ke tempat yang serendah-rendahnya' },
                { arab: 'إِلَّا الَّذِينَ آمَنُوا وَعَمِلُوا الصَّالِحَاتِ', latin: 'Illal-lażīna āmanū wa \'amiluṣ-ṣāliḥāti...', arti: 'kecuali orang-orang yang beriman dan mengerjakan kebajikan' },
                { arab: 'فَمَا يُكَذِّبُكَ بَعْدُ بِالدِّينِ', latin: 'Fama yukażżibuka ba\'du bid-dīn.', arti: 'Maka apa yang menyebabkan (mereka) mendustakanmu tentang hari pembalasan?' },
                { arab: 'أَلَيْسَ اللَّهُ بِأَحْكَمِ الْحَاكِمِينَ', latin: 'Alaisallāhu bi-aḥkamil-ḥākimīn.', arti: 'Bukankah Allah hakim yang paling adil?' }
            ]
        },
        'al-qadr': {
            no: 97, nama: 'Al-Qadr', arab: 'القدر', arti: 'Kemuliaan', jumlah: 5, golongan: 'Makkiyah',
            ayat: [
                { arab: 'إِنَّا أَنزَلْنَاهُ فِي لَيْلَةِ الْقَدْرِ', latin: 'Innā anzalnāhu fī lailatil-qadr.', arti: 'Sesungguhnya Kami telah menurunkannya (Al-Qur\'an) pada malam qadar' },
                { arab: 'وَمَا أَدْرَاكَ مَا لَيْلَةُ الْقَدْرِ', latin: 'Wa mā adrāka mā lailatul-qadr.', arti: 'Dan tahukah kamu apakah malam kemuliaan itu?' },
                { arab: 'لَيْلَةُ الْقَدْرِ خَيْرٌ مِّنْ أَلْفِ شَهْرٍ', latin: 'Lailatul-qadri khairum min alfi syahr.', arti: 'Malam kemuliaan itu lebih baik daripada seribu bulan' },
                { arab: 'تَنَزَّلُ الْمَلَائِكَةُ وَالرُّوحُ فِيهَا', latin: 'Tanazzalul-malā\'ikatu war-rūḥu fīhā...', arti: 'Pada malam itu turun para malaikat dan Ruh (Jibril) dengan izin Tuhannya' },
                { arab: 'سَلَامٌ هِيَ حَتَّىٰ مَطْلَعِ الْفَجْرِ', latin: 'Salāmun hiya ḥattā maṭla\'il-fajr.', arti: 'Sejahteralah (malam itu) sampai terbit fajar' }
            ]
        }
    };

    let siswaAktif = null;

    function escHtml(str) {
        return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // ── Normalisasi nama surah untuk lookup setoran DB ───────────────────
    function normNama(n) {
        return String(n || '').toLowerCase()
            .replace(/[^a-z0-9]/g, '');
    }

    // ── Render Tabel Surah dengan Status Real dari Database ─────────────
    function renderSurahPerJuz(juzKey) {
        const tbody = document.getElementById('tbodySurahHafalan');
        if (!tbody) return;

        const surahArray = QURAN_JUZ[juzKey] || QURAN_JUZ['30_dhuha'];

        // Ambil setoran siswa aktif dari DB
        const setoranList = siswaAktif ? (SETORAN_PER_SISWA[siswaAktif.id] || []) : [];

        // Buat index: normKey → max ayat yang sudah disetor
        const setoranMap = {};
        setoranList.forEach(s => {
            const k = normNama(s.surah);
            const maxA = parseInt(s.ayat_akhir || 0);
            if (!setoranMap[k] || maxA > setoranMap[k]) setoranMap[k] = maxA;
        });

        tbody.innerHTML = surahArray.map((s, idx) => {
            const k = normNama(s.nama);
            const maxSetoran = setoranMap[k] || 0;

            let statusBadge;
            if (maxSetoran >= s.ayat) {
                statusBadge = `<span class="hafalan-badge hafalan-badge-selesai"><i class="bi bi-check-circle-fill me-1"></i>Selesai</span>`;
            } else if (maxSetoran > 0) {
                statusBadge = `<span class="hafalan-badge hafalan-badge-berjalan"><i class="bi bi-clock-history me-1"></i>Sedang Berjalan</span>`;
            } else {
                statusBadge = `<span class="hafalan-badge hafalan-badge-belum">Belum Setor</span>`;
            }

            const escapedNama = escHtml(s.nama).replace(/'/g, "\\'");
            const escapedArab = (s.arab || '').replace(/'/g, "\\'");
            const safeKey = s.key || '';
            const targetAyatText = s.keterangan ? s.keterangan : `Ayat 1–${s.ayat}`;

            return `<tr>
                <td style="color:#94A3B8; font-size:13px; vertical-align:middle;">${idx + 1}.</td>
                <td class="cell-baca-surah"
                    title="Klik untuk membuka Mushaf Surah ${escHtml(s.nama)}"
                    onclick="bukaMushaf(${s.no}, '${escapedNama}', '${escapedArab}', '${safeKey}')">
                    <div class="d-flex align-items-center justify-content-between gap-2 py-1">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-book-half text-success" style="font-size:15px;"></i>
                            <span class="hafalan-surah-nama fw-semibold text-dark">
                                ${escHtml(s.nama)} &ndash; <span class="hafalan-arab" style="font-family:'Amiri', serif; font-size:18px;">${s.arab}</span>
                            </span>
                        </div>
                        <span class="badge-baca-mushaf">
                            <i class="bi bi-book-half"></i> Baca Mushaf
                        </span>
                    </div>
                </td>
                <td class="cell-baca-surah"
                    title="Klik untuk membuka Mushaf Surah ${escHtml(s.nama)}"
                    onclick="bukaMushaf(${s.no}, '${escapedNama}', '${escapedArab}', '${safeKey}')"
                    style="color:#475569; font-size:13.5px; vertical-align:middle;">
                    <span>${targetAyatText}</span>
                </td>
                <td class="text-center" style="vertical-align:middle;">${statusBadge}</td>
                <td class="col-aksi" style="vertical-align:middle;">
                    <button type="button" class="btn-setoran-plus" title="Catat Setoran ${escHtml(s.nama)}"
                            onclick="bukaLembarSetoran('${escapedNama}', ${s.ayat})">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </td>
            </tr>`;
        }).join('');
    }

    // ── Render Riwayat Setoran dari Database ─────────────────────────────
    function renderRiwayatSetoran(siswaId) {
        const tbody = document.getElementById('tbodyRiwayatSetoran');
        const counter = document.getElementById('totalSetoranCount');
        const list = SETORAN_PER_SISWA[siswaId] || [];

        if (counter) counter.textContent = list.length;

        if (!tbody) return;
        if (list.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted small">Belum ada riwayat setoran hafalan yang tercatat untuk siswa ini.</td></tr>`;
            return;
        }

        tbody.innerHTML = list.slice(0, 15).map((s, idx) => {
            const tgl  = s.tanggal_setor ? s.tanggal_setor.substring(0, 10) : '-';
            const nilaiH = s.nilai_hafalan ?? '–';
            let kualBadge;
            if (nilaiH >= 90) {
                kualBadge = `<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">A (${nilaiH})</span>`;
            } else if (nilaiH >= 80) {
                kualBadge = `<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">AB (${nilaiH})</span>`;
            } else if (nilaiH >= 70) {
                kualBadge = `<span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 rounded-pill">B (${nilaiH})</span>`;
            } else if (nilaiH >= 60) {
                kualBadge = `<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 rounded-pill">BC (${nilaiH})</span>`;
            } else {
                kualBadge = `<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill">C/D/E (${nilaiH})</span>`;
            }
            return `<tr>
                <td class="ps-3 text-muted small">${idx + 1}</td>
                <td class="text-nowrap small text-secondary fw-semibold">${tgl}</td>
                <td>
                    <div class="fw-bold text-dark">${escHtml(s.surah)}</div>
                    <div class="text-muted" style="font-size:11px;">Ayat ${s.ayat_awal} – ${s.ayat_akhir}</div>
                </td>
                <td>${kualBadge}</td>
                <td class="pe-3 text-muted small">${escHtml(s.catatan || '–')}</td>
            </tr>`;
        }).join('');
    }

    // ── Buka Lembar Setoran Eksklusif ────────────────────────────────────
    window.bukaLembarSetoran = function (surahNama, totalAyat) {
        if (!siswaAktif) {
            alert('Silakan pilih santri terlebih dahulu di panel kiri!');
            return;
        }

        document.getElementById('modalInputSiswaId').value = siswaAktif.id;
        document.getElementById('modalInputSurahNama').value = surahNama;
        document.getElementById('wrapPilihSurahBebas').style.display = 'none';

        document.getElementById('modalJudulSurah').textContent = 'Surah ' + surahNama;
        document.getElementById('modalSubInfo').textContent = 'Target: Ayat 1 s/d ' + totalAyat;
        document.getElementById('modalNamaSantri').textContent = siswaAktif.nama;
        document.getElementById('modalNisSantri').textContent = 'NIS: ' + siswaAktif.nis;
        document.getElementById('modalAvatarSantri').textContent = siswaAktif.inisial;
        document.getElementById('modalBadgeTotalAyat').textContent = 'Total ' + totalAyat + ' Ayat';

        const inputAwal = document.getElementById('modalInputAyatAwal');
        const inputAkhir = document.getElementById('modalInputAyatAkhir');
        inputAwal.value = 1;
        inputAwal.max = totalAyat;
        inputAkhir.value = totalAyat;
        inputAkhir.max = totalAyat;

        document.getElementById('modalAyatError').style.display = 'none';

        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalInputSetoran')).show();
    };

    // ── Setoran Bebas (pilih surah sendiri) ─────────────────────────────
    window.bukaSetoranBebas = function () {
        if (!siswaAktif) {
            alert('Silakan pilih santri terlebih dahulu di panel kiri!');
            return;
        }

        document.getElementById('modalInputSiswaId').value = siswaAktif.id;
        document.getElementById('wrapPilihSurahBebas').style.display = 'block';

        document.getElementById('modalJudulSurah').textContent = 'Setoran Bebas (Pilih Surah)';
        document.getElementById('modalSubInfo').textContent = 'Pilih surah dari 114 surah Al-Qur\'an';
        document.getElementById('modalNamaSantri').textContent = siswaAktif.nama;
        document.getElementById('modalNisSantri').textContent = 'NIS: ' + siswaAktif.nis;
        document.getElementById('modalAvatarSantri').textContent = siswaAktif.inisial;
        document.getElementById('modalBadgeTotalAyat').textContent = 'Surah Bebas';

        document.getElementById('selectSurahBebas').value = '';
        document.getElementById('modalAyatError').style.display = 'none';

        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalInputSetoran')).show();
    };

    // Sinkronisasi pilihan surah bebas
    document.getElementById('selectSurahBebas')?.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        const ayat = parseInt(opt.getAttribute('data-ayat') || '10');

        document.getElementById('modalInputSurahNama').value = this.value;
        document.getElementById('modalInputAyatAwal').value = 1;
        document.getElementById('modalInputAyatAwal').max = ayat;
        document.getElementById('modalInputAyatAkhir').value = ayat;
        document.getElementById('modalInputAyatAkhir').max = ayat;
        document.getElementById('modalBadgeTotalAyat').textContent = 'Total ' + ayat + ' Ayat';
    });

    // ── Cache memori mushaf agar surah yang pernah dibuka langsung instan ─────
    const mushafCache = {};

    // ── Buka Modal Mushaf Interaktif (Online API Al-Qur'an Kemenag RI) ────────
    window.bukaMushaf = function (noSurah, namaSurah, arabSurah, mockKey) {
        const modalEl = document.getElementById('modalBacaSurah');
        const modalJudul = document.getElementById('mushafJudul');
        const modalSubjudul = document.getElementById('mushafSubjudul');
        const tubuh = document.getElementById('mushafBody');
        const select = document.getElementById('filterAyatMushaf');

        modalJudul.innerHTML = `${escHtml(namaSurah)} <span class="mushaf-judul-arab ms-2">${arabSurah || ''}</span> <span style="font-size:14px; color:#64748B;">(QS. ${noSurah})</span>`;
        modalSubjudul.textContent = 'Memuat ayat dan terjemahan...';
        select.innerHTML = '<option value="">Lompat ke Ayat</option>';

        tubuh.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border" style="color:#11756D; width:2.5rem; height:2.5rem;" role="status"></div>
                <div class="mt-3 text-muted small fw-semibold">Mengambil ayat-ayat Surah ${escHtml(namaSurah)}...</div>
            </div>`;

        bootstrap.Modal.getOrCreateInstance(modalEl).show();

        // 1. Cek Cache Memori
        if (mushafCache[noSurah]) {
            renderDataMushaf(mushafCache[noSurah]);
            return;
        }

        // 2. Fetch ke API Publik Al-Qur'an Indonesia (EQuran.id API v2)
        fetch(`https://equran.id/api/v2/surat/${noSurah}`)
            .then(res => {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(json => {
                if (json && json.data && json.data.ayat) {
                    mushafCache[noSurah] = json.data;
                    renderDataMushaf(json.data);
                } else {
                    throw new Error('Data ayat kosong');
                }
            })
            .catch(err => {
                console.warn('API Utama gagal, mencoba data cadangan/mock...', err);
                // 3. Fallback jika ada di MUSHAF_MOCK lokal
                if (mockKey && MUSHAF_MOCK[mockKey]) {
                    renderDataFromMock(MUSHAF_MOCK[mockKey]);
                } else {
                    tubuh.innerHTML = `
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-wifi-off fs-1 d-block mb-2 text-warning"></i>
                            <div class="fw-bold text-dark">Tidak Dapat Memuat Ayat Online</div>
                            <div class="small mt-1 text-secondary" style="max-width:360px; margin:0 auto;">
                                Pastikan koneksi internet Anda aktif untuk membaca mushaf Surah ${escHtml(namaSurah)}.
                            </div>
                            <button type="button" class="btn btn-sm text-white mt-3 rounded-pill px-4" style="background:#11756D;"
                                    onclick="bukaMushaf(${noSurah}, '${escHtml(namaSurah).replace(/'/g, "\\'")}', '${(arabSurah || '').replace(/'/g, "\\'")}', '${mockKey || ''}')">
                                <i class="bi bi-arrow-clockwise me-1"></i>Coba Lagi
                            </button>
                        </div>`;
                }
            });

        function renderDataMushaf(data) {
            const surahNo = data.nomor || noSurah;
            modalJudul.innerHTML = `
                <span>${escHtml(data.namaLatin || namaSurah)}</span>
                <span class="mushaf-judul-arab ms-2">${data.nama || arabSurah || ''}</span>
                <span class="badge bg-white text-dark ms-2 fw-semibold" style="font-size:12px;">QS. ${surahNo}</span>
            `;
            modalSubjudul.innerHTML = `
                <span class="text-white-50">Arti:</span> <strong class="text-white">${escHtml(data.arti || '')}</strong> &bull;
                <span class="text-white-50">Jumlah:</span> <strong class="text-white">${data.jumlahAyat || (data.ayat ? data.ayat.length : 0)} Ayat</strong> &bull;
                <span class="badge text-white border border-light-subtle px-2 py-0.5 rounded-pill" style="font-size:10.5px; background:rgba(255,255,255,0.18);">${data.tempatTurun || 'Makkiyah'}</span>
            `;

            let bismillahHtml = '';
            // Surah selain Al-Fatihah (1) dan At-Taubah (9) diberi hiasan Basmalah
            if (parseInt(surahNo) !== 1 && parseInt(surahNo) !== 9) {
                bismillahHtml = `
                    <div class="text-center py-3 mb-4 rounded-3" style="background:#FFFFFF; border:1px solid #E2E8F0; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
                        <p class="m-0" style="font-family:'Amiri', 'Traditional Arabic', serif; font-size:26px; color:#11756D; line-height:2;">
                            بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ
                        </p>
                        <span class="text-muted" style="font-size:12px;">Dengan nama Allah Yang Maha Pengasih lagi Maha Penyayang</span>
                    </div>
                `;
            }

            let opsi = '<option value="">Lompat ke Ayat...</option>';
            const ayatCards = (data.ayat || []).map(ayat => {
                const noAyat = ayat.nomorAyat;
                opsi += `<option value="${noAyat}">Ayat ${noAyat}</option>`;
                return `
                    <article class="card-ayat-mushaf" id="mushafAyat-${noAyat}">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                            <span class="nomor-ayat-mushaf" title="Ayat ke-${noAyat}">${noAyat}</span>
                            <p class="teks-arab-mushaf">${ayat.teksArab}</p>
                        </div>
                        <div class="pt-2 border-top">
                            <p class="mb-1 fw-semibold" style="color:#0D9488; font-size:14px; line-height:1.6;">${escHtml(ayat.teksLatin)}</p>
                            <p class="mb-0 text-secondary" style="font-size:14px; line-height:1.65;">${escHtml(ayat.teksIndonesia)}</p>
                        </div>
                    </article>
                `;
            }).join('');

            tubuh.innerHTML = bismillahHtml + ayatCards;
            select.innerHTML = opsi;
            select.value = '';
            tubuh.scrollTop = 0;
        }

        function renderDataFromMock(mock) {
            const surahNo = mock.no || noSurah;
            modalJudul.innerHTML = `
                <span>${escHtml(mock.nama)}</span>
                <span class="mushaf-judul-arab ms-2">${mock.arab}</span>
                <span class="badge bg-white text-dark ms-2 fw-semibold" style="font-size:12px;">QS. ${surahNo}</span>
            `;
            modalSubjudul.innerHTML = `
                <span class="text-white-50">Arti:</span> <strong class="text-white">${escHtml(mock.arti)}</strong> &bull;
                <span class="text-white-50">Jumlah:</span> <strong class="text-white">${mock.jumlah} Ayat</strong> &bull;
                <span class="badge text-white border border-light-subtle px-2 py-0.5 rounded-pill" style="font-size:10.5px; background:rgba(255,255,255,0.18);">${mock.golongan}</span>
            `;

            let bismillahHtml = '';
            if (parseInt(surahNo) !== 1 && parseInt(surahNo) !== 9) {
                bismillahHtml = `
                    <div class="text-center py-3 mb-4 rounded-3" style="background:#FFFFFF; border:1px solid #E2E8F0; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
                        <p class="m-0" style="font-family:'Amiri', 'Traditional Arabic', serif; font-size:26px; color:#11756D; line-height:2;">
                            بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ
                        </p>
                        <span class="text-muted" style="font-size:12px;">Dengan nama Allah Yang Maha Pengasih lagi Maha Penyayang</span>
                    </div>
                `;
            }

            let opsi = '<option value="">Lompat ke Ayat...</option>';
            const ayatCards = mock.ayat.map((ayat, idx) => {
                const noAyat = idx + 1;
                opsi += `<option value="${noAyat}">Ayat ${noAyat}</option>`;
                return `
                    <article class="card-ayat-mushaf" id="mushafAyat-${noAyat}">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                            <span class="nomor-ayat-mushaf" title="Ayat ke-${noAyat}">${noAyat}</span>
                            <p class="teks-arab-mushaf">${ayat.arab}</p>
                        </div>
                        <div class="pt-2 border-top">
                            <p class="mb-1 fw-semibold" style="color:#0D9488; font-size:14px; line-height:1.6;">${escHtml(ayat.latin)}</p>
                            <p class="mb-0 text-secondary" style="font-size:14px; line-height:1.65;">${escHtml(ayat.arti)}</p>
                        </div>
                    </article>
                `;
            }).join('');

            tubuh.innerHTML = bismillahHtml + ayatCards;
            select.innerHTML = opsi;
            select.value = '';
            tubuh.scrollTop = 0;
        }
    };

    // Filter lompat ayat mushaf
    document.getElementById('filterAyatMushaf')?.addEventListener('change', function () {
        const no = this.value;
        const tubuh = document.getElementById('mushafBody');
        tubuh.querySelectorAll('.card-ayat-mushaf').forEach(el => el.classList.remove('aktif'));
        if (!no) return;
        const target = document.getElementById('mushafAyat-' + no);
        if (target) {
            target.classList.add('aktif');
            tubuh.scrollTo({ top: target.offsetTop - tubuh.offsetTop - 15, behavior: 'smooth' });
        }
    });

    // ── Event: ganti pilihan Juz ─────────────────────────────────────────
    document.getElementById('selectJuzHafalan')?.addEventListener('change', function () {
        renderSurahPerJuz(this.value);
    });

    // ── Event: klik kartu siswa di panel kiri ───────────────────────────
    document.getElementById('listHafalanSiswa')?.addEventListener('click', function (e) {
        const card = e.target.closest('.hafalan-siswa-card');
        if (!card) return;

        siswaAktif = {
            id: card.getAttribute('data-id'),
            nama: card.getAttribute('data-nama'),
            nis: card.getAttribute('data-nis'),
            inisial: card.getAttribute('data-inisial') || 'S'
        };

        document.querySelectorAll('.hafalan-siswa-card').forEach(el => el.classList.remove('aktif'));
        card.classList.add('aktif');

        document.getElementById('hafalanEmpty').style.display = 'none';
        document.getElementById('hafalanSurahArea').style.display = 'block';
        document.getElementById('displayNamaSantriHafalan').textContent = siswaAktif.nama;

        const juzDipilih = document.getElementById('selectJuzHafalan')?.value || '30_dhuha';
        renderSurahPerJuz(juzDipilih);
        renderRiwayatSetoran(siswaAktif.id);
    });

    // ── Event: klik tombol baca mushaf di tabel surah ────────────────────
    document.getElementById('tbodySurahHafalan')?.addEventListener('click', function (e) {
        const btnBaca = e.target.closest('.btn-baca-mushaf');
        if (btnBaca) {
            e.stopPropagation();
            bukaMushaf(btnBaca.getAttribute('data-key'), btnBaca.getAttribute('data-nama'));
        }
    });

    // ── Validasi form sebelum submit ─────────────────────────────────────
    document.getElementById('formSetoranModal')?.addEventListener('submit', function (e) {
        const awal = parseInt(document.getElementById('modalInputAyatAwal').value, 10);
        const akhir = parseInt(document.getElementById('modalInputAyatAkhir').value, 10);
        const surah = document.getElementById('modalInputSurahNama').value.trim();
        const err = document.getElementById('modalAyatError');

        if (!surah) {
            e.preventDefault();
            err.textContent = 'Pilih surah yang disetor terlebih dahulu!';
            err.style.display = 'block';
            return false;
        }
        if (akhir < awal) {
            e.preventDefault();
            err.textContent = 'Ayat akhir tidak boleh lebih kecil dari ayat awal!';
            err.style.display = 'block';
            return false;
        }
        err.style.display = 'none';
        return true;
    });

    // ── Filter pencarian siswa panel kiri ────────────────────────────────
    document.getElementById('searchHafalanSiswa')?.addEventListener('input', function () {
        const kw = this.value.toLowerCase().trim();
        document.querySelectorAll('#listHafalanSiswa .hafalan-siswa-card').forEach(c => {
            const n = (c.getAttribute('data-nama') || '').toLowerCase();
            const s = (c.getAttribute('data-nis') || '').toLowerCase();
            c.style.display = (n.includes(kw) || s.includes(kw)) ? '' : 'none';
        });
    });

    // ── Auto-pilih siswa jika ada query siswa_id setelah simpan setoran ──
    @if($selectedSiswa)
    (function () {
        const card = document.querySelector('.hafalan-siswa-card[data-id="{{ $selectedSiswa->id }}"]');
        if (card) card.click();
    })();
    @endif

})();
</script>
@endsection
