@extends('layouts.admin', ['title' => 'Detail Laporan – ' . $siswa->nama_lengkap])

@section('breadcrumb', 'Detail Siswa')

@section('styles')
    <style>
        /* ── Header tabel hijau mint ─────────────────────────────────────── */
        .tbl-detail thead {
            background: #D1FAE5;
        }

        .tbl-detail thead th {
            color: #000000;
            letter-spacing: .045em;
            font-size: .72rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .tbl-detail {
            table-layout: fixed;
            width: 100%;
        }

        .tbl-detail tbody tr:hover td {
            background: #F0FDF4;
        }

        .tbl-detail tbody tr.hidden-row {
            display: none;
        }

        /* ── Badge kategori ──────────────────────────────────────────────── */
        .badge-hafalan {
            background: #D1FAE5;
            color: #065F46;
            border: 1px solid #6EE7B7;
        }

        .badge-murajaah {
            background: #DBEAFE;
            color: #1D4ED8;
            border: 1px solid #93C5FD;
        }

        .badge-kategori {
            font-size: .7rem;
            font-weight: 700;
            border-radius: 99px;
            padding: .2em .75em;
            display: inline-block;
        }

        /* ── Badge indeks ────────────────────────────────────────────────── */
        .badge-indeks {
            font-size: .72rem;
            font-weight: 700;
            border-radius: 99px;
            padding: .22em .75em;
            display: inline-block;
        }

        .bi-A,
        .bi-AB {
            background: #D1FAE5;
            color: #065F46;
            border: 1px solid #6EE7B7;
        }

        .bi-B,
        .bi-BC {
            background: #DBEAFE;
            color: #1D4ED8;
            border: 1px solid #93C5FD;
        }

        .bi-C {
            background: #FEF9C3;
            color: #92400E;
            border: 1px solid #FDE68A;
        }

        .bi-D,
        .bi-E {
            background: #FEE2E2;
            color: #991B1B;
            border: 1px solid #FCA5A5;
        }

        /* ── Tab filter ──────────────────────────────────────────────────── */
        .tab-filter {
            display: inline-flex;
            gap: .4rem;
            background: #F0FDF4;
            border: 1px solid #A7F3D0;
            border-radius: 99px;
            padding: .25rem .4rem;
        }

        .tab-filter button {
            border: none;
            background: transparent;
            border-radius: 99px;
            padding: .3rem .85rem;
            font-size: .78rem;
            font-weight: 600;
            color: #065F46;
            cursor: pointer;
            transition: background .15s, color .15s;
        }

        .tab-filter button.aktif {
            background: #047857;
            color: #fff;
        }

        /* ── Kartu identitas ─────────────────────────────────────────────── */
        .kartu-identitas {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            padding: 1.5rem 1.75rem;
            box-shadow: 0 4px 16px rgba(15, 23, 42, .04);
            color: #000000;
        }

        /* ── Base stat pill ──────────────────────────────────────────────── */
        .stat-pill {
            border-radius: 12px;
            padding: .75rem 1rem;
            min-width: 120px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .4rem;
        }

        .stat-pill-icon {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            font-size: 1rem;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Kartu 1 – Total Setoran */
        .stat-setoran {
            background: linear-gradient(135deg, #ECFDF5, #F0FDFA);
            border: 1px solid #A7F3D0;
        }

        .stat-setoran .stat-pill-icon {
            background: linear-gradient(135deg, #10B981, #0D9488);
        }

        /* Kartu 2 – Total Murajaah */
        .stat-murajaah {
            background: linear-gradient(135deg, #FFF7ED, #FEF2F2);
            border: 1px solid #FED7AA;
        }

        .stat-murajaah .stat-pill-icon {
            background: linear-gradient(135deg, #F97316, #EA580C);
        }

        /* Kartu 3 – Rata Nilai */
        .stat-rata {
            background: linear-gradient(135deg, #EFF6FF, #F0F9FF);
            border: 1px solid #BFDBFE;
        }

        .stat-rata .stat-pill-icon {
            background: linear-gradient(135deg, #3B82F6, #2563EB);
        }

        /* Kartu 4 – Predikat */
        .stat-predikat {
            background: linear-gradient(135deg, #FDF2F8, #FAF5FF);
            border: 1px solid #FBCFE8;
        }

        .stat-predikat .stat-pill-icon {
            background: linear-gradient(135deg, #EC4899, #9333EA);
        }

        /* ── Tombol cetak ────────────────────────────────────────────────── */
        .btn-cetak {
            background: #2563EB;
            border: none;
            color: #fff;
            border-radius: 8px;
            padding: .45rem 1rem;
            font-size: .82rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            transition: background .15s;
            cursor: pointer;
        }

        .btn-cetak:hover {
            background: #1D4ED8;
            color: #fff;
        }

        /* ════════════════════════════════════════════════════════════════════
       PRINT STYLES — Kartu Hafalan & Muroja'ah Resmi
    ════════════════════════════════════════════════════════════════════ */
        @page {
            size: A4 portrait;
            margin: 0;
        }

        @media print {

            /* ── Sembunyikan seluruh UI web ───────────────────────────────── */
            #adminSidebar,
            #adminTopbar,
            #sbOverlay,
            .no-print,
            .kartu-identitas,
            .card.border-0.shadow-sm.rounded-4 {
                display: none !important;
            }

            #adminContent {
                margin-left: 0 !important;
                padding-top: 0 !important;
            }

            .admin-topbar {
                display: none !important;
            }

            .admin-main {
                padding: 0 !important;
            }

            body {
                background: #fff !important;
                font-family: 'Plus Jakarta Sans', Arial, sans-serif;
                print-color-adjust: exact !important;
                -webkit-print-color-adjust: exact !important;
            }

            /* ── Kontainer kartu cetak ───────────────────────────────────── */
            .print-card-container {
                display: block !important;
                padding: 12mm 15mm;
                background: #FFFFFF;
                color: #000000;
                box-shadow: none !important;
            }

            /* ── Reset semua shadow & efek bawaan dalam kontainer ─────────── */
            .print-card-container * {
                box-shadow: none !important;
                text-shadow: none !important;
                print-color-adjust: exact !important;
                -webkit-print-color-adjust: exact !important;
            }

            /* ── Warna latar header tabel & badge tercetak penuh ─────────── */
            * {
                print-color-adjust: exact !important;
                -webkit-print-color-adjust: exact !important;
            }
        }
    </style>
@endsection

@section('content')

    {{-- ══════════════════════════════════════════════════════════════════
    KONTAINER KARTU CETAK (hanya tampil saat window.print())
    Tersembunyi di layar web normal.
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="print-card-container" style="display:none;">

        {{-- ── Kop Surat Resmi ──────────────────────────────────────── --}}
        <div
            style="display:flex; align-items:flex-start; gap:14px; padding-bottom:8px; border-bottom:1px solid #000000; margin-bottom:10px;">
            <img src="{{ asset('images/logo-sekolah.png') }}" alt="Logo SMA IT Al-Fadhilah"
                style="width:82px; height:82px; object-fit:contain; flex-shrink:0;">
            <div style="flex:1; text-align:center; line-height:1.45;">
                <div style="font-size:15pt; font-weight:700; color:#000000;">KARTU HAFALAN &amp; MUROJA'AH AL-QUR'AN</div>
                <div style="font-size:13pt; color:#000000;">PROGRAM TAHFIDZ AL-QUR'AN</div>
                <div style="font-size:13pt; color:#000000;">SMA IT AL-FADHILAH PEKANBARU</div>
                <div style="font-size:9pt; color:#334155;">Jl. Muhajirin No. RT/RW. 04/09 Kel. Sidomulyo Barat, Pekanbaru -
                    Riau 28294</div>
            </div>
        </div>

        {{-- ── Identitas Siswa 3 Baris ─────────────────────────────── --}}
        <div style="font-size:11pt; color:#000000; line-height:1.5; margin:10px 0 12px;">
            <div>
                <span style="display:inline-block; width:150px; font-weight:600;">Nama Siswa</span>
                : {{ $siswa->nama_lengkap }}
            </div>
            <div>
                <span style="display:inline-block; width:150px; font-weight:600;">NIS / Halaqah</span>
                : {{ $siswa->nip_nis }} / {{ $siswa->halaqah?->nama_halaqah ?? '-' }}
            </div>
            <div>
                <span style="display:inline-block; width:150px; font-weight:600;">Ustadz Pembimbing</span>
                : {{ $siswa->halaqah?->ustadz?->nama_lengkap ?? '-' }}
            </div>
        </div>

        {{-- ── Tabel Riwayat Hafalan & Muroja'ah ───────────────────── --}}
        <table style="width:100%; border-collapse:collapse; table-layout:fixed; margin-bottom:0;">
            <thead>
                <tr style="background:#267308; color:#FFFFFF;">
                    <th
                        style="width:7%;  border:1px solid #475569 !important; padding:6px 8px; font-size:9pt; font-weight:700; text-align:center;">
                        NO</th>
                    <th
                        style="width:35%; border:1px solid #475569 !important; padding:6px 8px; font-size:9pt; font-weight:700; text-align:left;">
                        NAMA SURAH</th>
                    <th
                        style="width:23%; border:1px solid #475569 !important; padding:6px 8px; font-size:9pt; font-weight:700; text-align:center;">
                        TANGGAL SETORAN</th>
                    <th
                        style="width:20%; border:1px solid #475569 !important; padding:6px 8px; font-size:9pt; font-weight:700; text-align:center;">
                        KATEGORI</th>
                    <th
                        style="width:15%; border:1px solid #475569 !important; padding:6px 8px; font-size:9pt; font-weight:700; text-align:center;">
                        INDEKS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($aktivitas as $j => $item)
                    @php
                        $bgKat = $item['kategori'] === 'Hafalan'
                            ? 'background:#BAFC8C; color:#086314;'
                            : 'background:#FFE6A8; color:#634F08;';
                        $nilaiPrint = $item['nilai'] ?? '–';
                        $indeksPrint = $item['indeks']?->value ?? '–';
                    @endphp
                    <tr>
                        <td
                            style="border:1px solid #475569 !important; padding:5px 8px; font-size:9pt; color:#000000; text-align:center;">
                            {{ $j + 1 }}</td>
                        <td
                            style="border:1px solid #475569 !important; padding:5px 8px; font-size:9pt; color:#000000; text-align:left;">
                            {{ $item['nama_surah'] }}</td>
                        <td
                            style="border:1px solid #475569 !important; padding:5px 8px; font-size:9pt; color:#000000; text-align:center;">
                            {{ $item['tanggal']->translatedFormat('d F Y') }}</td>
                        <td style="border:1px solid #475569 !important; padding:5px 8px; font-size:9pt; text-align:center;">
                            <span
                                style="{{ $bgKat }} border-radius:4px; padding:1px 7px; font-weight:700; font-size:8.5pt; display:inline-block;">{{ $item['kategori'] }}</span>
                        </td>
                        <td
                            style="border:1px solid #475569 !important; padding:5px 8px; font-size:9pt; color:#000000; text-align:center; font-weight:600;">
                            {{ $nilaiPrint }} ({{ $indeksPrint }})</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5"
                            style="border:1px solid #475569 !important; padding:10px 8px; font-size:9pt; color:#475569; text-align:center; font-style:italic;">
                            Belum ada riwayat hafalan atau murajaah untuk siswa ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background:#F1F5F9;">
                    <td colspan="4"
                        style="border:1px solid #475569 !important; padding:6px 10px; font-size:9pt; font-weight:700; color:#000000; text-align:right;">
                        TOTAL CATATAN
                    </td>
                    <td
                        style="border:1px solid #475569 !important; padding:6px 8px; font-size:9pt; font-weight:700; color:#000000; text-align:center;">
                        {{ $aktivitas->count() }} aktivitas
                    </td>
                </tr>
            </tfoot>
        </table>

        {{-- ── Tanda Tangan 2 Kolom ─────────────────────────────────── --}}
        <div style="display:flex; justify-content:space-between; margin-top:28px; font-size:9.5pt; color:#000000;">

            {{-- Sisi Kiri: Ustadz --}}
            <div style="text-align:left;">
                <div style="font-weight:600;">MENYETUJUI</div>
                <div>USTADZ PEMBIMBING,</div>
                <div style="height:56px;"></div>
                <div style="border-top:1px solid #000000; padding-top:3px; display:inline-block; min-width:180px;">
                    {{ $siswa->halaqah?->ustadz?->nama_lengkap ?? '-' }}
                </div>
            </div>

            {{-- Sisi Kanan: Siswa --}}
            <div style="text-align:left; min-width:200px;">
                <div style="font-weight:600;">PEKANBARU,
                    {{ strtoupper(now()->timezone('Asia/Jakarta')->translatedFormat('d F Y')) }}</div>
                <div>SISWA YBS,</div>
                <div style="height:56px;"></div>
                <div style="border-top:1px solid #000000; padding-top:3px; display:inline-block; min-width:180px;">
                    {{ $siswa->nama_lengkap }}
                </div>
            </div>

        </div>

    </div>
    {{-- /print-card-container --}}

    {{-- ── Header & Tombol Aksi ────────────────────────────────────────── --}}
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4 no-print">
        <div>
            <h2 class="fw-bold mb-1 h4" style="color:#000000;">
                <i class="bi bi-clipboard2-data me-2"></i>Detail Laporan Siswa
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.laporan-tahfiz.index') }}" class="text-decoration-none">Laporan Tahfiz</a>
                    </li>
                    <li class="breadcrumb-item active">Detail Siswa</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.laporan-tahfiz.index') }}" class="btn btn-sm btn-outline-secondary fw-semibold">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
            <button type="button" class="btn-cetak" onclick="window.print()">
                <i class="bi bi-printer-fill"></i>Cetak Kartu Hafalan
            </button>
        </div>
    </div>

    {{-- ── Kartu Identitas Siswa + Statistik ───────────────────────────── --}}
    <div class="kartu-identitas mb-4">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
            <div>
                <div class="fw-bold mb-1" style="font-size:1.15rem; color:#000000;">{{ $siswa->nama_lengkap }}</div>
                <div class="d-flex flex-wrap align-items-center gap-2 mt-2 small">
                    <span style="color:#64748B;"><i class="bi bi-credit-card me-1"></i>NIS: {{ $siswa->nip_nis }}</span>
                    <span
                        style="background:#F3E8FF; color:#7E22CE; border-radius:999px; padding:4px 12px; font-size:.75rem; font-weight:600;">
                        <i class="bi bi-collection me-1"></i>{{ $siswa->halaqah?->nama_halaqah ?? '–' }}
                    </span>
                    <span
                        style="background:#E0F2FE; color:#0369A1; border-radius:999px; padding:4px 12px; font-size:.75rem; font-weight:600;">
                        <i class="bi bi-person-check me-1"></i>{{ $siswa->halaqah?->ustadz?->nama_lengkap ?? '–' }}
                    </span>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @php
                    $pills = [
                        ['label' => 'Total Setoran',  'nilai' => $stat['total_setoran'],          'icon' => 'bi-journal-bookmark-fill', 'cls' => 'stat-setoran'],
                        ['label' => 'Total Murajaah', 'nilai' => $stat['total_murajaah'],         'icon' => 'bi-arrow-repeat',          'cls' => 'stat-murajaah'],
                        ['label' => 'Rata-Rata Nilai', 'nilai' => $stat['rata_nilai'] . '%',       'icon' => 'bi-graph-up-arrow',        'cls' => 'stat-rata'],
                        ['label' => 'Predikat',        'nilai' => $stat['indeks']->value,          'icon' => 'bi-award-fill',            'cls' => 'stat-predikat'],
                    ];
                @endphp
                @foreach($pills as $st)
                    <div class="stat-pill {{ $st['cls'] }} text-center">
                        <div class="stat-pill-icon mx-auto mb-1">
                            <i class="bi {{ $st['icon'] }}"></i>
                        </div>
                        <div class="fw-bold" style="font-size:1rem; line-height:1; color:#000000;">{{ $st['nilai'] }}</div>
                        <div style="font-size:.65rem; color:#475569;">{{ $st['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Filter Instan (disembunyikan saat print) ────────────────────── --}}
    <div class="d-flex flex-wrap align-items-center gap-3 mb-3 no-print">
        {{-- Tab kategori --}}
        <div class="tab-filter" role="group" aria-label="Filter kategori">
            <button type="button" class="aktif" onclick="filterAktivitas(this,'semua')">
                Semua
                <span class="badge ms-1 rounded-pill"
                    style="background:rgba(255,255,255,.25); font-size:.65rem;">{{ $aktivitas->count() }}</span>
            </button>
            <button type="button" onclick="filterAktivitas(this,'Hafalan')">
                Hafalan
                <span class="badge ms-1 rounded-pill"
                    style="background:rgba(255,255,255,.25); font-size:.65rem;">{{ $aktivitas->where('kategori', 'Hafalan')->count() }}</span>
            </button>
            <button type="button" onclick="filterAktivitas(this,'Murajaah')">
                Murajaah
                <span class="badge ms-1 rounded-pill"
                    style="background:rgba(255,255,255,.25); font-size:.65rem;">{{ $aktivitas->where('kategori', 'Murajaah')->count() }}</span>
            </button>
        </div>

        {{-- Pencarian surah --}}
        <div class="input-group input-group-sm" style="max-width:240px;">
            <span class="input-group-text bg-white" style="border-color:#A7F3D0;">
                <i class="bi bi-search text-muted" style="font-size:.8rem;"></i>
            </span>
            <input type="text" id="cariSurah" class="form-control border-start-0"
                style="border-color:#A7F3D0; font-size:.82rem;" placeholder="Cari nama surah…"
                oninput="cariSurah(this.value)">
        </div>
    </div>

    {{-- ── Tabel Detail Riwayat ─────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0 tbl-detail" id="tblAktivitas">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-uppercase" style="width:6%; color:#000000;">No</th>
                        <th class="px-3 py-3 text-uppercase" style="width:28%; color:#000000;">Nama Surah</th>
                        <th class="px-3 py-3 text-uppercase" style="width:18%; color:#000000;">Tanggal</th>
                        <th class="px-3 py-3 text-uppercase text-center" style="width:14%; color:#000000;">Kategori</th>
                        <th class="px-4 py-3 text-uppercase text-center" style="width:34%; color:#000000;">Kualitas Hafalan
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($aktivitas as $i => $item)
                        @php
                            /** @var \App\Enums\NilaiIndeks $indeks */
                            $indeks = $item['indeks'];
                            $kVal   = $indeks?->value ?? '–';
                            $kBg = match($kVal) {
                                'A', 'AB' => ['bg'=>'#D1FAE5','color'=>'#065F46','border'=>'#6EE7B7'],
                                'B', 'BC' => ['bg'=>'#DBEAFE','color'=>'#1D4ED8','border'=>'#93C5FD'],
                                'C'       => ['bg'=>'#FEF9C3','color'=>'#92400E','border'=>'#FDE68A'],
                                'D', 'E'  => ['bg'=>'#FEE2E2','color'=>'#991B1B','border'=>'#FCA5A5'],
                                default   => ['bg'=>'#F3F4F6','color'=>'#374151','border'=>'#D1D5DB'],
                            };
                            $katClass = $item['kategori'] === 'Hafalan' ? 'badge-hafalan' : 'badge-murajaah';
                        @endphp
                        <tr data-kategori="{{ $item['kategori'] }}" data-surah="{{ strtolower($item['nama_surah']) }}">
                            <td class="px-4 py-3 text-muted small text-start">{{ $i + 1 }}</td>

                            <td class="px-3 py-3 text-start">
                                <div class="fw-semibold small" style="color:#1F4172;">
                                    {{ $item['nama_surah'] }}
                                </div>
                                @if($item['catatan'])
                                    <div class="text-muted" style="font-size:.7rem;">
                                        {{ Str::limit($item['catatan'], 60) }}
                                    </div>
                                @endif
                            </td>

                            <td class="px-3 py-3 small text-muted text-start">
                                {{ $item['tanggal']->translatedFormat('d F Y') }}
                            </td>

                            <td class="px-3 py-3 text-center">
                                <span class="badge-kategori {{ $katClass }}">
                                    {{ $item['kategori'] }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-center">
                                <span class="badge-indeks"
                                    style="background:{{ $kBg['bg'] }}; color:{{ $kBg['color'] }}; border:1px solid {{ $kBg['border'] }};">
                                    {{ $kVal }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-5 text-center text-muted small">
                                <i class="bi bi-journal-x display-6 d-block mb-2 opacity-25"></i>
                                Belum ada riwayat hafalan atau murajaah untuk siswa ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        var filterKategoriAktif = 'semua';
        var filterSurahAktif = '';

        function terapkanFilter() {
            var rows = document.querySelectorAll('#tblAktivitas tbody tr[data-kategori]');
            rows.forEach(function (row) {
                var cocokKategori = filterKategoriAktif === 'semua' ||
                    row.dataset.kategori === filterKategoriAktif;
                var cocokSurah = !filterSurahAktif ||
                    row.dataset.surah.includes(filterSurahAktif);
                row.classList.toggle('hidden-row', !(cocokKategori && cocokSurah));
            });
        }

        function filterAktivitas(btn, kategori) {
            document.querySelectorAll('.tab-filter button').forEach(function (b) {
                b.classList.remove('aktif');
            });
            btn.classList.add('aktif');
            filterKategoriAktif = kategori;
            terapkanFilter();
        }

        function cariSurah(q) {
            filterSurahAktif = q.toLowerCase().trim();
            terapkanFilter();
        }
    </script>
@endsection