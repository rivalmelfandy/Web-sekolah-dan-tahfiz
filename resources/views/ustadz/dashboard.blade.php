@extends('layouts.ustadz', ['title' => 'Rekap Tahfiz Halaqah'])

@section('breadcrumb', 'Rekap Halaqah')

@section('content')

{{-- ── Alert Notifikasi ─────────────────────────────────────────────── --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible d-flex align-items-center gap-2 py-2 mb-4" role="alert">
        <i class="bi bi-check-circle-fill flex-shrink-0"></i>
        <span class="small">{{ session('success') }}</span>
        <button type="button" class="btn-close btn-sm ms-auto" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible d-flex align-items-center gap-2 py-2 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
        <span class="small">{{ session('error') }}</span>
        <button type="button" class="btn-close btn-sm ms-auto" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ── Badge & Header Halaqah ────────────────────────────────────────── --}}
<div class="mb-4">
    <span class="badge px-3 py-2 fw-bold rounded-pill text-white"
          style="background-color: #117A74; font-size: 11px; letter-spacing: 0.05em;">
        REKAPITULASI HALAQAH {{ $halaqah ? strtoupper($halaqah->nama_halaqah) : 'BINAAN' }}
    </span>
    <h2 class="fw-bold mt-2 mb-1" style="color: #1E293B; font-size: 1.65rem;">Rekap Tahfiz Siswa Binaan</h2>
    <p class="text-muted mb-0 small">
        Pembimbing: <strong>{{ $user->nama_lengkap }}</strong>
        @if($halaqah)
            &bull; Target: <span class="badge bg-light text-dark border">{{ $halaqah->target_juz }}</span>
        @endif
    </p>
</div>

