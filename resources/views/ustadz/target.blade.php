@extends('layouts.ustadz', ['title' => 'Target Hafalan'])

@section('breadcrumb', 'Target Hafalan')

@section('styles')
<style>
.target-panel {
    border-radius: 16px;
    border: 1px solid #E2E8F0;
    box-shadow: 0 2px 10px rgba(0,0,0,0.03);
}
.target-siswa-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 14px;
    border-radius: 12px;
    border: 1px solid transparent;
    text-decoration: none;
    cursor: pointer;
    transition: all .15s ease;
    background: transparent;
    width: 100%;
    text-align: left;
}
.target-siswa-card:hover {
    background-color: #F8FAFC;
    border-color: #E2E8F0;
}
.target-siswa-card.aktif {
    background-color: #F0FDF9;
    border-color: #11756D;
    border-left: 4px solid #11756D;
}
.preset-pill-btn {
    border: 1px solid #E2E8F0;
    background: #FFFFFF;
    color: #475569;
    font-size: 12px;
    font-weight: 500;
    padding: 5px 13px;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
}
.preset-pill-btn:hover {
    border-color: #11756D;
    background: #F0FDF9;
    color: #11756D;
}
.preset-pill-btn.active {
    border-color: #11756D;
    background: #11756D;
    color: #FFFFFF;
}
.duration-pill-btn {
    border: 1px solid #E2E8F0;
    background: #FFFFFF;
    color: #64748B;
    font-size: 11.5px;
    font-weight: 500;
    padding: 3px 9px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.15s ease;
}
.duration-pill-btn:hover, .duration-pill-btn.active {
    border-color: #11756D;
    background: #F0FDF9;
    color: #11756D;
    font-weight: 600;
}
.target-field {
    border-radius: 10px;
    border: 1px solid #CBD5E1;
    padding: 9px 12px;
    font-size: 13.5px;
    background-color: #FFFFFF;
}
.target-field:focus {
    border-color: #11756D;
    box-shadow: 0 0 0 3px rgba(17, 117, 109, 0.12);
}
.target-select optgroup {
    font-weight: 700;
    color: #11756D;
    background: #F8FAFC;
}
.target-select option {
    font-weight: 500;
    color: #1E293B;
    background: #FFFFFF;
    padding: 4px 8px;
}
</style>
@endsection

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
            <div class="fw-semibold mb-1">Gagal menyimpan target:</div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    </div>
@endif

{{-- ── Header Banner ──────────────────────────────────────────────────── --}}
<div class="mb-4">
    <span class="badge px-3 py-1 text-white mb-2 fw-bold" style="background-color: #11756D;">PENETAPAN TARGET</span>
    <h2 class="fw-bold mb-1" style="color: #1E293B; font-size: 1.65rem;">Target Hafalan Siswa</h2>
    <p class="text-muted small mb-0">Tetapkan sasaran juz atau surah dan rentang tanggal target bagi siswa halaqah binaan Anda.</p>
</div>

