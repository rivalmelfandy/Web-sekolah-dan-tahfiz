@extends('layouts.ustadz', ['title' => 'Evaluasi Murajaah'])

@section('breadcrumb', 'Murajaah')

@section('content')

{{-- ── Notifikasi Alert ──────────────────────────────────────────────── --}}
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

{{-- ── Header Banner ──────────────────────────────────────────────────── --}}
<div class="mb-4">
    <span class="badge px-3 py-1 text-white mb-2 fw-bold" style="background-color: #11756D;">PENGUATAN HAFALAN</span>
    <h2 class="fw-bold mb-1" style="color: #1E293B; font-size: 1.65rem;">Evaluasi Murajaah Siswa</h2>
    <p class="text-muted small mb-0">Uji dan perkuat hafalan lama siswa binaan agar tetap mutqin dan terjaga kelancarannya.</p>
</div>

{{-- ── Layout 2 Kolom (Identik dengan Tab Hafalan & RPLBO) ─────────────── --}}
<div class="hafalan-layout">

    {{-- ══ PANEL KIRI: DAFTAR PILIH SISWA ══════════════════════════════════ --}}
    <section class="hafalan-panel hafalan-panel-siswa" aria-label="Pilih siswa murajaah">
        <h2 class="hafalan-panel-title">
            <i class="bi bi-person"></i>
            <span>Pilih Siswa ({{ $santriList->count() }})</span>
        </h2>
        <div class="hafalan-search-wrap">
            <i class="bi bi-search"></i>
            <input type="search" id="searchMurajaahSiswa" placeholder="Cari nama atau NIS..." autocomplete="off">
        </div>
        <div id="listMurajaahSiswa">
            @forelse($santriList as $s)
                @php
                    $kata = explode(' ', trim($s->nama_lengkap));
                    $inisial = strtoupper(substr($kata[0], 0, 1));
                    if (isset($kata[1])) $inisial .= strtoupper(substr($kata[1], 0, 1));
                @endphp
                <button type="button"
                        class="hafalan-siswa-card"
                        data-id="{{ $s->id }}"
                        data-nama="{{ $s->nama_lengkap }}"
                        data-nis="{{ $s->nip_nis }}"
                        data-inisial="{{ $inisial }}"
                        data-search-nama="{{ strtolower($s->nama_lengkap) }}"
                        data-search-nis="{{ strtolower($s->nip_nis) }}"
                        onclick="pilihSantriMurajaah(this)">
                    <span class="hafalan-avatar" aria-hidden="true">{{ $inisial }}</span>
                    <span class="hafalan-siswa-meta">
                        <span class="hafalan-siswa-nama text-truncate">{{ $s->nama_lengkap }}</span>
                        <span class="hafalan-siswa-nis">NIS: {{ $s->nip_nis }}</span>
                    </span>
                </button>
            @empty
                <div class="text-center text-muted small py-4">
                    <i class="bi bi-people" style="font-size:2rem;opacity:.35;"></i>
                    <p class="mt-2 mb-0">Belum ada siswa di halaqah Anda.</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- ══ PANEL KANAN: RIWAYAT SETORAN & EVALUASI MURAJAAH ══════════════ --}}
    <section class="hafalan-panel hafalan-panel-surah" aria-label="Riwayat setoran dan evaluasi murajaah" id="panelKananMurajaah">

        {{-- State kosong --}}
        <div id="murajaahEmpty" class="hafalan-empty">
            <p>Pilih siswa di panel kiri untuk melihat riwayat setoran dan menambahkan evaluasi murajaah.</p>
        </div>

        {{-- State aktif (muncul setelah siswa dipilih) --}}
        <div id="murajaahRiwayatArea" hidden>

            {{-- Header panel kanan --}}
            <div class="hafalan-surah-head">
                <div>
                    <h2>Riwayat Setoran &amp; Evaluasi Murajaah</h2>
                    <p>Pilih baris setoran untuk memperbaiki kualitas hafalan melalui murajaah</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button"
                            class="btn btn-sm text-white fw-semibold rounded-pill px-3 shadow-sm"
                            style="background:#11756D; font-size:12.5px;"
                            onclick="openModalMurajaahBebas()">
                        <i class="bi bi-journal-plus me-1"></i>Murajaah Mandiri
                    </button>
                    <span class="hafalan-santri-pill" id="badgeSantriMurajaah">Siswa: –</span>
                </div>
            </div>

            {{-- Tabel 1: Riwayat Setoran yang bisa di-evaluasi --}}
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold" style="font-size:14px; color:#1E293B;">
                        <i class="bi bi-list-check me-1" style="color:#11756D;"></i>Daftar Setoran Santri
                    </span>
                    <span class="text-muted" style="font-size:12px;" id="infoJumlahSetoran">Menampilkan 0 setoran</span>
                </div>
                <div class="table-responsive" style="max-height: 320px; overflow-y: auto; border: 1px solid #F1F5F9; border-radius: 10px;">
                    <table class="hafalan-table mb-0">
                        <thead style="position: sticky; top: 0; background: #FFFFFF; z-index: 2;">
                            <tr>
                                <th style="width:110px;">Tanggal</th>
                                <th>Nama Surah</th>
                                <th style="width:140px;">Rentang Ayat</th>
                                <th style="width:150px;">Kualitas Setor</th>
                                <th class="col-aksi">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tabelSetoranMurajaah">
                            <tr><td colspan="5" class="text-center text-muted small py-3">Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end align-items-center mt-2" style="font-size:12px; color:#94A3B8;">
                    <span>Tekan tombol <strong style="color:#11756D;">+</strong> pada baris setoran untuk mengevaluasi murajaah</span>
                </div>
            </div>

            {{-- Tabel 2: Riwayat Hasil Evaluasi Murajaah --}}
            <div class="pt-3 border-top">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold" style="font-size:14px; color:#1E293B;">
                        <i class="bi bi-patch-check-fill me-1" style="color:#11756D;"></i>Riwayat Hasil Evaluasi Murajaah
                    </span>
                    <span class="text-muted" style="font-size:12px;" id="labelJmlMurajaah">(0 evaluasi)</span>
                </div>
                <div class="table-responsive" style="max-height: 260px; overflow-y: auto; border: 1px solid #F1F5F9; border-radius: 10px;">
                    <table class="hafalan-table mb-0">
                        <thead style="position: sticky; top: 0; background: #FFFFFF; z-index: 2;">
                            <tr>
                                <th style="width:40px;">No</th>
                                <th style="width:110px;">Tanggal</th>
                                <th>Surah &amp; Rentang</th>
                                <th style="width:150px;">Hasil Evaluasi</th>
                                <th>Catatan Ustadz</th>
                            </tr>
                        </thead>
                        <tbody id="tabelRiwayatMurajaah">
                            <tr><td colspan="5" class="text-center text-muted small py-3">Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>{{-- /murajaahRiwayatArea --}}
    </section>

