@extends('layouts.siswa', ['title' => 'Progres Hafalan'])

@section('breadcrumb', 'Progres Hafalan')

@section('content')

    {{-- ── HERO BANNER ─────────────────────────────────────────────────── --}}
    <div class="rounded-4 p-4 p-md-5 text-white shadow-sm mb-4"
        style="background: linear-gradient(135deg, #0D6B65 0%, #117A74 60%, #138C85 100%);">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h1 class="fw-bold mb-1 h3">Assalamu'alaikum, {{ $user->nama_lengkap }}!</h1>
                <p class="mb-0 opacity-75 small">
                    @if($namaHalaqah)
                        Halaqah {{ $namaHalaqah }}
                        @if($namaUstadzTarget)
                            &bull; Pembimbing: {{ $namaUstadzTarget }}
                        @endif
                    @else
                        Pantau progres hafalan dan aktivitas tahfiz Anda di SMA IT Al-Fadhilah Pekanbaru.
                    @endif
                </p>
            </div>
            <span class="badge bg-white bg-opacity-25 text-white px-3 py-2 rounded-pill small fw-semibold">
                <i class="bi bi-person-badge me-1"></i>NIS: {{ $user->nip_nis }}
            </span>
        </div>
    </div>

    {{-- ── 4 KARTU KPI ──────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">

        {{-- Total Setoran --}}
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 kpi-siswa-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                            style="width:46px;height:46px;background:linear-gradient(135deg,#10B981,#0D9488);box-shadow:0 4px 10px rgba(16,185,129,.25);">
                            <i class="bi bi-journal-check fs-5 text-white"></i>
                        </div>
                        <i class="bi bi-book fs-3" style="color:#10B981;opacity:.18;"></i>
                    </div>
                    <div class="display-6 fw-bold text-dark">{{ $totalSetoran }}</div>
                    <div class="fw-semibold small mt-1 text-dark">TOTAL SETORAN</div>
                    <div class="text-muted" style="font-size:.72rem;">
                        {{ $totalSetoran > 0 ? 'Catatan setoran hafalan' : 'Belum ada data setoran' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Setoran Lancar --}}
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 kpi-siswa-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                            style="width:46px;height:46px;background:linear-gradient(135deg,#3B82F6,#2563EB);box-shadow:0 4px 10px rgba(59,130,246,.25);">
                            <i class="bi bi-patch-check-fill fs-5 text-white"></i>
                        </div>
                        <i class="bi bi-graph-up fs-3" style="color:#3B82F6;opacity:.18;"></i>
                    </div>
                    <div class="display-6 fw-bold text-dark">{{ $setoranLancar }}</div>
                    <div class="fw-semibold small mt-1 text-dark">SETORAN LANCAR</div>
                    <div class="text-muted" style="font-size:.72rem;">
                        @if($totalSetoran > 0)
                            {{ $setoranLancar }} dari {{ $totalSetoran }} setoran
                        @else
                            Belum ada data setoran
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Murajaah --}}
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 kpi-siswa-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                            style="width:46px;height:46px;background:linear-gradient(135deg,#F97316,#EA580C);box-shadow:0 4px 10px rgba(249,115,22,.25);">
                            <i class="bi bi-arrow-repeat fs-5 text-white"></i>
                        </div>
                        <i class="bi bi-clock-history fs-3" style="color:#F97316;opacity:.18;"></i>
                    </div>
                    <div class="display-6 fw-bold text-dark">{{ $totalMurajaah }}</div>
                    <div class="fw-semibold small mt-1 text-dark">TOTAL MURAJAAH</div>
                    <div class="text-muted" style="font-size:.72rem;">
                        {{ $totalMurajaah > 0 ? 'Sesi pengulangan hafalan' : 'Belum ada data murajaah' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Status Target --}}
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 kpi-siswa-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                            style="width:46px;height:46px;background:linear-gradient(135deg,#14B8A6,#0D9488);box-shadow:0 4px 10px rgba(20,184,166,.25);">
                            <i class="bi bi-bullseye fs-5 text-white"></i>
                        </div>
                        <i class="bi bi-flag fs-3" style="color:#14B8A6;opacity:.18;"></i>
                    </div>
                    <div class="fw-bold text-dark {{ $targetAktifRaw ? 'h4 mb-1' : 'display-6' }}"
                        style="line-height:1.25;">
                        {{ $targetAktifRaw ? 'Aktif' : '–' }}
                    </div>
                    <div class="fw-semibold small mt-1 text-dark">TARGET HAFALAN</div>
                    <div class="text-muted text-truncate" style="font-size:.72rem;"
                        title="{{ $targetAktifRaw ? $targetAktifRaw->surah_awal . ' – ' . $targetAktifRaw->surah_akhir : 'Belum ada target aktif' }}">
                        {{ $targetAktifRaw
                            ? $targetAktifRaw->surah_awal . ' – ' . $targetAktifRaw->surah_akhir
                            : 'Belum ada target aktif' }}
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── PANEL PROGRES HAFALAN ────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                <div>
                    <h2 class="fw-bold mb-0 h5 text-dark">
                        <i class="bi bi-graph-up-arrow me-2" style="color:#117A74;"></i>Ringkasan Progres Hafalan
                    </h2>
                    <p class="text-muted small mb-0 mt-1">Capaian berdasarkan kualitas hafalan yang dicatat Ustadz.</p>
                </div>
                @if($totalSetoran > 0)
                    @php
                        $pctLancar = $totalSetoran > 0
                            ? round($setoranLancar / $totalSetoran * 100)
                            : 0;
                    @endphp
                    <span class="badge px-3 py-2 rounded-pill fw-semibold"
                        style="background:#D1FAE5; color:#065F46; font-size:.8rem;">
                        {{ $pctLancar }}% Lancar
                    </span>
                @endif
            </div>

            <div class="row g-4">
                {{-- Bar distribusi kualitas + hafalan terakhir --}}
                <div class="col-12 col-md-7">

                    @if($totalSetoran > 0)
                        @php
                            $pctLancar       = round($setoranLancar / $totalSetoran * 100);
                            $setoranKurang   = \App\Models\SetoranHafalan::where('siswa_id', $user->id)
                                ->where('kualitas', \App\Enums\KualitasLevel::KurangLancar->value)->count();
                            $setoranTidak    = \App\Models\SetoranHafalan::where('siswa_id', $user->id)
                                ->where('kualitas', \App\Enums\KualitasLevel::TidakLancar->value)->count();
                            $pctKurang       = round($setoranKurang / $totalSetoran * 100);
                            $pctTidak        = round($setoranTidak / $totalSetoran * 100);
                        @endphp

                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small fw-semibold text-dark">Lancar</span>
                                <span class="small text-muted">{{ $setoranLancar }} setoran ({{ $pctLancar }}%)</span>
                            </div>
                            <div class="progress rounded-pill mb-3" style="height:12px; background:#E2E8F0;">
                                <div class="progress-bar rounded-pill" style="width:{{ $pctLancar }}%; background:#10B981;"></div>
                            </div>

                            <div class="d-flex justify-content-between mb-1">
                                <span class="small fw-semibold text-dark">Kurang Lancar</span>
                                <span class="small text-muted">{{ $setoranKurang }} setoran ({{ $pctKurang }}%)</span>
                            </div>
                            <div class="progress rounded-pill mb-3" style="height:12px; background:#E2E8F0;">
                                <div class="progress-bar rounded-pill" style="width:{{ $pctKurang }}%; background:#F59E0B;"></div>
                            </div>

                            <div class="d-flex justify-content-between mb-1">
                                <span class="small fw-semibold text-dark">Tidak Lancar</span>
                                <span class="small text-muted">{{ $setoranTidak }} setoran ({{ $pctTidak }}%)</span>
                            </div>
                            <div class="progress rounded-pill" style="height:12px; background:#E2E8F0;">
                                <div class="progress-bar rounded-pill" style="width:{{ $pctTidak }}%; background:#EF4444;"></div>
                            </div>
                        </div>
                    @else
                        <div class="p-3 rounded-3 text-center text-muted" style="background:#F8FAFC; border:1px dashed #CBD5E1;">
                            <i class="bi bi-journal-x d-block mb-1 opacity-25 fs-4"></i>
                            <span class="small">Belum ada data setoran hafalan.</span>
                        </div>
                    @endif

                    {{-- Hafalan terakhir --}}
                    @if($setoranTerakhir)
                        @php
                            $kualitasLabel = $setoranTerakhir->kualitas->value;
                            $kualitasBg = match($setoranTerakhir->kualitas->value) {
                                'Lancar'        => '#D1FAE5',
                                'Kurang Lancar' => '#FEF9C3',
                                'Tidak Lancar'  => '#FEE2E2',
                                default         => '#F3F4F6',
                            };
                            $kualitasColor = match($setoranTerakhir->kualitas->value) {
                                'Lancar'        => '#065F46',
                                'Kurang Lancar' => '#92400E',
                                'Tidak Lancar'  => '#991B1B',
                                default         => '#374151',
                            };
                        @endphp
                        <div class="mt-4 p-3 rounded-3" style="background:#F0FDF4; border:1px solid #A7F3D0;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-bookmark-check-fill" style="color:#059669;"></i>
                                <span class="fw-semibold small text-dark">Hafalan Terakhir</span>
                                <span class="ms-auto text-muted" style="font-size:.72rem;">
                                    {{ $setoranTerakhir->tanggal_setor->translatedFormat('d M Y') }}
                                </span>
                            </div>
                            <div class="fw-bold text-dark">{{ $setoranTerakhir->surah }}</div>
                            <div class="d-flex flex-wrap gap-3 mt-2 small">
                                <span class="text-muted">
                                    Ayat <strong class="text-dark">{{ $setoranTerakhir->ayat_awal }}–{{ $setoranTerakhir->ayat_akhir }}</strong>
                                </span>
                                <span class="text-muted">
                                    Kualitas
                                    <span class="badge fw-semibold ms-1 px-2 py-1 rounded-pill"
                                        style="background:{{ $kualitasBg }}; color:{{ $kualitasColor }}; font-size:.7rem;">
                                        {{ $kualitasLabel }}
                                    </span>
                                </span>
                                <span class="text-muted">
                                    Ustadz <strong class="text-dark">{{ $setoranTerakhir->nama_ustadz }}</strong>
                                </span>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Panel evaluasi target --}}
                <div class="col-12 col-md-5">
                    @if($targetAktifRaw)
                        <div class="h-100 p-3 rounded-3"
                            style="background:linear-gradient(135deg,#FFFBEB,#FEF9C3); border:1px solid #FDE68A;">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div class="rounded-3 d-flex align-items-center justify-content-center"
                                    style="width:34px;height:34px;background:linear-gradient(135deg,#F59E0B,#D97706);">
                                    <i class="bi bi-bullseye text-white" style="font-size:.9rem;"></i>
                                </div>
                                <span class="fw-bold small text-dark">Evaluasi Target Aktif</span>
                                <span class="badge ms-auto px-2 py-1 rounded-pill"
                                    style="background:#D1FAE5; color:#065F46; font-size:.68rem;">Aktif</span>
                            </div>

                            <div class="p-2 rounded-2 mb-3" style="background:rgba(255,255,255,.65);">
                                <div class="text-muted" style="font-size:.7rem;">Rentang Surah Target</div>
                                <div class="fw-bold text-dark mt-1" style="font-size:.9rem;">
                                    {{ $targetAktifRaw->surah_awal }} &ndash; {{ $targetAktifRaw->surah_akhir }}
                                </div>
                            </div>

                            <div class="d-flex flex-column gap-2" style="font-size:.78rem;">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Mulai</span>
                                    <span class="fw-semibold text-dark">
                                        {{ \Carbon\Carbon::parse($targetAktifRaw->tanggal_mulai)->translatedFormat('d M Y') }}
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Berakhir</span>
                                    <span class="fw-semibold text-dark">
                                        {{ \Carbon\Carbon::parse($targetAktifRaw->tanggal_akhir)->translatedFormat('d M Y') }}
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Pembimbing</span>
                                    <span class="fw-semibold text-dark">{{ $namaUstadzTarget ?? '–' }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-1 mt-1"
                                    style="border-top:1px dashed #FDE68A;">
                                    <span class="text-muted">Sisa Waktu</span>
                                    @if($hariSisaTarget !== null)
                                        @if($hariSisaTarget < 0)
                                            <span class="fw-bold" style="color:#DC2626;">Telah berakhir</span>
                                        @elseif($hariSisaTarget === 0)
                                            <span class="fw-bold" style="color:#D97706;">Hari ini</span>
                                        @elseif($hariSisaTarget <= 7)
                                            <span class="fw-bold" style="color:#D97706;">{{ $hariSisaTarget }} hari lagi</span>
                                        @else
                                            <span class="fw-bold" style="color:#065F46;">{{ $hariSisaTarget }} hari lagi</span>
                                        @endif
                                    @else
                                        <span class="text-muted">–</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="h-100 d-flex flex-column align-items-center justify-content-center text-center p-4 rounded-3"
                            style="background:#F8FAFC; border:1px dashed #CBD5E1; min-height:160px;">
                            <i class="bi bi-flag display-6 mb-2 opacity-25"></i>
                            <p class="fw-semibold text-dark small mb-1">Belum ada target aktif</p>
                            <p class="text-muted mb-0" style="font-size:.78rem;">
                                Target hafalan akan ditetapkan oleh Ustadz pembimbing.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── TABEL SETORAN TERBARU + PANEL HALAQAH ───────────────────────── --}}
    <div class="row g-3">

        {{-- Kiri: Tabel setoran terbaru --}}
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-0 d-flex flex-column">
                    <div class="p-4 pb-3 d-flex align-items-center justify-content-between flex-wrap gap-2 border-bottom">
                        <div>
                            <h2 class="fw-bold mb-0 h5 text-dark">
                                <i class="bi bi-journal-bookmark-fill me-2" style="color:#117A74;"></i>Setoran Hafalan Terbaru
                            </h2>
                            <p class="text-muted small mb-0 mt-1">5 catatan setoran hafalan terakhir Anda.</p>
                        </div>
                        @if($totalSetoran > 5)
                            <a href="{{ route('siswa.riwayat') }}" class="btn btn-sm fw-semibold rounded-pill px-3"
                                style="background:#E8F5F4; color:#117A74; border:none; font-size:.78rem;">
                                Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        @elseif($totalSetoran > 0)
                            <span class="badge rounded-pill fw-semibold px-3 py-2"
                                style="background:#E8F5F4; color:#117A74; font-size:.75rem;">
                                {{ $totalSetoran }} Setoran
                            </span>
                        @endif
                    </div>

                    @if($setoranTerbaru->isEmpty())
                        <div class="text-center py-5 px-4 text-muted my-auto">
                            <i class="bi bi-journal-x display-5 d-block mb-2 opacity-25"></i>
                            <p class="fw-semibold text-dark small mb-1">Belum ada data setoran hafalan</p>
                            <p class="mb-0 small">Catatan setoran Anda akan tampil di sini setelah diinput oleh Ustadz pembimbing.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 tbl-siswa">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3">Tanggal</th>
                                        <th class="px-3 py-3">Surah &amp; Ayat</th>
                                        <th class="px-3 py-3 text-center">Kualitas</th>
                                        <th class="px-4 py-3">Ustadz</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($setoranTerbaru as $setoran)
                                        @php
                                            $kBg = match($setoran->kualitas->value) {
                                                'Lancar'        => '#D1FAE5',
                                                'Kurang Lancar' => '#FEF9C3',
                                                'Tidak Lancar'  => '#FEE2E2',
                                                default         => '#F3F4F6',
                                            };
                                            $kColor = match($setoran->kualitas->value) {
                                                'Lancar'        => '#065F46',
                                                'Kurang Lancar' => '#92400E',
                                                'Tidak Lancar'  => '#991B1B',
                                                default         => '#374151',
                                            };
                                        @endphp
                                        <tr>
                                            <td class="px-4 py-3 small text-muted text-nowrap">
                                                {{ $setoran->tanggal_setor->translatedFormat('d M Y') }}
                                            </td>
                                            <td class="px-3 py-3">
                                                <div class="fw-semibold small text-dark">{{ $setoran->surah }}</div>
                                                <div class="text-muted" style="font-size:.75rem;">
                                                    Ayat {{ $setoran->ayat_awal }}–{{ $setoran->ayat_akhir }}
                                                </div>
                                            </td>
                                            <td class="px-3 py-3 text-center">
                                                <span class="badge fw-semibold px-2 py-1 rounded-pill"
                                                    style="background:{{ $kBg }}; color:{{ $kColor }}; font-size:.7rem;">
                                                    {{ $setoran->kualitas->value }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 small text-muted">
                                                {{ $setoran->nama_ustadz }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($totalSetoran > 5)
                            <div class="p-3 text-center border-top">
                                <a href="{{ route('siswa.riwayat') }}" class="text-decoration-none small fw-semibold"
                                    style="color:#117A74;">
                                    <i class="bi bi-arrow-right me-1"></i>Lihat semua {{ $totalSetoran }} setoran
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        {{-- Kanan: Info halaqah --}}
        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 h-100"
                style="background:linear-gradient(135deg,#FFFBEB,#FEF9C3); border-left:4px solid #F59E0B !important;">
                <div class="card-body p-4">
                    <span class="badge fw-bold px-3 py-1 rounded-pill mb-3 d-inline-block"
                        style="background:#FEF9C3; color:#B45309; font-size:.7rem; letter-spacing:.05em;">
                        <i class="bi bi-people-fill me-1"></i>INFORMASI HALAQAH
                    </span>

                    @if($namaHalaqah)
                        <h3 class="fw-bold h6 mb-2 text-dark">{{ $namaHalaqah }}</h3>
                        <p class="text-muted small mb-3" style="line-height:1.6;">
                            Data perkembangan hafalan Anda diperbarui oleh Ustadz pembimbing
                            setelah setiap sesi setoran dan murajaah.
                        </p>
                        <div class="d-flex flex-column gap-2">
                            <div class="d-flex justify-content-between align-items-center py-2 px-3 rounded-3"
                                style="background:rgba(17,122,116,.07);">
                                <span class="small text-muted">Ustadz Pembimbing</span>
                                <span class="fw-bold small" style="color:#117A74;">
                                    {{ $namaUstadzTarget ?? '–' }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-2 px-3 rounded-3"
                                style="background:rgba(17,122,116,.07);">
                                <span class="small text-muted">Total Setoran</span>
                                <span class="fw-bold small" style="color:#117A74;">{{ $totalSetoran }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-2 px-3 rounded-3"
                                style="background:rgba(17,122,116,.07);">
                                <span class="small text-muted">Total Murajaah</span>
                                <span class="fw-bold small" style="color:#117A74;">{{ $totalMurajaah }}</span>
                            </div>
                        </div>
                    @else
                        <h3 class="fw-bold h6 mb-2 text-dark">Belum Tergabung Halaqah</h3>
                        <p class="text-muted small mb-0" style="line-height:1.6;">
                            Akun Anda belum ditempatkan pada kelompok halaqah. Silakan hubungi
                            Admin atau Ustadz koordinator tahfiz.
                        </p>
                    @endif
                </div>
            </div>
        </div>

    </div>

@endsection