{{-- ── 4 KPI Cards (RPLBO Pattern) ──────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-sm-6">
        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:16px; padding:20px 22px; box-shadow:0 2px 10px rgba(0,0,0,0.03); border-left:4px solid #117A74;">
            <div class="text-uppercase fw-bold text-muted" style="font-size:11px; letter-spacing:0.05em;">Siswa Binaan</div>
            <div class="fw-bold mt-1" style="font-size:2rem; color:#1E293B;">{{ $totalSantri }}</div>
            <div class="text-muted" style="font-size:12.5px;">Siswa dalam halaqah</div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6">
        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:16px; padding:20px 22px; box-shadow:0 2px 10px rgba(0,0,0,0.03); border-left:4px solid #117A74;">
            <div class="text-uppercase fw-bold text-muted" style="font-size:11px; letter-spacing:0.05em;">Setoran Tercatat</div>
            <div class="fw-bold mt-1" style="font-size:2rem; color:#1E293B;">{{ $totalSetoran }}</div>
            <div class="text-muted" style="font-size:12.5px;">Total riwayat setoran</div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6">
        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:16px; padding:20px 22px; box-shadow:0 2px 10px rgba(0,0,0,0.03); border-left:4px solid #117A74;">
            <div class="text-uppercase fw-bold text-muted" style="font-size:11px; letter-spacing:0.05em;">Total Murajaah</div>
            <div class="fw-bold mt-1" style="font-size:2rem; color:#1E293B;">{{ $totalMurajaah }}</div>
            <div class="text-muted" style="font-size:12.5px;">Sesi pengulangan mutqin</div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6">
        <div style="background:#fff; border:1px solid #E2E8F0; border-radius:16px; padding:20px 22px; box-shadow:0 2px 10px rgba(0,0,0,0.03); border-left:4px solid #117A74;">
            <div class="text-uppercase fw-bold text-muted" style="font-size:11px; letter-spacing:0.05em;">Tingkat Kelancaran</div>
            <div class="fw-bold mt-1" style="font-size:2rem; color:#117A74;">{{ $persentaseKelancaran }}%</div>
            <div class="text-muted" style="font-size:12.5px;">Setoran berstatus Lancar</div>
        </div>
    </div>
</div>

{{-- ── Tabel Daftar Siswa & Rekap Capaian ───────────────────────────── --}}
<div style="background:#fff; border:1px solid #E2E8F0; border-radius:16px; box-shadow:0 2px 10px rgba(0,0,0,0.03); overflow:hidden;">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 px-4 py-3" style="border-bottom:1px solid #F1F5F9;">
        <h5 class="fw-bold mb-0" style="color:#1E293B; font-size:16px;">
            <i class="bi bi-people-fill me-2" style="color:#117A74;"></i>Daftar Siswa &amp; Rekap Capaian
        </h5>
        <div class="d-flex align-items-center gap-3">
            <span class="badge rounded-pill fw-semibold" id="badgeJumlahSiswa"
                  style="background:#E6F4F1; color:#117A74; font-size:12px; padding:6px 14px;">
                {{ $totalSantri }} Siswa Terdaftar
            </span>
            <div class="position-relative">
                <i class="bi bi-search position-absolute" style="left:12px; top:50%; transform:translateY(-50%); color:#94A3B8; font-size:14px;"></i>
                <input type="text" id="searchSiswaInput" class="form-control form-control-sm"
                       placeholder="Cari NIS atau nama..."
                       style="padding-left:36px; border-radius:10px; border:1px solid #E2E8F0; min-width:220px; font-size:13px; height:38px;">
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="tabelRekapSiswa">
            <thead style="background:#F8FAFC;">
                <tr style="font-size:12px; color:#64748B; text-transform:uppercase; letter-spacing:0.04em;">
                    <th class="ps-4 py-3 fw-semibold" style="width:50px;">No</th>
                    <th class="py-3 fw-semibold" style="width:110px;">NIS</th>
                    <th class="py-3 fw-semibold">Nama Siswa</th>
                    <th class="py-3 fw-semibold" style="width:240px;">Persentase Kelancaran</th>
                    <th class="py-3 fw-semibold" style="width:190px;">Setoran Terakhir</th>
                    <th class="py-3 pe-4 fw-semibold text-center" style="width:160px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekapSantri as $idx => $s)
                    <tr class="baris-siswa">
                        <td class="ps-4 text-muted small">{{ $idx + 1 }}</td>
                        <td class="small fw-semibold text-secondary nis-cell">{{ $s->nip_nis }}</td>
                        <td>
                            <div class="fw-bold text-dark nama-cell" style="font-size:14px;">{{ $s->nama_lengkap }}</div>
                            <div class="text-muted small" style="font-size:11.5px;">{{ $s->total_setoran }} setoran &bull; {{ $s->total_murajaah }} murajaah</div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height:7px; border-radius:99px; background:#F1F5F9;">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{ $s->capaian }}%; background-color:#117A74;"
                                         aria-valuenow="{{ $s->capaian }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="fw-semibold small" style="width:36px; text-align:right; font-size:12px; color:#1E293B;">{{ $s->capaian }}%</span>
                            </div>
                        </td>
                        <td>
                            <div class="small fw-semibold text-dark">{{ $s->tanggal_terakhir }}</div>
                            <div class="text-muted text-truncate" style="font-size:11.5px; max-width:180px;">{{ $s->surah_terakhir }}</div>
                        </td>
                        <td class="pe-4 text-center">
                            <button type="button" class="btn btn-sm text-white px-3 rounded-pill fw-semibold"
                                    style="background:#117A74; font-size:12px;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalRiwayat{{ $s->id }}">
                                <i class="bi bi-clock-history me-1"></i>Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted small">
                            <i class="bi bi-people fs-2 d-block mb-2 text-secondary"></i>
                            Belum ada santri yang terdaftar dalam halaqah ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ── Modal Riwayat per Siswa ─────────────────────────────────────── --}}
