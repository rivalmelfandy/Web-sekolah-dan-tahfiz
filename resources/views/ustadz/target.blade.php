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
}
.target-siswa-card:hover {
    background-color: #F8FAFC;
    border-color: #E2E8F0;
}
.target-siswa-card.aktif {
    background-color: #E6F4F1;
    border-color: #117A74;
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
            <div class="fw-semibold mb-1">Gagal menyimpan target:</div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    </div>
@endif

{{-- ── Header Banner ────────────────────────────────────────────────── --}}
<div class="mb-4">
    <span class="badge px-3 py-1 text-white mb-2 fw-bold" style="background-color: #117A74;">PENETAPAN TARGET</span>
    <h2 class="fw-bold mb-1" style="color: #1E293B; font-size: 1.65rem;">Target Hafalan Siswa</h2>
    <p class="text-muted small mb-0">Tetapkan sasaran juz atau surah dan rentang tanggal target bagi siswa halaqah binaan Anda.</p>
</div>

<div class="row g-4 align-items-start">

    {{-- ══ PANEL KIRI: DAFTAR PILIH SISWA ══════════════════════════════ --}}
    <div class="col-12 col-lg-4">
        <div class="target-panel p-3 bg-white">
            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-person-check-fill" style="color:#117A74;"></i>
                <span>Pilih Siswa ({{ $santriList->count() }})</span>
            </h6>
            <div class="mb-3 position-relative">
                <i class="bi bi-search position-absolute text-muted" style="top: 13px; left: 14px; font-size: 14px;"></i>
                <input type="text" id="cariSantriTarget" class="form-control ps-5"
                       placeholder="Cari nama atau NIS..."
                       style="height: 42px; border-radius: 10px; background-color: #F8FAFC; border: 1px solid #E2E8F0; font-size: 13.5px;">
            </div>

            <div class="d-flex flex-column gap-1 overflow-y-auto" style="max-height: 560px;" id="listSantriTarget">
                @forelse($santriList as $s)
                    @php
                        $hasTarget = isset($targetAktifMap[$s->id]);
                        $isAktif = $selectedSiswa && $selectedSiswa->id === $s->id;
                    @endphp
                    <a href="{{ route('ustadz.target.index', ['siswa_id' => $s->id]) }}"
                       class="target-siswa-card {{ $isAktif ? 'aktif' : '' }}"
                       data-nama="{{ strtolower($s->nama_lengkap) }}"
                       data-nis="{{ strtolower($s->nip_nis) }}">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width: 42px; height: 42px; background: {{ $isAktif ? '#117A74' : '#E2E8F0' }}; color: {{ $isAktif ? '#fff' : '#64748B' }};">
                            <i class="bi bi-person-fill fs-5"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="fw-bold text-dark text-truncate" style="font-size: 13.5px;">{{ $s->nama_lengkap }}</div>
                            <div class="small text-muted text-truncate" style="font-size: 12px;">NIS: {{ $s->nip_nis }}</div>
                        </div>
                        @if($hasTarget)
                            <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 10.5px;">
                                Aktif
                            </span>
                        @endif
                    </a>
                @empty
                    <div class="text-center py-4 text-muted small">
                        Belum ada siswa dalam halaqah.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ══ PANEL KANAN: TARGET AKTIF & FORM TAMBAH TARGET ══════════════ --}}
    <div class="col-12 col-lg-8">
        <div class="target-panel p-4 bg-white">
            @if(!$selectedSiswa)
                <div class="text-center py-5 my-4">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center p-3 mb-3"
                         style="width: 64px; height: 64px; background: #E6F4F1; color: #117A74;">
                        <i class="bi bi-bullseye fs-2"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Pilih Siswa Terlebih Dahulu</h5>
                    <p class="text-muted small mx-auto" style="max-width: 400px;">
                        Silakan klik salah satu nama siswa di panel sebelah kiri untuk melihat target aktif atau menetapkan target hafalan baru.
                    </p>
                </div>
            @else
                <div>
                    {{-- Header Santri Terpilih --}}
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 pb-3 mb-4"
                         style="border-bottom: 1px solid #E2E8F0;">
                        <div>
                            <span class="badge px-2 py-1 rounded-pill mb-1" style="background:#E6F4F1; color:#117A74; font-size:11px;">
                                Siswa Terpilih
                            </span>
                            <h4 class="fw-bold mb-0 text-dark">{{ $selectedSiswa->nama_lengkap }}</h4>
                            <div class="text-muted small">NIS: {{ $selectedSiswa->nip_nis }} &bull; Halaqah: {{ $halaqah?->nama_halaqah ?? '–' }}</div>
                        </div>
                    </div>

                    {{-- Target Aktif Saat Ini --}}
                    @if($targetAktif)
                        <div class="p-3 mb-4 rounded-3 text-white position-relative shadow-sm"
                             style="background: linear-gradient(135deg, #0D6B65 0%, #117A74 100%);">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                                <span class="badge bg-white text-dark fw-bold px-3 py-1 rounded-pill" style="font-size:11px;">
                                    <i class="bi bi-stars text-warning me-1"></i>Target Sedang Berjalan
                                </span>
                                <form action="{{ route('ustadz.target.selesai', $targetAktif->id) }}" method="POST"
                                      onsubmit="return confirm('Apakah target ini sudah selesai dicapai oleh siswa?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-light text-dark fw-bold rounded-pill px-3 py-1 shadow-sm" style="font-size:11.5px;">
                                        <i class="bi bi-check-circle-fill text-success me-1"></i>Tandai Selesai
                                    </button>
                                </form>
                            </div>
                            <h4 class="fw-bold mb-1">{{ $targetAktif->surah_awal }} s.d. {{ $targetAktif->surah_akhir }}</h4>
                            <div class="small opacity-90 d-flex flex-wrap gap-3 mt-2">
                                <span><i class="bi bi-calendar-event me-1"></i>Mulai: {{ $targetAktif->tanggal_mulai?->translatedFormat('d M Y') }}</span>
                                <span><i class="bi bi-flag me-1"></i>Batas Akhir: {{ $targetAktif->tanggal_akhir?->translatedFormat('d M Y') }}</span>
                                <span>
                                    <i class="bi bi-hourglass-split me-1"></i>
                                    @if($hariSisa >= 0)
                                        Sisa: <strong>{{ $hariSisa }} hari</strong>
                                    @else
                                        <span class="text-warning fw-bold">Terlewat {{ abs($hariSisa) }} hari</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-light border d-flex align-items-center gap-3 p-3 mb-4 rounded-3">
                            <i class="bi bi-info-circle text-muted fs-4"></i>
                            <div class="small text-muted">
                                <strong>Belum ada target aktif.</strong> Gunakan formulir di bawah ini untuk menetapkan target baru bagi santri ini.
                            </div>
                        </div>
                    @endif

                    {{-- Form Penetapan Target Baru --}}
                    <div class="p-3 mb-4 rounded-3" style="background:#F8FAFC; border:1px solid #E2E8F0;">
                        <h6 class="fw-bold text-dark mb-3" style="font-size:14px;">
                            <i class="bi bi-plus-circle me-1" style="color:#117A74;"></i>Tetapkan Target Hafalan Baru
                        </h6>
                        <form action="{{ route('ustadz.target.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="siswa_id" value="{{ $selectedSiswa->id }}">

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark">Surah / Juz Awal <span class="text-danger">*</span></label>
                                    <input type="text" name="surah_awal" class="form-control form-control-sm"
                                           list="listPilihanTarget" placeholder="Contoh: An-Naba', Juz 30 – An-Naba'" required style="border-radius:8px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark">Surah / Juz Akhir <span class="text-danger">*</span></label>
                                    <input type="text" name="surah_akhir" class="form-control form-control-sm"
                                           list="listPilihanTarget" placeholder="Contoh: An-Nas, Juz 30 – An-Nas" required style="border-radius:8px;">
                                </div>
                            </div>

                            <datalist id="listPilihanTarget">
                                @foreach($surahList as $sr)
                                    <option value="{{ $sr['nama'] }} (Ayat 1–{{ $sr['ayat'] }})">
                                @endforeach
                            </datalist>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_mulai" class="form-control form-control-sm"
                                           value="{{ date('Y-m-d') }}" required style="border-radius:8px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark">Batas Akhir Target <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_akhir" class="form-control form-control-sm"
                                           value="{{ date('Y-m-d', strtotime('+30 days')) }}" required style="border-radius:8px;">
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-sm text-white rounded-pill px-4 fw-semibold" style="background:#117A74;">
                                    <i class="bi bi-save me-1"></i>Simpan Target Baru
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Tabel Riwayat Target Sebelumnya --}}
                    <div>
                        <h6 class="fw-bold text-dark mb-2" style="font-size:14px;">
                            <i class="bi bi-clock-history me-1 text-secondary"></i>Riwayat Target Siswa ({{ $riwayatTarget->count() }})
                        </h6>
                        <div class="table-responsive" style="border:1px solid #E2E8F0; border-radius:12px; overflow:hidden;">
                            <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                                <thead style="background:#F8FAFC;">
                                    <tr style="font-size:12px; color:#64748B; text-transform:uppercase; letter-spacing:0.03em;">
                                        <th class="ps-3 py-2.5 fw-semibold" style="width:40px;">No</th>
                                        <th class="py-2.5 fw-semibold">Rentang Target</th>
                                        <th class="py-2.5 fw-semibold" style="width:200px;">Jadwal Target</th>
                                        <th class="py-2.5 pe-3 fw-semibold text-center" style="width:120px;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($riwayatTarget as $idx => $tgt)
                                        <tr>
                                            <td class="ps-3 text-muted small">{{ $idx + 1 }}</td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $tgt->surah_awal }} &rarr; {{ $tgt->surah_akhir }}</div>
                                            </td>
                                            <td>
                                                <div class="small text-secondary">
                                                    {{ $tgt->tanggal_mulai?->translatedFormat('d M Y') }} s.d. {{ $tgt->tanggal_akhir?->translatedFormat('d M Y') }}
                                                </div>
                                            </td>
                                            <td class="pe-3 text-center">
                                                @if($tgt->status?->value === 'Aktif')
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                                                        Aktif
                                                    </span>
                                                @elseif($tgt->status?->value === 'Selesai')
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill">
                                                        Selesai
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 rounded-pill">
                                                        Nonaktif
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted small">
                                                Belum ada riwayat penetapan target untuk siswa ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            @endif
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
// Filter pencarian santri di panel kiri
document.getElementById('cariSantriTarget')?.addEventListener('input', function() {
    const keyword = this.value.toLowerCase().trim();
    const cards = document.querySelectorAll('#listSantriTarget .target-siswa-card');

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
</script>
@endsection
