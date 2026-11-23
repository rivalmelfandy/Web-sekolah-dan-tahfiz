@extends('layouts.admin', ['title' => 'Rekap Tahfidz'])

@section('breadcrumb', 'Rekap Tahfidz')

@section('styles')
<style>
    /* ── Header tabel hijau mint ─────────────────────────────────── */
    .tbl-rekap thead { background: #D1FAE5; }
    .tbl-rekap thead th {
        color: #065F46; letter-spacing: .045em;
        font-size: .72rem; font-weight: 700;
    }
    .tbl-rekap tbody tr:hover td { background: #F0FDF4; }

    /* ── Badge capaian ────────���──────────────────────────────────── */
    .badge-capaian {
        font-size: .72rem; font-weight: 700;
        border-radius: 99px; padding: .22em .75em;
        display: inline-block;
    }
    .badge-capaian.bg-a   { background: #D1FAE5; color: #065F46; border: 1px solid #6EE7B7; }
    .badge-capaian.bg-b   { background: #DBEAFE; color: #1D4ED8; border: 1px solid #93C5FD; }
    .badge-capaian.bg-c   { background: #FEF9C3; color: #92400E; border: 1px solid #FDE68A; }
    .badge-capaian.bg-d   { background: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5; }

    /* ── Filter bar ──────────────────────────────────────────────── */
    .filter-bar {
        background: #F0FDF4;
        border: 1px solid #A7F3D0;
        border-radius: 12px;
        padding: .9rem 1.1rem;
    }
    .form-select:focus, .form-control:focus {
        border-color: #047857;
        box-shadow: 0 0 0 3px rgba(4,120,87,.12);
    }

    /* ── Progress capaian ────────────────────────────────────────── */
    .prog-capaian { height: 7px; border-radius: 99px; background: #E5E7EB; }
    .prog-capaian-fill { height: 100%; border-radius: 99px; transition: width .3s; }

    /* ── Kartu stat ──────────────────────────────────────────────── */
    .stat-icon {
        width: 44px; height: 44px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
</style>
@endsection

@section('content')

{{-- ── Header ──────────────────────────────────────────────────────── --}}
<div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1 h4" style="color:#000000;">
            <i class="bi bi-file-earmark-bar-graph me-2"></i>Rekap Tahfidz
        </h2>
        <p class="text-muted mb-0 small">
            Pantau aktivitas setoran dan capaian hafalan siswa per kelompok halaqah.
        </p>
    </div>
</div>

@include('admin._alert')

{{-- ── 4 KARTU STAT ─────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="stat-icon" style="background: linear-gradient(135deg, #10B981, #0D9488); box-shadow: 0 4px 10px rgba(16,185,129,.25);">
                    <i class="bi bi-mortarboard-fill" style="color:#FFFFFF !important; font-size:1.1rem;"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1" style="color:#065F46;">{{ $statTotal['siswa'] }}</div>
                    <div class="text-muted small mt-1">Total Siswa</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="stat-icon" style="background: linear-gradient(135deg, #EC4899, #DB2777); box-shadow: 0 4px 10px rgba(236,72,153,.25);">
                    <i class="bi bi-collection-fill" style="color:#FFFFFF !important; font-size:1.1rem;"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1" style="color:#065F46;">{{ $statTotal['halaqah'] }}</div>
                    <div class="text-muted small mt-1">Kelompok Halaqah</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="stat-icon" style="background: linear-gradient(135deg, #3B82F6, #2563EB); box-shadow: 0 4px 10px rgba(59,130,246,.25);">
                    <i class="bi bi-journal-bookmark-fill" style="color:#FFFFFF !important; font-size:1.1rem;"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1" style="color:#065F46;">{{ $statTotal['setoran'] }}</div>
                    <div class="text-muted small mt-1">Total Setoran (12 Bln)</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="stat-icon" style="background: linear-gradient(135deg, #F97316, #EA580C); box-shadow: 0 4px 10px rgba(249,115,22,.25);">
                    <i class="bi bi-graph-up-arrow" style="color:#FFFFFF !important; font-size:1.1rem;"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1" style="color:#065F46;">
                        {{ $statTotal['rata_nilai'] }}<span class="fs-6 fw-normal text-muted">%</span>
                    </div>
                    <div class="text-muted small mt-1">Rata-Rata Capaian</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── FILTER DROPDOWN HALAQAH ─────────────────────────────────────── --}}
<div class="filter-bar mb-4 d-flex flex-wrap align-items-center gap-3">
    <i class="bi bi-funnel-fill" style="color:#047857;"></i>
    <span class="fw-semibold small" style="color:#065F46;">Filter Halaqah:</span>

    <form method="GET" action="{{ route('admin.rekap-tahfidz') }}"
          class="d-flex align-items-center gap-2 flex-wrap mb-0" id="formFilter">
        <select name="halaqah_id" id="halaqah_id"
                class="form-select form-select-sm"
                style="min-width:200px; max-width:280px;"
                onchange="this.form.submit()">
            <option value="">— Semua Halaqah —</option>
            @foreach($halaqahOptions as $h)
                <option value="{{ $h->id }}" {{ $halaqahIdFilter == $h->id ? 'selected' : '' }}>
                    [{{ $h->kode_halaqah }}] {{ $h->nama_halaqah }}
                </option>
            @endforeach
        </select>
    </form>

    @if($halaqahIdFilter)
        @php $aktif = $halaqahOptions->firstWhere('id', $halaqahIdFilter); @endphp
        <span class="badge rounded-pill fw-semibold px-3 py-1 small"
              style="background:#D1FAE5; color:#065F46; border:1px solid #6EE7B7;">
            <i class="bi bi-check-circle me-1"></i>
            {{ $aktif?->nama_halaqah ?? 'Halaqah #'.$halaqahIdFilter }}
        </span>
        <a href="{{ route('admin.rekap-tahfidz') }}"
           class="btn btn-sm fw-semibold"
           style="background:transparent; border:1px solid #DC2626; color:#DC2626; border-radius:8px; font-size:.78rem;">
            <i class="bi bi-x-circle me-1"></i>Reset Filter
        </a>
    @endif
</div>

{{-- ── GRAFIK BULANAN ───────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
            <h3 class="fw-bold mb-0 h5" style="color:#000000;">
                <i class="bi bi-bar-chart-fill me-2" style="color:#000000;"></i>Aktivitas Setoran Bulanan
            </h3>
            <span class="text-muted small">12 Bulan Terakhir</span>
        </div>

        @if(array_sum($grafikSetoran) === 0)
            <div class="text-center py-5 text-muted">
                <i class="bi bi-bar-chart display-5 d-block mb-2 opacity-25"></i>
                <p class="mb-0 small">Belum ada data setoran pada periode ini.</p>
            </div>
        @else
            <div style="position:relative; height:240px;">
                <canvas id="grafikBulanan"></canvas>
            </div>
        @endif
    </div>
</div>

{{-- ── TABEL SISWA ──────────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h3 class="fw-bold mb-0 h5" style="color:#000000;">
            <i class="bi bi-people-fill me-2" style="color:#000000;"></i>Rekap Capaian Siswa
        </h3>
        <span class="text-muted small">{{ $siswaList->count() }} siswa</span>
    </div>
    <div class="card-body p-0">
        @if($siswaList->isEmpty())
            <div class="text-center py-5 text-muted px-4">
                <i class="bi bi-people display-5 d-block mb-2 opacity-25"></i>
                <p class="mb-0 small">Belum ada data siswa
                    {{ $halaqahIdFilter ? 'di halaqah ini.' : 'yang terdaftar.' }}
                </p>
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 tbl-rekap">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-uppercase" style="color:#000000;">No</th>
                        <th class="px-3 py-3 text-uppercase" style="color:#000000;">Nama Siswa</th>
                        <th class="px-3 py-3 text-uppercase" style="color:#000000;">Nama Halaqah</th>
                        <th class="px-3 py-3 text-uppercase" style="color:#000000;">Target Aktif</th>
                        <th class="px-3 py-3 text-uppercase" style="color:#000000;">Hafalan Terakhir</th>
                        <th class="px-3 py-3 text-uppercase text-center" style="color:#000000; min-width:140px;">Capaian</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($siswaList as $i => $s)
                    <tr>
                        {{-- # --}}
                        <td class="px-4 py-3 text-muted small">{{ $i + 1 }}</td>

                        {{-- Nama Siswa --}}
                        <td class="px-3 py-3">
                            <div class="fw-semibold small" style="color:#000000;">{{ $s['nama_lengkap'] }}</div>
                        </td>

                        {{-- Nama Halaqah --}}
                        <td class="px-3 py-3 small text-muted">
                            {{ $s['nama_halaqah'] }}
                        </td>

                        {{-- Target Aktif --}}
                        <td class="px-3 py-3 small">
                            @if($s['target_aktif'])
                                <div class="fw-semibold" style="color:#1F4172; font-size:.8rem;">
                                    {{ $s['target_aktif']['surah_awal'] }}
                                    <span class="text-muted fw-normal">s/d</span>
                                    {{ $s['target_aktif']['surah_akhir'] }}
                                </div>
                                <div class="text-muted" style="font-size:.7rem;">
                                    {{ $s['target_aktif']['tanggal_mulai'] }} –
                                    {{ $s['target_aktif']['tanggal_akhir'] }}
                                </div>
                            @else
                                <span class="text-muted small">Belum ada target</span>
                            @endif
                        </td>

                        {{-- Hafalan Terakhir --}}
                        <td class="px-3 py-3 small">
                            @if($s['hafalan_terakhir'])
                                <div class="fw-semibold" style="color:#1F4172; font-size:.8rem;">
                                    {{ $s['hafalan_terakhir']['surah'] }}
                                    <span class="text-muted fw-normal">
                                        ({{ $s['hafalan_terakhir']['ayat_awal'] }}–{{ $s['hafalan_terakhir']['ayat_akhir'] }})
                                    </span>
                                </div>
                                <div class="text-muted" style="font-size:.7rem;">
                                    {{ $s['hafalan_terakhir']['tanggal'] }} &middot;
                                    Nilai: <strong>{{ $s['hafalan_terakhir']['nilai'] }}</strong>
                                </div>
                            @else
                                <span class="text-muted small">Belum ada setoran</span>
                            @endif
                        </td>

                        {{-- Persentase Capaian --}}
                        <td class="px-3 py-3 text-center">
                            @php
                                $pct = $s['persen_capaian'];
                                if ($pct >= 80)      { $kls = 'bg-a'; }
                                elseif ($pct >= 70)  { $kls = 'bg-b'; }
                                elseif ($pct >= 60)  { $kls = 'bg-c'; }
                                else                 { $kls = 'bg-d'; }
                                $barColor = $pct >= 80 ? '#047857' : ($pct >= 60 ? '#1D4ED8' : ($pct >= 50 ? '#D97706' : '#DC2626'));
                            @endphp
                            <div class="d-flex flex-column align-items-center gap-1">
                                <span class="badge-capaian {{ $kls }}">{{ $pct }}%</span>
                                <div class="prog-capaian w-100" style="max-width:90px;">
                                    <div class="prog-capaian-fill"
                                         style="width:{{ $pct }}%; background:{{ $barColor }};"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

@endsection

{{-- ── CHART.JS ─────────────────────────────────────────────────────── --}}
@if(array_sum($grafikSetoran) > 0)
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"
        integrity="sha256-oCV/FBfCMvjSHxNE5QRNEFkLXBkMFHzYLBolbSEfHTg=" crossorigin="anonymous"></script>
<script>
(function () {
    const labels  = @json($grafikLabels);
    const setoran = @json($grafikSetoran);

    new Chart(document.getElementById('grafikBulanan'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Jumlah Setoran',
                data: setoran,
                backgroundColor: 'rgba(17,122,116,.75)',
                borderRadius: 7,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.parsed.y} setoran`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: { size: 11 },
                        color: '#64748B',
                        callback: v => Number.isInteger(v) ? v : ''
                    },
                    grid: { color: 'rgba(0,0,0,.05)' }
                },
                x: {
                    ticks: { font: { size: 11 }, color: '#64748B' },
                    grid: { display: false }
                }
            }
        }
    });
})();
</script>
@endsection
@endif