@foreach($rekapSantri as $s)
    <div class="modal fade" id="modalRiwayat{{ $s->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header text-white rounded-top-4" style="background:#117A74;">
                    <div>
                        <h6 class="modal-title fw-bold mb-0">Riwayat Tahfiz: {{ $s->nama_lengkap }}</h6>
                        <small class="opacity-75">NIS: {{ $s->nip_nis }}</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    {{-- 5 Setoran Terakhir --}}
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0 text-dark" style="font-size:13.5px;">
                                <i class="bi bi-journal-check text-success me-1"></i>5 Setoran Hafalan Terbaru
                            </h6>
                            <a href="{{ route('ustadz.hafalan.index', ['siswa_id' => $s->id]) }}"
                               class="text-decoration-none small fw-semibold" style="color:#117A74; font-size:12px;">
                                + Input Setoran Baru
                            </a>
                        </div>
                        @if($s->riwayat_setoran->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered align-middle mb-0" style="font-size:12.5px;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Surah &amp; Ayat</th>
                                            <th>Kualitas</th>
                                            <th>Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($s->riwayat_setoran as $rSetor)
                                            <tr>
                                                <td class="text-nowrap">{{ $rSetor->tanggal_setor?->translatedFormat('d M Y') }}</td>
                                                <td class="fw-semibold">{{ $rSetor->surah }} (Ayat {{ $rSetor->ayat_awal }}–{{ $rSetor->ayat_akhir }})</td>
                                                <td>
                                                    @if($rSetor->kualitas?->value === 'Lancar')
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Lancar</span>
                                                    @elseif($rSetor->kualitas?->value === 'Kurang Lancar')
                                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Kurang Lancar</span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Tidak Lancar</span>
                                                    @endif
                                                </td>
                                                <td class="text-muted">{{ $rSetor->catatan ?: '–' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-light border text-center small text-muted py-3 mb-0">
                                Belum ada riwayat setoran hafalan.
                            </div>
                        @endif
                    </div>

                    {{-- 5 Murajaah Terakhir --}}
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0 text-dark" style="font-size:13.5px;">
                                <i class="bi bi-arrow-repeat text-primary me-1"></i>5 Murajaah Terbaru
                            </h6>
                            <a href="{{ route('ustadz.murajaah.index', ['siswa_id' => $s->id]) }}"
                               class="text-decoration-none small fw-semibold" style="color:#117A74; font-size:12px;">
                                + Evaluasi Murajaah
                            </a>
                        </div>
                        @if($s->riwayat_murajaah->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered align-middle mb-0" style="font-size:12.5px;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Surah &amp; Rentang</th>
                                            <th>Status Mutqin</th>
                                            <th>Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($s->riwayat_murajaah as $rMur)
                                            <tr>
                                                <td class="text-nowrap">{{ $rMur->tanggal_murajaah?->translatedFormat('d M Y') }}</td>
                                                <td class="fw-semibold">{{ $rMur->surah }} ({{ $rMur->ayat_range }})</td>
                                                <td>
                                                    @if($rMur->status_mutqin?->value === 'Lancar')
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Lancar</span>
                                                    @elseif($rMur->status_mutqin?->value === 'Kurang Lancar')
                                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Kurang Lancar</span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Tidak Lancar</span>
                                                    @endif
                                                </td>
                                                <td class="text-muted">{{ $rMur->catatan ?: '–' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-light border text-center small text-muted py-3 mb-0">
                                Belum ada riwayat murajaah.
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endforeach

@endsection

@section('scripts')
<script>
// Filter pencarian santri real-time di tabel rekap
document.getElementById('searchSiswaInput')?.addEventListener('input', function() {
    const keyword = this.value.toLowerCase().trim();
    const rows = document.querySelectorAll('#tabelRekapSiswa tbody tr.baris-siswa');
    let visibleCount = 0;

    rows.forEach(row => {
        const nis = row.querySelector('.nis-cell')?.textContent.toLowerCase() || '';
        const nama = row.querySelector('.nama-cell')?.textContent.toLowerCase() || '';
        if (nis.includes(keyword) || nama.includes(keyword)) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    const badge = document.getElementById('badgeJumlahSiswa');
    if (badge) {
        badge.textContent = visibleCount + ' Siswa Ditampilkan';
    }
});
</script>
@endsection
