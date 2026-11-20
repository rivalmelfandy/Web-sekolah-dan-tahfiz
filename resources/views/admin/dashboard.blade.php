@extends('layouts.admin', ['title' => 'Dashboard Rekap'])

@section('breadcrumb', 'Dashboard Rekap')

@section('content')

{{-- ── HERO ───────────────────────────────────────────────────────── --}}
<div class="rounded-4 p-4 p-md-5 text-white shadow-sm mb-4"
     style="background: linear-gradient(135deg, #0D6B65 0%, #117A74 60%, #138C85 100%);">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h1 class="fw-bold mb-1 h3">Assalamu'alaikum, {{ $user->nama_lengkap }}!</h1>
            <p class="mb-0 opacity-75 small">Pantau perkembangan hafalan dan aktivitas halaqah binaan SMA IT Al-Fadhilah.</p>
        </div>
        <span class="badge bg-white bg-opacity-20 text-white px-3 py-2 rounded-pill small">
            <i class="bi bi-calendar3 me-1"></i>Periode: {{ $periodeLabel }}
        </span>
    </div>
</div>

@include('admin._alert')

{{-- ── 4 KARTU RINGKASAN ───────────────────────────────────────────── --}}
<div class="row g-3 mb-4">

    {{-- Total Siswa dalam Halaqah --}}
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center"
                         style="width:46px; height:46px; background:#E8F5F4;">
                        <i class="bi bi-mortarboard-fill fs-5" style="color:#117A74;"></i>
                    </div>
                    <i class="bi bi-people text-muted opacity-25 fs-3"></i>
                </div>
                <div class="display-6 fw-bold" style="color:#0D6B65;">{{ $totalSiswaDalamHalaqah }}</div>
                <div class="fw-semibold small mt-1" style="color:#1F4172;">TOTAL SISWA</div>
                <div class="text-muted" style="font-size:.72rem;">Aktif dalam Halaqah</div>
            </div>
        </div>
    </div>

    {{-- Ustadz Pengampu --}}
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center"
                         style="width:46px; height:46px; background:#E8F5F4;">
                        <i class="bi bi-person-check-fill fs-5" style="color:#117A74;"></i>
                    </div>
                    <i class="bi bi-person-workspace text-muted opacity-25 fs-3"></i>
                </div>
                <div class="display-6 fw-bold" style="color:#0D6B65;">{{ $totalUstadz }}</div>
                <div class="fw-semibold small mt-1" style="color:#1F4172;">USTADZ PENGAMPU</div>
                <div class="text-muted" style="font-size:.72rem;">Semua Aktif Membimbing</div>
            </div>
        </div>
    </div>

    {{-- Rata-Rata Capaian --}}
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center"
                         style="width:46px; height:46px; background:#E8F5F4;">
                        <i class="bi bi-graph-up-arrow fs-5" style="color:#117A74;"></i>
                    </div>
                    <i class="bi bi-activity text-muted opacity-25 fs-3"></i>
                </div>
                <div class="display-6 fw-bold" style="color:#0D6B65;">
                    {{ $rataCapaianKeseluruhan }}<span class="fs-4 fw-normal text-muted">%</span>
                </div>
                <div class="fw-semibold small mt-1" style="color:#1F4172;">RATA-RATA CAPAIAN</div>
                <div class="text-muted" style="font-size:.72rem;">
                    @if($rataCapaianKeseluruhan > 0)
                        +{{ $rataCapaianKeseluruhan }}% dari target bulanan
                    @else
                        Belum ada data setoran
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Halaqah Aktif --}}
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center"
                         style="width:46px; height:46px; background:#E8F5F4;">
                        <i class="bi bi-collection-fill fs-5" style="color:#117A74;"></i>
                    </div>
                    <i class="bi bi-diagram-3 text-muted opacity-25 fs-3"></i>
                </div>
                <div class="display-6 fw-bold" style="color:#0D6B65;">{{ $totalHalaqah }}</div>
                <div class="fw-semibold small mt-1" style="color:#1F4172;">HALAQAH AKTIF</div>
                <div class="text-muted" style="font-size:.72rem;">Kelompok Tahfiz</div>
            </div>
        </div>
    </div>

</div>

