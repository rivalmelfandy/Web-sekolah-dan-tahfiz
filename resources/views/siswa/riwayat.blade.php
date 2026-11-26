@extends('layouts.siswa', ['title' => 'Riwayat Hafalan'])

@section('breadcrumb', 'Riwayat Hafalan')

@section('content')

    {{-- ── HEADER ──────────────────────────────────────────────────────── --}}
    <div class="mb-4">
        <h2 class="fw-bold mb-1 h4 text-dark">
            <i class="bi bi-journal-bookmark-fill me-2" style="color:#117A74;"></i>Riwayat Hafalan
        </h2>
        <p class="text-muted mb-0 small">Seluruh catatan setoran hafalan dan sesi murajaah Anda.</p>
    </div>

    {{-- ── 3 KARTU STATISTIK RINGKAS ──────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width:42px;height:42px;background:linear-gradient(135deg,#10B981,#0D9488);">
                            <i class="bi bi-journal-check text-white fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-4 text-dark lh-1">{{ $totalSetoran }}</div>
                            <div class="text-muted small mt-1">Total Setoran</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width:42px;height:42px;background:linear-gradient(135deg,#F97316,#EA580C);">
                            <i class="bi bi-arrow-repeat text-white fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-4 text-dark lh-1">{{ $totalMurajaah }}</div>
                            <div class="text-muted small mt-1">Total Murajaah</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width:42px;height:42px;background:linear-gradient(135deg,#3B82F6,#2563EB);">
                            <i class="bi bi-award-fill text-white fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-4 text-dark lh-1">
                                {{ $rataNilai !== null ? $rataNilai : '–' }}
                            </div>
                            <div class="text-muted small mt-1">
                                Rata-Rata Nilai
                                @if($predikatRata)
                                    &middot;
                                    <span class="badge-indeks bi-{{ $predikatRata->value }}">
                                        {{ $predikatRata->value }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── FILTER ──────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="{{ route('siswa.riwayat') }}" class="d-flex flex-wrap align-items-end gap-3">
                <div style="min-width:160px; flex:1;">
                    <label class="form-label small fw-semibold text-dark mb-1">
                        <i class="bi bi-funnel me-1" style="color:#117A74;"></i>Kategori
                    </label>
                    <select name="kategori" class="form-select form-select-sm"
                        style="border-color:#E2E8F0; border-radius:8px;">
                        <option value="semua" {{ $filterKategori === 'semua' ? 'selected' : '' }}>Semua</option>
                        <option value="Hafalan" {{ $filterKategori === 'Hafalan' ? 'selected' : '' }}>Hafalan</option>
                        <option value="Murajaah" {{ $filterKategori === 'Murajaah' ? 'selected' : '' }}>Murajaah</option>
                    </select>
                </div>
                <div style="min-width:200px; flex:2;">
                    <label class="form-label small fw-semibold text-dark mb-1">
                        <i class="bi bi-search me-1" style="color:#117A74;"></i>Cari Surah
                    </label>
                    <input type="text" name="surah" class="form-control form-control-sm" placeholder="Contoh: Al-Baqarah"
                        value="{{ $filterSurah }}" style="border-color:#E2E8F0; border-radius:8px;">
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-sm fw-semibold px-3"
                        style="background:#117A74; color:#fff; border:none; border-radius:8px;">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                    @if($filterKategori !== 'semua' || $filterSurah !== '')
                        <a href="{{ route('siswa.riwayat') }}" class="btn btn-sm fw-semibold px-3"
                            style="background:#F1F5F9; color:#374151; border:none; border-radius:8px;">
                            <i class="bi bi-x me-1"></i>Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- ── LAYOUT 2 KOLOM: TABEL + PANEL INFO ──────────────────────────── --}}
    <div class="row g-3 align-items-start">

        {{-- ── KIRI: Tabel riwayat ──────────────────────────────────────── --}}
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    {{-- Header --}}
                    <div class="p-4 pb-3 d-flex align-items-center justify-content-between flex-wrap gap-2 border-bottom">
                        <div>
                            <h3 class="fw-bold mb-0 h5 text-dark">Daftar Aktivitas Hafalan</h3>
                            <p class="text-muted small mb-0 mt-1">
                                @if($filterKategori !== 'semua' || $filterSurah !== '')
                                    Menampilkan {{ $aktivitas->count() }} hasil
                                    @if($filterKategori !== 'semua') &middot; Kategori: <strong>{{ $filterKategori }}</strong>
                                    @endif
                                    @if($filterSurah !== '') &middot; Surah: <strong>"{{ $filterSurah }}"</strong> @endif
                                @else
                                    {{ $aktivitas->count() }} total catatan aktivitas
                                @endif
                            </p>
                        </div>
                        @if($aktivitas->isNotEmpty())
                            <span class="badge rounded-pill fw-semibold px-3 py-2"
                                style="background:#E8F5F4; color:#117A74; font-size:.75rem;">
                                <i class="bi bi-list-check me-1"></i>{{ $aktivitas->count() }} Catatan
                            </span>
                        @endif
                    </div>

                    @if($aktivitas->isEmpty())
                        <div class="text-center py-5 px-4">
                            @if($filterKategori !== 'semua' || $filterSurah !== '')
                                <i class="bi bi-search display-5 d-block mb-3 opacity-25"></i>
                                <p class="fw-semibold text-dark mb-1">Tidak ada hasil yang cocok</p>
                                <p class="text-muted small mb-3">
                                    Coba ubah filter atau
                                    <a href="{{ route('siswa.riwayat') }}" style="color:#117A74;">tampilkan semua</a>.
                                </p>
                            @else
                                <i class="bi bi-journal-x display-5 d-block mb-3 opacity-25"></i>
                                <p class="fw-semibold text-dark mb-1">Belum ada riwayat hafalan</p>
                                <p class="text-muted small mb-0">
                                    Catatan setoran dan murajaah Anda akan tampil di sini setelah
                                    diinput oleh Ustadz pembimbing.
                                </p>
                            @endif
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 tbl-siswa">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3">#</th>
                                        <th class="px-3 py-3">Tanggal</th>
                                        <th class="px-3 py-3">Kategori</th>
                                        <th class="px-3 py-3">Surah &amp; Ayat</th>
                                        <th class="px-3 py-3 text-center">Nilai</th>
                                        <th class="px-3 py-3 text-center">Kualitas</th>
                                        <th class="px-4 py-3">Ustadz</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($aktivitas as $i => $item)
                                        <tr>
                                            <td class="px-4 py-3 text-muted small">{{ $i + 1 }}</td>

                                            <td class="px-3 py-3 text-nowrap">
                                                <div class="small fw-semibold text-dark">
                                                    {{ $item['tanggal']->translatedFormat('d M Y') }}
                                                </div>
                                                <div class="text-muted" style="font-size:.72rem;">
                                                    {{ $item['tanggal']->translatedFormat('l') }}
                                                </div>
                                            </td>

                                            <td class="px-3 py-3">
                                                @if($item['kategori'] === 'Hafalan')
                                                    <span class="badge fw-semibold rounded-pill px-2 py-1"
                                                        style="background:#D1FAE5; color:#065F46; font-size:.7rem;">
                                                        <i class="bi bi-journal-check me-1"></i>Hafalan
                                                    </span>
                                                @else
                                                    <span class="badge fw-semibold rounded-pill px-2 py-1"
                                                        style="background:#DBEAFE; color:#1D4ED8; font-size:.7rem;">
                                                        <i class="bi bi-arrow-repeat me-1"></i>Murajaah
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="px-3 py-3">
                                                <div class="fw-semibold small text-dark">{{ $item['nama_surah'] }}</div>
                                                @if($item['ayat_awal'] !== null && $item['ayat_akhir'] !== null)
                                                    <div class="text-muted" style="font-size:.72rem;">
                                                        Ayat {{ $item['ayat_awal'] }}–{{ $item['ayat_akhir'] }}
                                                    </div>
                                                @endif
                                            </td>

                                            <td class="px-3 py-3 text-center">
                                                <div class="fw-bold small text-dark">{{ $item['nilai'] }}</div>
                                            </td>

                                            <td class="px-3 py-3 text-center">
                                                @if($item['kualitas_terkini'])
                                                    <span class="badge-indeks bi-{{ $item['kualitas_terkini']->value }}">
                                                        {{ $item['kualitas_terkini']->value }}
                                                    </span>
                                                @else
                                                    <span class="text-muted small">–</span>
                                                @endif
                                            </td>

                                            <td class="px-4 py-3 small text-muted text-nowrap">
                                                {{ $item['ustadz'] }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── KANAN: Panel informasi ────────────────────────────────────── --}}
        <div class="col-12 col-xl-4 d-flex flex-column gap-3">

            {{-- Ringkasan aktivitas --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h4 class="fw-bold h6 text-dark mb-3">
                        <i class="bi bi-clipboard2-data me-2" style="color:#117A74;"></i>Ringkasan Aktivitas
                    </h4>
                    <div class="d-flex flex-column gap-2" style="font-size:.82rem;">
                        <div class="d-flex justify-content-between align-items-center py-2 px-3 rounded-3"
                            style="background:#F0FDF4;">
                            <span class="text-muted">Setoran Hafalan</span>
                            <span class="fw-bold" style="color:#065F46;">
                                {{ $aktivitas->where('kategori', 'Hafalan')->count() }}
                                <span class="text-muted fw-normal">/{{ $totalSetoran }}</span>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-2 px-3 rounded-3"
                            style="background:#EFF6FF;">
                            <span class="text-muted">Murajaah</span>
                            <span class="fw-bold" style="color:#1D4ED8;">
                                {{ $aktivitas->where('kategori', 'Murajaah')->count() }}
                                <span class="text-muted fw-normal">/{{ $totalMurajaah }}</span>
                            </span>
                        </div>
                        @if($rataNilai !== null)
                            <div class="d-flex justify-content-between align-items-center py-2 px-3 rounded-3"
                                style="background:#F5F3FF;">
                                <span class="text-muted">Rata-Rata Nilai</span>
                                <span class="fw-bold" style="color:#6D28D9;">
                                    {{ $rataNilai }}
                                    @if($predikatRata)
                                        <span class="badge-indeks bi-{{ $predikatRata->value }} ms-1" style="font-size:.65rem;">
                                            {{ $predikatRata->value }}
                                        </span>
                                    @endif
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Distribusi kualitas (jika ada data) --}}
            @if($aktivitas->isNotEmpty())
                @php
                    $hanya = $filterKategori === 'Hafalan'
                        ? $aktivitas
                        : $aktivitas->where('kategori', 'Hafalan');
                    $distribusi = $hanya->groupBy(fn($item) => $item['kualitas_terkini']?->value ?? '–')
                        ->map->count()
                        ->sortKeysDesc();
                @endphp
                @if($distribusi->isNotEmpty())
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h4 class="fw-bold h6 text-dark mb-3">
                                <i class="bi bi-bar-chart-fill me-2" style="color:#117A74;"></i>Distribusi Kualitas Hafalan
                            </h4>
                            <div class="d-flex flex-column gap-2">
                                @foreach($distribusi as $grade => $jumlah)
                                    @php
                                        $totalHafalan = $hanya->count();
                                        $pct = $totalHafalan > 0 ? round($jumlah / $totalHafalan * 100) : 0;
                                        $gradeColors = [
                                            'A' => ['bar' => '#10B981', 'bg' => '#D1FAE5', 'text' => '#065F46', 'border' => '#6EE7B7'],
                                            'AB' => ['bar' => '#10B981', 'bg' => '#D1FAE5', 'text' => '#065F46', 'border' => '#6EE7B7'],
                                            'B' => ['bar' => '#3B82F6', 'bg' => '#DBEAFE', 'text' => '#1D4ED8', 'border' => '#93C5FD'],
                                            'BC' => ['bar' => '#3B82F6', 'bg' => '#DBEAFE', 'text' => '#1D4ED8', 'border' => '#93C5FD'],
                                            'C' => ['bar' => '#F59E0B', 'bg' => '#FEF9C3', 'text' => '#92400E', 'border' => '#FDE68A'],
                                            'D' => ['bar' => '#EF4444', 'bg' => '#FEE2E2', 'text' => '#991B1B', 'border' => '#FCA5A5'],
                                            'E' => ['bar' => '#6B7280', 'bg' => '#F3F4F6', 'text' => '#374151', 'border' => '#D1D5DB'],
                                        ];
                                        $c = $gradeColors[$grade] ?? $gradeColors['E'];
                                    @endphp
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="badge-indeks"
                                                style="background:{{ $c['bg'] }}; color:{{ $c['text'] }}; border:1px solid {{ $c['border'] }};">
                                                {{ $grade }}
                                            </span>
                                            <span class="small text-muted">{{ $jumlah }} setoran ({{ $pct }}%)</span>
                                        </div>
                                        <div class="progress rounded-pill" style="height:6px; background:#E2E8F0;">
                                            <div class="progress-bar rounded-pill"
                                                style="width:{{ $pct }}%; background:{{ $c['bar'] }};">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            {{-- Panduan kualitas --}}
            <div class="card border-0 shadow-sm rounded-4"
                style="background:linear-gradient(135deg,#F0FDF4,#ECFDF5); border-left:4px solid #10B981 !important;">
                <div class="card-body p-4">
                    <span class="badge fw-bold px-3 py-1 rounded-pill mb-3 d-inline-block"
                        style="background:#D1FAE5; color:#065F46; font-size:.7rem; letter-spacing:.05em;">
                        <i class="bi bi-info-circle me-1"></i>PANDUAN PENILAIAN
                    </span>
                    <div class="d-flex flex-column gap-1" style="font-size:.75rem;">
                        @foreach([
                                ['A', '90–100', 'Sangat Baik'],
                                ['AB', '80–89', 'Baik Sekali'],
                                ['B', '70–79', 'Baik'],
                                ['BC', '60–69', 'Cukup Baik'],
                                ['C', '50–59', 'Cukup'],
                                ['D', '40–49', 'Kurang'],
                                ['E', '0–39', 'Sangat Kurang'],
                            ] as [$g, $range, $label])
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge-indeks bi-{{ $g }}"
                                    style="min-width:32px; text-align:center;">{{ $g }}</span>
                                <span class="text-muted" style="min-width:50px;">{{ $range }}</span>
                                <span class="text-dark">{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>

    </div>

@endsection