<div class="row g-4 align-items-start">

    {{-- ══ PANEL KIRI: DAFTAR PILIH SISWA ══════════════════════════════════ --}}
    <div class="col-12 col-lg-4">
        <div class="target-panel p-3 bg-white">
            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-person text-secondary"></i>
                <span>Pilih Siswa ({{ $santriList->count() }})</span>
            </h6>
            <div class="mb-3 position-relative">
                <i class="bi bi-search position-absolute text-muted" style="top: 13px; left: 14px; font-size: 14px;"></i>
                <input type="text" id="cariSantriTarget" class="form-control ps-5"
                       placeholder="Cari nama atau NIS..."
                       style="height: 42px; border-radius: 10px; background-color: #F1F5F9; border: 1px solid #E2E8F0; font-size: 13.5px;">
            </div>

            <div class="d-flex flex-column gap-1 overflow-y-auto" style="max-height: 560px;" id="listSantriTarget">
                @forelse($santriList as $s)
                    @php
                        $hasTarget = isset($targetAktifMap[$s->id]);
                        $isAktif = $selectedSiswa && $selectedSiswa->id === $s->id;
                        $kata = explode(' ', trim($s->nama_lengkap));
                        $inisial = strtoupper(substr($kata[0], 0, 1));
                        if (isset($kata[1])) $inisial .= strtoupper(substr($kata[1], 0, 1));
                    @endphp
                    <button type="button"
                            class="target-siswa-card {{ $isAktif ? 'aktif' : '' }}"
                            data-id="{{ $s->id }}"
                            data-nama="{{ $s->nama_lengkap }}"
                            data-nis="{{ $s->nip_nis }}"
                            data-inisial="{{ $inisial }}"
                            onclick="pilihSantriTarget({{ $s->id }}, '{{ addslashes($s->nama_lengkap) }}', '{{ addslashes($s->nip_nis) }}', '{{ $inisial }}', this)">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width: 40px; height: 40px; background: {{ $isAktif ? '#11756D' : '#E2E8F0' }}; color: {{ $isAktif ? '#fff' : '#64748B' }}; font-weight: bold; font-size: 13.5px;"
                             id="avatarSantriCard-{{ $s->id }}">
                            {{ $inisial }}
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="fw-bold text-dark text-truncate" style="font-size: 13.5px;">{{ $s->nama_lengkap }}</div>
                            <div class="small text-muted text-truncate" style="font-size: 11.5px;">NIS: {{ $s->nip_nis }}</div>
                        </div>
                        @if($hasTarget)
                            <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2 py-0.5" style="font-size: 10px;">
                                Aktif
                            </span>
                        @endif
                    </button>
                @empty
                    <div class="text-center py-4 text-muted small">
                        Belum ada siswa dalam halaqah ini.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ══ PANEL KANAN: FORM TAMBAH TARGET & DETAIL TARGET AKTIF ═════════ --}}
    <div class="col-12 col-lg-8">
        <div class="target-panel p-4 bg-white">

            {{-- 1. Empty State Sebelum Siswa Dipilih --}}
            <div id="targetEmptyState" class="text-center py-5 my-auto" style="{{ $selectedSiswa ? 'display: none;' : '' }}">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center p-3 mb-3"
                     style="width: 56px; height: 56px; background: #F1F5F9; color: #64748B;">
                    <i class="bi bi-person-check fs-3"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1" style="font-size: 15px;">Pilih Siswa Terlebih Dahulu</h5>
                <p class="text-muted small mb-0" style="max-width: 380px; margin: 0 auto;">
                    Klik salah satu siswa di panel kiri untuk melihat target aktif atau menetapkan target hafalan baru.
                </p>
            </div>

            {{-- 2. Area Formulir dan Detail (Tampil Otomatis Saat Siswa Dipilih) --}}
            <div id="targetFormArea" style="{{ $selectedSiswa ? '' : 'display: none;' }}">

                {{-- Header Form dengan Badge Santri Terpilih --}}
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pb-3 mb-3 border-bottom">
                    <div>
                        <h4 class="fw-bold mb-0 text-dark" style="font-size: 1.25rem;">Tetapkan Target Hafalan</h4>
                        <small class="text-muted">Komitmen capaian surah dan tenggat waktu evaluasi santri</small>
                    </div>
                    <div class="badge rounded-pill d-inline-flex align-items-center px-3 py-1.5 fw-semibold"
                         style="background-color: #E6F4F1; color: #11756D; font-size: 12px; gap: 6px;">
                        <i class="bi bi-person-fill"></i>
                        <span id="targetBadgeNama">{{ $selectedSiswa?->nama_lengkap ?? '-' }}</span>
                        <span class="opacity-75">•</span>
                        <span id="targetBadgeNis">NIS: {{ $selectedSiswa?->nip_nis ?? '-' }}</span>
                    </div>
                </div>

                {{-- Card Target Aktif Santri (Desain Elegan, Bersih & Tenang) --}}
                <div id="targetAktifSantriBox" class="p-3 rounded-3 mb-4"
                     style="background: #F8FAFC; border: 1px solid #E2E8F0; border-left: 4px solid #11756D; {{ $targetAktif ? '' : 'display: none;' }}">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1.5">
                        <span class="text-uppercase fw-bold text-muted small" style="font-size: 11px; letter-spacing: 0.05em;">
                            <i class="bi bi-bullseye text-success me-1"></i>Target Aktif Saat Ini
                        </span>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-0.5" style="font-size: 11px;" id="infoTargetBadgeStatus">
                                Aktif
                            </span>
                            <form id="formTandaiSelesai" action="{{ $targetAktif ? route('ustadz.target.selesai', $targetAktif->id) : '#' }}" method="POST"
                                  onsubmit="return confirm('Tandai target hafalan ini sebagai Selesai?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2.5 py-0.5 fw-semibold" style="font-size: 11px;">
                                    <i class="bi bi-check2 me-1"></i>Tandai Selesai
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="fw-bold text-dark mb-1" style="font-size: 15px;" id="infoTargetSasaran">
                        {{ $targetAktif ? $targetAktif->surah_awal . ' s.d. ' . $targetAktif->surah_akhir : '-' }}
                    </div>
                    <div class="text-muted small d-flex flex-wrap align-items-center gap-2" style="font-size: 12.5px;">
                        <span><i class="bi bi-calendar3 me-1"></i><span id="infoTargetRentangTanggal">{{ $targetAktif ? ($targetAktif->tanggal_mulai?->translatedFormat('d M Y') . ' s.d. ' . $targetAktif->tanggal_akhir?->translatedFormat('d M Y')) : '-' }}</span></span>
                        <span class="text-muted opacity-50">•</span>
                        <span id="infoTargetSisa" class="fw-semibold text-success">{{ $hariSisa !== null ? ($hariSisa >= 0 ? "Sisa $hariSisa Hari" : "Terlewat " . abs($hariSisa) . " Hari") : 'Aktif' }}</span>
                    </div>
                </div>

                {{-- Notice Jika Belum Ada Target Aktif --}}
                <div id="targetBelumAdaBox" class="p-3 rounded-3 mb-4 text-muted small"
                     style="background: #F8FAFC; border: 1px solid #E2E8F0; {{ $targetAktif ? 'display: none;' : '' }}">
                    <i class="bi bi-info-circle me-1 text-secondary"></i>
                    <span>Belum ada target aktif untuk santri ini. Gunakan formulir di bawah ini untuk menetapkan target baru.</span>
                </div>

                {{-- Template Cepat Target (Desain Minimalis & Profesional) --}}
                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold" style="font-size: 12px;">Pilihan Cepat Target:</span>
                    </div>
                    <div class="d-flex flex-wrap gap-1.5" id="presetButtonsContainer">
                        <button type="button" class="preset-pill-btn" onclick="applyPresetTarget('juz30_full', this)">
                            Juz 30 (Lengkap)
                        </button>
                        <button type="button" class="preset-pill-btn" onclick="applyPresetTarget('juz30_pendek', this)">
                            Juz 30 (Adh-Dhuha – An-Nas)
                        </button>
                        <button type="button" class="preset-pill-btn" onclick="applyPresetTarget('juz1_full', this)">
                            Juz 1 (Al-Fatihah – Al-Baqarah)
                        </button>
                        <button type="button" class="preset-pill-btn" onclick="applyPresetTarget('juz2_full', this)">
                            Juz 2 (Al-Baqarah lanjutan)
                        </button>
                    </div>
                </div>

                {{-- Formulir Input Target Baru --}}
                <form action="{{ route('ustadz.target.store') }}" method="POST" id="formTetapkanTarget">
                    @csrf
                    <input type="hidden" name="siswa_id" id="targetInputSiswaId" value="{{ $selectedSiswa?->id ?? '' }}">

                    <div class="row g-3 mb-3">
                        {{-- Kolom 1: Mulai Hafalan (Optgroup per Juz) --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-dark mb-1">
                                Mulai Hafalan (Batas Awal) <span class="text-danger">*</span>
                            </label>
                            <select name="surah_awal" id="targetSurahAwal" class="form-select target-field target-select" required>
                                <option value="" disabled selected>-- Pilih Batas Awal --</option>
                                @foreach($kurikulumTarget as $juzNama => $daftarOpsi)
                                    <optgroup label="{{ $juzNama }}">
                                        @foreach($daftarOpsi as $opsiTeks)
                                            <option value="{{ $opsiTeks }}">{{ $opsiTeks }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        {{-- Kolom 2: Tanggal Mulai --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-dark mb-1">
                                Tanggal Mulai <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="tanggal_mulai" id="targetTanggalMulai"
                                   class="form-control target-field"
                                   value="{{ date('Y-m-d') }}" required onchange="onTanggalMulaiChange()">
                        </div>

                        {{-- Kolom 3: Batas Akhir Hafalan (Optgroup per Juz) --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-dark mb-1">
                                Batas Akhir Hafalan (Sasaran) <span class="text-danger">*</span>
                            </label>
                            <select name="surah_akhir" id="targetSurahAkhir" class="form-select target-field target-select" required>
                                <option value="" disabled selected>-- Pilih Batas Akhir --</option>
                                @foreach($kurikulumTarget as $juzNama => $daftarOpsi)
                                    <optgroup label="{{ $juzNama }}">
                                        @foreach($daftarOpsi as $opsiTeks)
                                            <option value="{{ $opsiTeks }}">{{ $opsiTeks }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        {{-- Kolom 4: Tanggal Akhir Target + Pilihan Cepat Durasi --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-semibold text-dark mb-1">
                                Batas Akhir Target <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="tanggal_akhir" id="targetTanggalAkhir"
                                   class="form-control target-field"
                                   value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>

                            <div class="d-flex flex-wrap align-items-center gap-1.5 mt-2">
                                <span class="text-muted" style="font-size: 11px;">Durasi:</span>
                                <button type="button" class="duration-pill-btn" onclick="setDurasiTarget(14, this)">+14 Hari</button>
                                <button type="button" class="duration-pill-btn active" onclick="setDurasiTarget(30, this)">+30 Hari</button>
                                <button type="button" class="duration-pill-btn" onclick="setDurasiTarget(60, this)">+60 Hari</button>
                                <button type="button" class="duration-pill-btn" onclick="setDurasiTarget(90, this)">+90 Hari</button>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol Aksi Simpan Target --}}
                    <div class="d-flex align-items-center gap-2 pt-3 border-top">
                        <button type="submit" class="btn text-white fw-bold rounded-pill px-4 py-2 shadow-sm"
                                style="background-color: #11756D; font-size: 13.5px;">
                            <i class="bi bi-check2 me-1"></i>Simpan Target Baru
                        </button>
                        <button type="button" class="btn btn-light rounded-pill px-4 py-2 fw-semibold text-secondary"
                                style="font-size: 13.5px;" onclick="batalPilihTarget()">
                            Batal
                        </button>
                    </div>
                </form>

                {{-- Riwayat Penetapan Target Siswa Terpilih --}}
                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-dark" style="font-size: 13.5px;">
                            <i class="bi bi-clock-history me-1 text-secondary"></i>Riwayat Target Siswa Ini
                        </span>
                    </div>
                    <div class="table-responsive" style="border: 1px solid #E2E8F0; border-radius: 10px; overflow: hidden;">
                        <table class="table table-sm table-hover align-middle mb-0" style="font-size: 12.5px;">
                            <thead style="background: #F8FAFC;">
                                <tr style="font-size: 11.5px; color: #64748B; text-transform: uppercase; letter-spacing: 0.03em;">
                                    <th class="ps-3 py-2 fw-semibold" style="width: 40px;">No</th>
                                    <th class="py-2 fw-semibold">Rentang Target</th>
                                    <th class="py-2 fw-semibold" style="width: 200px;">Jadwal Target</th>
                                    <th class="py-2 pe-3 fw-semibold text-center" style="width: 100px;">Status</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyRiwayatTarget">
                                @forelse($riwayatTarget as $idx => $tgt)
                                    <tr>
                                        <td class="ps-3 text-muted small">{{ $idx + 1 }}</td>
                                        <td>
                                            <span class="fw-semibold text-dark">{{ $tgt->surah_awal }} &rarr; {{ $tgt->surah_akhir }}</span>
                                        </td>
                                        <td>
                                            <span class="text-secondary small">
                                                {{ $tgt->tanggal_mulai?->translatedFormat('d M Y') }} s.d. {{ $tgt->tanggal_akhir?->translatedFormat('d M Y') }}
                                            </span>
                                        </td>
                                        <td class="pe-3 text-center">
                                            @if($tgt->status?->value === 'Aktif')
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 rounded-pill">Aktif</span>
                                            @elseif($tgt->status?->value === 'Selesai')
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0.5 rounded-pill">Selesai</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-0.5 rounded-pill">Nonaktif</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-muted small">
                                            Belum ada riwayat penetapan target untuk siswa ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

{{-- ══ TABEL MONITORING TARGET SELURUH SANTRI ════════════════════════════ --}}
<div class="mt-4">
    <div class="card p-4 border-0 shadow-sm rounded-4 bg-white">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom gap-2">
            <div>
                <h5 class="fw-bold mb-1 text-dark" style="font-size: 15px;">
                    <i class="bi bi-table text-success me-1.5"></i>Status Target Seluruh Siswa Halaqah
                </h5>
                <p class="text-muted small mb-0">Daftar lengkap komitmen hafalan siswa yang sedang berlangsung di Halaqah {{ $halaqah?->nama_halaqah ?? 'Ustadz' }}.</p>
            </div>
            <span class="badge bg-light text-muted border px-3 py-1.5 fw-semibold" style="font-size: 12px;">
                {{ $santriList->count() }} Total Siswa
            </span>
        </div>

        <div class="table-responsive rounded-3 border">
            <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                <thead class="table-light">
                    <tr class="small text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        <th class="ps-3 py-2.5" style="width: 50px;">No</th>
                        <th class="py-2.5">Siswa</th>
                        <th class="py-2.5">Target Sasaran</th>
                        <th class="py-2.5">Rentang Tanggal</th>
                        <th class="py-2.5 text-center">Status</th>
                        <th class="pe-3 py-2.5 text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($santriList as $idx => $st)
                        @php
                            $tgtData = $targetAktifMap->get($st->id);
                        @endphp
                        <tr class="santri-row-monitoring" style="cursor: pointer;" onclick="fokuskanSantri({{ $st->id }})">
                            <td class="ps-3 text-muted small">{{ $idx + 1 }}.</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $st->nama_lengkap }}</div>
                                <div class="text-muted small">NIS: {{ $st->nip_nis }}</div>
                            </td>
                            <td>
                                @if($tgtData)
                                    <span class="fw-semibold text-dark">{{ $tgtData->surah_awal }} &rarr; {{ $tgtData->surah_akhir }}</span>
                                @else
                                    <span class="text-muted small">Belum ada target</span>
                                @endif
                            </td>
                            <td>
                                @if($tgtData)
                                    <div class="small text-secondary">
                                        {{ $tgtData->tanggal_mulai?->translatedFormat('d M Y') }} s.d. {{ $tgtData->tanggal_akhir?->translatedFormat('d M Y') }}
                                    </div>
                                @else
                                    <span class="text-muted small">–</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($tgtData)
                                    <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2.5 py-0.5" style="font-size: 11px;">
                                        Target Aktif
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-light text-muted border px-2.5 py-0.5" style="font-size: 11px;">
                                        Kosong
                                    </span>
                                @endif
                            </td>
                            <td class="pe-3 text-center">
                                <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 py-0.5 fw-semibold" style="font-size: 11.5px;"
                                        onclick="event.stopPropagation(); fokuskanSantri({{ $st->id }})">
                                    <i class="bi bi-pencil-square me-1"></i>Kelola
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted small">
                                Belum ada siswa dalam halaqah ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Peta data siswa dan target untuk interaktivitas instan tanpa refresh
const targetAktifMapData = @json($targetAktifMap);
const riwayatTargetMapData = @json($riwayatTargetMap);
let santriTargetTerpilih = null;

// Helper: Format tanggal bersih '26 Nov 2026' (bukan ISO timestamp mentah)
function fmtTglID(str) {
    if (!str) return '-';
    const clean = String(str).substring(0, 10);
    const parts = clean.split('-');
    if (parts.length === 3) {
        const d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
        if (!isNaN(d.getTime())) {
            return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        }
    }
    return clean;
}

function setSelectOptionByText(selectEl, textSnippet) {
    if (!selectEl) return;
    const lower = textSnippet.toLowerCase();
    for (let i = 0; i < selectEl.options.length; i++) {
        if (selectEl.options[i].text.toLowerCase().includes(lower)) {
            selectEl.selectedIndex = i;
            return;
        }
    }
}

// ── Pilihan Cepat Preset Target Populer ───────────────────────────────
function applyPresetTarget(presetKey, btnEl) {
    const selAwal  = document.getElementById('targetSurahAwal');
    const selAkhir = document.getElementById('targetSurahAkhir');
    if (!selAwal || !selAkhir) return;

    if (presetKey === 'juz30_full') {
        setSelectOptionByText(selAwal, 'An-Naba');
        setSelectOptionByText(selAkhir, 'An-Nas');
    } else if (presetKey === 'juz30_pendek') {
        setSelectOptionByText(selAwal, 'Adh-Dhuha');
        setSelectOptionByText(selAkhir, 'An-Nas');
    } else if (presetKey === 'juz1_full') {
        setSelectOptionByText(selAwal, 'Al-Fatihah');
        setSelectOptionByText(selAkhir, 'Al-Baqarah (Ayat 1');
        if (selAkhir.selectedIndex <= 0) setSelectOptionByText(selAkhir, 'Al-Baqarah');
    } else if (presetKey === 'juz2_full') {
        setSelectOptionByText(selAwal, 'Al-Baqarah (Ayat 142');
        setSelectOptionByText(selAkhir, 'Al-Baqarah (Ayat 142');
    }

    document.querySelectorAll('.preset-pill-btn').forEach(b => b.classList.remove('active'));
    if (btnEl) btnEl.classList.add('active');
}

// ── Pilihan Cepat Durasi Tanggal ──────────────────────────────────────
function setDurasiTarget(days, btnEl) {
    const tglMulaiEl = document.getElementById('targetTanggalMulai');
    const tglAkhirEl = document.getElementById('targetTanggalAkhir');
    if (!tglMulaiEl || !tglAkhirEl) return;

    let baseDate = tglMulaiEl.value ? new Date(tglMulaiEl.value) : new Date();
    if (isNaN(baseDate.getTime())) baseDate = new Date();

    const endDate = new Date(baseDate.getTime() + days * 24 * 60 * 60 * 1000);
    const yyyy = endDate.getFullYear();
    const mm = String(endDate.getMonth() + 1).padStart(2, '0');
    const dd = String(endDate.getDate()).padStart(2, '0');
    tglAkhirEl.value = `${yyyy}-${mm}-${dd}`;

    document.querySelectorAll('.duration-pill-btn').forEach(b => b.classList.remove('active'));
    if (btnEl) btnEl.classList.add('active');
}

function onTanggalMulaiChange() {
    const activeDurBtn = document.querySelector('.duration-pill-btn.active');
    if (activeDurBtn) {
        const text = activeDurBtn.innerText;
        const match = text.match(/\d+/);
        if (match) {
            setDurasiTarget(parseInt(match[0], 10), activeDurBtn);
        }
    }
}

// ── Klik Kartu Siswa di Panel Kiri ────────────────────────────────────
function pilihSantriTarget(id, nama, nis, inisial, el) {
    santriTargetTerpilih = { id, nama, nis, inisial };

    // Update kartu aktif di panel kiri
    document.querySelectorAll('.target-siswa-card').forEach(card => card.classList.remove('aktif'));
    if (el) el.classList.add('aktif');

    // Sembunyikan empty state dan munculkan form area
    document.getElementById('targetEmptyState').style.display = 'none';
    document.getElementById('targetFormArea').style.display   = 'block';

    // Update identitas nama siswa di badge form
    document.getElementById('targetInputSiswaId').value = id;
    document.getElementById('targetBadgeNama').textContent = nama;
    document.getElementById('targetBadgeNis').textContent = 'NIS: ' + (nis || '-');

    // Cek apakah siswa ini memiliki target aktif
    const targetAktif = targetAktifMapData[id] || null;
    const boxAktif = document.getElementById('targetAktifSantriBox');
    const boxBelum = document.getElementById('targetBelumAdaBox');
    const formTandai = document.getElementById('formTandaiSelesai');

    if (targetAktif) {
        document.getElementById('infoTargetSasaran').textContent = targetAktif.surah_awal + ' s.d. ' + targetAktif.surah_akhir;
        
        const tglM = fmtTglID(targetAktif.tanggal_mulai);
        const tglA = fmtTglID(targetAktif.tanggal_akhir);
        document.getElementById('infoTargetRentangTanggal').textContent = `${tglM} s.d. ${tglA}`;

        // Hitung sisa hari
        let sisaHariText = 'Aktif';
        if (targetAktif.tanggal_akhir) {
            const rawAkhir = targetAktif.tanggal_akhir.substring(0, 10);
            const akhirDate = new Date(rawAkhir);
            const now = new Date();
            now.setHours(0,0,0,0);
            if (!isNaN(akhirDate.getTime())) {
                const diffTime = akhirDate - now;
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                sisaHariText = diffDays >= 0 ? `Sisa ${diffDays} Hari` : `Terlewat ${Math.abs(diffDays)} Hari`;
            }
        }
        document.getElementById('infoTargetSisa').textContent = sisaHariText;

        if (formTandai) formTandai.action = '/ustadz/target/' + targetAktif.id + '/selesai';
        boxAktif.style.display = 'block';
        boxBelum.style.display = 'none';
    } else {
        boxAktif.style.display = 'none';
        boxBelum.style.display = 'block';
    }

    // Update tabel riwayat target siswa ini
    const riwayat = riwayatTargetMapData[id] || [];
    const tbody = document.getElementById('tbodyRiwayatTarget');
    if (tbody) {
        if (riwayat.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center py-3 text-muted small">Belum ada riwayat penetapan target untuk siswa ini.</td></tr>`;
        } else {
            tbody.innerHTML = riwayat.map((tgt, idx) => {
                let badgeStatus = '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-0.5 rounded-pill">Nonaktif</span>';
                if (tgt.status === 'Aktif') {
                    badgeStatus = '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 rounded-pill">Aktif</span>';
                } else if (tgt.status === 'Selesai') {
                    badgeStatus = '<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0.5 rounded-pill">Selesai</span>';
                }
                const tglMulai = fmtTglID(tgt.tanggal_mulai);
                const tglAkhir = fmtTglID(tgt.tanggal_akhir);

                return `<tr>
                    <td class="ps-3 text-muted small">${idx + 1}</td>
                    <td><span class="fw-semibold text-dark">${tgt.surah_awal} &rarr; ${tgt.surah_akhir}</span></td>
                    <td><span class="text-secondary small">${tglMulai} s.d. ${tglAkhir}</span></td>
                    <td class="pe-3 text-center">${badgeStatus}</td>
                </tr>`;
            }).join('');
        }
    }
}

function batalPilihTarget() {
    santriTargetTerpilih = null;
    document.querySelectorAll('.target-siswa-card').forEach(card => card.classList.remove('aktif'));
    document.getElementById('targetEmptyState').style.display = 'block';
    document.getElementById('targetFormArea').style.display   = 'none';

    const form = document.getElementById('formTetapkanTarget');
    if (form) form.reset();
    document.getElementById('targetInputSiswaId').value = '';
}

function fokuskanSantri(id) {
    const card = document.querySelector(`.target-siswa-card[data-id="${id}"]`);
    if (card) {
        card.click();
        card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

// ── Pencarian Real-Time Siswa di Panel Kiri ───────────────────────────
document.getElementById('cariSantriTarget')?.addEventListener('input', function () {
    const val = this.value.trim().toLowerCase();
    document.querySelectorAll('.target-siswa-card').forEach(card => {
        const nama = (card.getAttribute('data-nama') || '').toLowerCase();
        const nis  = (card.getAttribute('data-nis')  || '').toLowerCase();
        card.style.display = (nama.includes(val) || nis.includes(val)) ? 'flex' : 'none';
    });
});

// ── Auto-pilih siswa awal jika ada siswa_id di URL ───────────────────
@if($selectedSiswa)
window.addEventListener('DOMContentLoaded', () => {
    const cardAktif = document.querySelector('.target-siswa-card[data-id="{{ $selectedSiswa->id }}"]');
    if (cardAktif) {
        cardAktif.click();
    }
});
@endif
</script>
@endsection