{{-- ── GRAFIK BATANG + RINGKASAN ───────────────────────────────────── --}}
<div class="row g-3 mb-4">

    {{-- Grafik --}}
    <div class="col-12 col-xl-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <h2 class="fw-bold mb-0 h5" style="color:#117A74;">
                        <i class="bi bi-bar-chart-fill me-2"></i>Capaian per Kelompok Halaqah
                    </h2>
                    <span class="text-muted small">Bulan Berjalan</span>
                </div>

                @if(count($grafikLabels) === 0)
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-bar-chart display-5 d-block mb-2 opacity-25"></i>
                        <p class="mb-0 small">Belum ada halaqah. <a href="{{ route('admin.halaqah.create') }}" style="color:#117A74;">Tambah halaqah</a> untuk melihat grafik.</p>
                    </div>
                @else
                    <div style="position:relative; height:240px;">
                        <canvas id="grafikCapaian"></canvas>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Info Panel kanan --}}
    <div class="col-12 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 h-100"
             style="background: linear-gradient(135deg, #FFFBEB 0%, #FEF9C3 100%); border-left: 4px solid #F59E0B !important;">
            <div class="card-body p-4">
                <span class="badge fw-bold px-3 py-1 rounded-pill mb-3 d-inline-block"
                      style="background:#FEF9C3; color:#B45309; font-size:.7rem; letter-spacing:.05em;">
                    <i class="bi bi-lightbulb-fill me-1"></i>INFORMASI MUTABAAH
                </span>
                <h3 class="fw-bold h6 mb-2" style="color:#1F4172;">Evaluasi Berkala Siswa</h3>
                <p class="text-muted small mb-3" style="line-height:1.65;">
                    Seluruh data capaian siswa disinkronkan langsung dari catatan harian
                    para asatidz pembina halaqah. Buka menu laporan untuk mencetak rekap semester.
                </p>

                {{-- Ringkasan cepat --}}
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex justify-content-between align-items-center py-2 px-3 rounded-3"
                         style="background:rgba(17,122,116,.07);">
                        <span class="small text-muted">Total Setoran</span>
                        <span class="fw-bold small" style="color:#117A74;">{{ $totalSiswaDalamHalaqah > 0 ? '–' : '0' }} catatan</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 px-3 rounded-3"
                         style="background:rgba(17,122,116,.07);">
                        <span class="small text-muted">Halaqah Berisi Siswa</span>
                        <span class="fw-bold small" style="color:#117A74;">{{ $ringkasanHalaqah->where('status','Aktif')->count() }} kelompok</span>
                    </div>
                </div>

                @if(Route::has('admin.laporan'))
                <a href="{{ route('admin.laporan') }}"
                   class="btn btn-sm fw-semibold mt-3 w-100 rounded-pill"
                   style="background:#F59E0B; border:none; color:#fff;">
                    <i class="bi bi-file-earmark-bar-graph me-1"></i>Buka Laporan Evaluasi
                </a>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- ── TABEL RINGKASAN KELOMPOK HALAQAH ───────────────────────────── --}}
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h2 class="fw-bold mb-0 h5" style="color:#117A74;">
            <i class="bi bi-table me-2"></i>Ringkasan Kelompok Halaqah
        </h2>
        <a href="{{ route('admin.halaqah.index') }}"
           class="btn btn-sm fw-semibold rounded-pill px-3"
           style="background:#E8F5F4; color:#117A74; border:none;">
            Kelola Semua Halaqah <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="card-body p-0">
        @if($ringkasanHalaqah->isEmpty())
            <div class="text-center py-5 text-muted px-4">
                <i class="bi bi-collection display-5 d-block mb-2 opacity-25"></i>
                <p class="mb-0 small">Belum ada halaqah. <a href="{{ route('admin.halaqah.create') }}" style="color:#117A74;">Tambahkan halaqah</a> untuk melihat ringkasan.</p>
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background:#F0F9F8;">
                    <tr>
                        <th class="px-4 py-3 small fw-semibold text-uppercase" style="color:#117A74; letter-spacing:.04em;">Nama Halaqah</th>
                        <th class="px-3 py-3 small fw-semibold text-uppercase" style="color:#117A74; letter-spacing:.04em;">Ustadz Pembimbing</th>
                        <th class="px-3 py-3 small fw-semibold text-uppercase" style="color:#117A74; letter-spacing:.04em;">Target Hafalan</th>
                        <th class="px-3 py-3 small fw-semibold text-uppercase text-center" style="color:#117A74; letter-spacing:.04em;">Rata-Rata Capaian</th>
                        <th class="px-4 py-3 small fw-semibold text-uppercase text-center" style="color:#117A74; letter-spacing:.04em;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ringkasanHalaqah as $row)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="fw-semibold small" style="color:#0D6B65;">{{ $row['nama_halaqah'] }}</div>
                            <div class="text-muted" style="font-size:.74rem;">{{ $row['kode_halaqah'] }} · {{ $row['jumlah_siswa'] }} siswa</div>
                        </td>
                        <td class="px-3 py-3 text-muted small">{{ $row['ustadz'] }}</td>
                        <td class="px-3 py-3 text-muted small">{{ $row['target_juz'] }}</td>
                        <td class="px-3 py-3 text-center">
                            @php $pct = $row['capaian']; @endphp
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <div class="progress flex-grow-1" style="height:8px; max-width:90px;">
                                    <div class="progress-bar"
                                         role="progressbar"
                                         style="width:{{ $pct }}%; background:{{ $pct >= 75 ? '#117A74' : ($pct >= 50 ? '#D97706' : '#DC2626') }};"
                                         aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                <span class="fw-bold small" style="min-width:38px; color:#0D6B65;">{{ $pct }}%</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($row['status'] === 'Aktif')
                                <span class="badge fw-semibold rounded-pill px-3 py-1 small"
                                      style="background:#E8F5F4; color:#117A74; border:1px solid rgba(17,122,116,.2);">
                                    Sesuai Target
                                </span>
                            @else
                                <span class="badge fw-semibold rounded-pill px-3 py-1 small"
                                      style="background:#F1F5F9; color:#64748B; border:1px solid #E2E8F0;">
                                    Belum Ada Siswa
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

<style>
    .hover-lift { transition: transform .2s ease, box-shadow .2s ease; }
    .hover-lift:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.08) !important; }
</style>

@endsection

{{-- ── CHART.JS ─────────────────────────────────────────────────────── --}}
@if(count($grafikLabels) > 0)
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"
        integrity="sha256-oCV/FBfCMvjSHxNE5QRNEFkLXBkMFHzYLBolbSEfHTg=" crossorigin="anonymous"></script>
<script>
(function () {
    const labels = @json($grafikLabels);
    const data   = @json($grafikData);

    const teal = 'rgba(17,122,116,.85)';
    const colors = data.map(v =>
        v >= 75 ? teal :
        v >= 50 ? 'rgba(217,119,6,.85)' :
                  'rgba(220,38,38,.85)'
    );

    new Chart(document.getElementById('grafikCapaian'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Rata-rata Capaian (%)',
                data,
                backgroundColor: colors,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ` ${ctx.parsed.y}%` } }
            },
            scales: {
                y: {
                    min: 0, max: 100,
                    ticks: { callback: v => v + '%', font: { size: 11 }, color: '#64748B' },
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