</div>{{-- /hafalan-layout --}}

{{-- ══ MODAL: LEMBAR EVALUASI MURAJAAH ══════════════════════════════════════ --}}
<div class="modal fade" id="modalEvaluasiMurajaah" tabindex="-1" aria-labelledby="modalEvaluasiMurajaahLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 540px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form method="POST" action="{{ route('ustadz.murajaah.store') }}">
                @csrf
                <input type="hidden" name="siswa_id" id="modalMurajaahSiswaId">
                <input type="hidden" name="setoran_id" id="modalSetoranId">

                {{-- Header Gradient Elegan --}}
                <div class="modal-header border-0 text-white py-3 px-4"
                     style="background: linear-gradient(135deg, #0A2E2B 0%, #11756D 100%);">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                             style="background: rgba(255,255,255,0.15); width: 42px; height: 42px;">
                            <i class="bi bi-patch-check fs-4 text-warning"></i>
                        </div>
                        <div>
                            <span class="badge bg-warning text-dark fw-bold px-2 py-0.5 mb-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                EVALUASI PENGUATAN HAFALAN
                            </span>
                            <h5 class="modal-title fw-bold mb-0 text-white" id="modalEvaluasiMurajaahLabel">
                                Lembar Evaluasi Murajaah
                            </h5>
                            <div class="small text-white-50 mt-0.5">
                                Santri: <span id="modalNamaSantriMurajaah" class="fw-semibold text-white">-</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body p-4">
                    {{-- Nama Surah --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Nama Surah <span class="text-danger">*</span></label>
                        <input type="text" name="surah" id="modalSurah" class="form-control"
                               placeholder="Pilih atau ketik nama surah..." required
                               style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px; height:42px; font-size:14px;"
                               list="datalistSurahMurajaah">
                        <datalist id="datalistSurahMurajaah">
                            @foreach($surahList as $sr)
                                <option value="{{ $sr['nama'] }}">
                            @endforeach
                        </datalist>
                    </div>

                    {{-- Rentang Ayat --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Rentang Ayat <span class="text-danger">*</span></label>
                        <input type="text" name="ayat_range" id="modalAyatRange" class="form-control"
                               placeholder="Contoh: Ayat 1–20 atau Seluruh Ayat" required
                               style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px; height:42px; font-size:14px;">
                    </div>

                    {{-- Nilai Murajaah --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Nilai Murajaah (0–100) <span class="text-danger">*</span></label>
                        <input type="number" name="nilai_murajaah" id="modalNilaiMurajaah" class="form-control"
                               value="80" min="0" max="100" required
                               style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px; height:42px; font-size:14px;">
                    </div>

                    {{-- Tanggal Murajaah --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Tanggal Evaluasi <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_murajaah" class="form-control"
                               value="{{ date('Y-m-d') }}" required
                               style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px; height:42px; font-size:14px;">
                    </div>

                    {{-- Catatan Ustadz --}}
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-dark">Catatan Ustadz (Opsional)</label>
                        <textarea name="catatan" rows="3" class="form-control"
                                  placeholder="Catatan makhraj, tajwid, kelancaran sambungan ayat..."
                                  style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px; font-size:13.5px;"></textarea>
                    </div>
                </div>

                <div class="modal-footer px-4 py-3 bg-light border-0 d-flex gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold text-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn text-white rounded-pill px-4 fw-bold ms-auto"
                            style="background:#11756D;">
                        <i class="bi bi-save me-1"></i>Simpan Evaluasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// ── Data dari DB (di-encode controller) ─────────────────────────────────────
const SETORAN_PER_SISWA  = @json($setoranPerSiswa);
const MURAJAAH_PER_SISWA = @json($murajaahPerSiswa);

// ── State aktif ──────────────────────────────────────────────────────────────
let aktivSiswaId = null;

// ── Helper: format tanggal ───────────────────────────────────────────────────
function fmtTgl(str) {
    if (!str) return '-';
    const d = new Date(str);
    if (isNaN(d)) return str;
    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}

// ── Helper: badge nilai/indeks ───────────────────────────────────────────────
function badgeNilai(nilai) {
    const n = parseInt(nilai);
    if (isNaN(n)) return `<span class="hafalan-badge hafalan-badge-belum">${nilai || '-'}</span>`;
    if (n >= 90) return `<span class="hafalan-badge hafalan-badge-selesai"><i class="bi bi-check-circle-fill me-1"></i>A (${n})</span>`;
    if (n >= 80) return `<span class="hafalan-badge hafalan-badge-selesai"><i class="bi bi-check-circle-fill me-1"></i>AB (${n})</span>`;
    if (n >= 70) return `<span class="hafalan-badge hafalan-badge-berjalan"><i class="bi bi-clock-history me-1"></i>B (${n})</span>`;
    if (n >= 60) return `<span class="hafalan-badge hafalan-badge-berjalan"><i class="bi bi-clock-history me-1"></i>BC (${n})</span>`;
    if (n >= 50) return `<span class="hafalan-badge" style="background:#FEF9C3; color:#B45309;"><i class="bi bi-dash-circle me-1"></i>C (${n})</span>`;
    return `<span class="hafalan-badge" style="background:#FEE2E2; color:#B91C1C;"><i class="bi bi-x-circle-fill me-1"></i>D/E (${n})</span>`;
}

// ── Pilih santri ─────────────────────────────────────────────────────────────
function pilihSantriMurajaah(btn) {
    // Hapus highlight lama
    document.querySelectorAll('#listMurajaahSiswa .hafalan-siswa-card').forEach(c => {
        c.classList.remove('aktif', 'active');
    });

    // Highlight yang dipilih
    btn.classList.add('aktif', 'active');

    const id      = parseInt(btn.dataset.id);
    const nama    = btn.dataset.nama;
    const nis     = btn.dataset.nis;
    aktivSiswaId  = id;

    // Update header panel kanan
    document.getElementById('badgeSantriMurajaah').textContent = 'Siswa: ' + nama;

    // Render tabel setoran & riwayat murajaah
    renderTabelSetoran(id);
    renderTabelMurajaah(id);

    // Tampilkan panel kanan (sembunyikan empty state tanpa konflik CSS)
    document.getElementById('murajaahEmpty').hidden       = true;
    document.getElementById('murajaahRiwayatArea').hidden = false;
}

// ── Render tabel setoran yang bisa di-murajaah ───────────────────────────────
function renderTabelSetoran(siswaId) {
    const tbody  = document.getElementById('tabelSetoranMurajaah');
    const label  = document.getElementById('infoJumlahSetoran');
    const list   = SETORAN_PER_SISWA[siswaId] ?? [];
    label.textContent = `Menampilkan ${list.length} setoran`;

    if (!list.length) {
        tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted small py-4">
            <i class="bi bi-inbox" style="font-size:1.75rem;opacity:.35;"></i>
            <div class="mt-2">Belum ada setoran hafalan untuk siswa ini.</div>
        </td></tr>`;
        return;
    }

    tbody.innerHTML = list.map((s, i) => {
        const ayat = `Ayat ${s.ayat_awal}–${s.ayat_akhir}`;
        return `<tr>
            <td style="color:#64748B; font-size:13px;">${fmtTgl(s.tanggal_setor)}</td>
            <td>
                <span class="fw-bold text-dark" style="font-size:13.5px;">${s.surah ?? '-'}</span>
            </td>
            <td style="color:#64748B; font-size:13px;">${ayat}</td>
            <td>${badgeNilai(s.nilai_hafalan ?? '-')}</td>
            <td class="col-aksi">
                <button type="button" class="btn-setoran-plus"
                        title="Evaluasi Murajaah ${s.surah}"
                        onclick="openModalEvaluasiSetoran(${s.id ?? 0}, '${(s.surah ?? '').replace(/'/g,"\\'")}', '${ayat.replace(/'/g,"\\'")}')">
                    <i class="bi bi-plus-lg"></i>
                </button>
            </td>
        </tr>`;
    }).join('');
}

// ── Render tabel riwayat hasil evaluasi murajaah ─────────────────────────────
function renderTabelMurajaah(siswaId) {
    const tbody = document.getElementById('tabelRiwayatMurajaah');
    const label = document.getElementById('labelJmlMurajaah');
    const list  = MURAJAAH_PER_SISWA[siswaId] ?? [];
    label.textContent = `(${list.length} evaluasi)`;

    if (!list.length) {
        tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted small py-4">
            <i class="bi bi-journal-x" style="font-size:1.75rem;opacity:.35;"></i>
            <div class="mt-2">Belum ada catatan evaluasi murajaah untuk siswa ini.</div>
        </td></tr>`;
        return;
    }

    tbody.innerHTML = list.map((m, i) => {
        return `<tr>
            <td style="color:#94A3B8; font-size:13px;">${i + 1}.</td>
            <td style="color:#64748B; font-size:13px;">${fmtTgl(m.tanggal_murajaah)}</td>
            <td>
                <span class="fw-semibold text-dark">${m.surah ?? '-'}</span>
                <span class="text-muted small ms-1">(${m.ayat_range ?? ''})</span>
            </td>
            <td>${badgeNilai(m.nilai_murajaah ?? '-')}</td>
            <td class="text-muted small">${m.catatan ? m.catatan : '–'}</td>
        </tr>`;
    }).join('');
}

// ── Buka modal dari baris setoran ────────────────────────────────────────────
function openModalEvaluasiSetoran(setoranId, surah, ayatRange) {
    document.getElementById('modalSetoranId').value         = setoranId;
    document.getElementById('modalSurah').value             = surah;
    document.getElementById('modalAyatRange').value         = ayatRange;
    document.getElementById('modalMurajaahSiswaId').value   = aktivSiswaId ?? '';
    
    const siswaCard = document.querySelector(`#listMurajaahSiswa .hafalan-siswa-card[data-id="${aktivSiswaId}"]`);
    const namaSiswa = siswaCard ? siswaCard.dataset.nama : 'Siswa';
    document.getElementById('modalNamaSantriMurajaah').textContent = namaSiswa;

    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEvaluasiMurajaah')).show();
}

// ── Buka modal murajaah mandiri (tanpa id setoran) ────────────────────────────
function openModalMurajaahBebas() {
    document.getElementById('modalSetoranId').value         = '';
    document.getElementById('modalSurah').value             = '';
    document.getElementById('modalAyatRange').value         = '';
    document.getElementById('modalMurajaahSiswaId').value   = aktivSiswaId ?? '';

    const siswaCard = document.querySelector(`#listMurajaahSiswa .hafalan-siswa-card[data-id="${aktivSiswaId}"]`);
    const namaSiswa = siswaCard ? siswaCard.dataset.nama : 'Siswa';
    document.getElementById('modalNamaSantriMurajaah').textContent = namaSiswa;

    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEvaluasiMurajaah')).show();
}

// ── Filter pencarian panel kiri ──────────────────────────────────────────────
document.getElementById('searchMurajaahSiswa')?.addEventListener('input', function() {
    const kw = this.value.toLowerCase().trim();
    document.querySelectorAll('#listMurajaahSiswa .hafalan-siswa-card').forEach(card => {
        const nama = card.dataset.searchNama ?? '';
        const nis  = card.dataset.searchNis  ?? '';
        card.style.display = (nama.includes(kw) || nis.includes(kw)) ? '' : 'none';
    });
});

// ── Auto-pilih siswa setelah redirect dari store ──────────────────────────────
@if($selectedSiswa)
document.addEventListener('DOMContentLoaded', function() {
    const targetBtn = document.querySelector(
        '#listMurajaahSiswa .hafalan-siswa-card[data-id="{{ $selectedSiswa->id }}"]'
    );
    if (targetBtn) {
        targetBtn.click();
        setTimeout(() => targetBtn.scrollIntoView({ behavior: 'smooth', block: 'nearest' }), 100);
    }
});
@endif
</script>
@endsection
