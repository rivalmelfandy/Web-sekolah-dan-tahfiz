@extends('layouts.public', ['page_title' => 'Akademik', 'current_page' => 'akademik'])

@section('content')

{{-- ═══════════════════════════════════════════════════════════════
     PAGE HERO BANNER
════════════════════════════════════════════════════════════════ --}}
<section class="py-4" style="background: linear-gradient(135deg, #1F4172 0%, #1D4ED8 100%);">
    <div class="container py-3 text-white text-center">
        <span class="badge bg-white bg-opacity-25 text-white fw-semibold mb-2 px-3 py-2 small">
            <i class="bi bi-book-fill me-1"></i> Program Pendidikan
        </span>
        <h1 class="fw-bold mb-1" style="font-size: clamp(1.6rem, 4vw, 2.5rem);">Program Akademik</h1>
        <p class="mb-0 opacity-75">SMA IT Al-Fadhilah Pekanbaru</p>
    </div>
</section>


{{-- ═══════════════════════════════════════════════════════════════
     SECTION 1 — PROGRAM PEMBINAAN UNGGULAN
════════════════════════════════════════════════════════════════ --}}
<section id="program-akademik" class="py-5">
    <div class="container">

        {{-- Heading --}}
        <div class="text-center mb-5">
            <span class="badge rounded-pill fw-semibold px-3 py-2 mb-2"
                  style="background:#DBEAFE; color:#1D4ED8; letter-spacing:0.05em;">
                <i class="bi bi-stars me-1"></i> PROGRAM UNGGULAN
            </span>
            <h2 class="fw-bold" style="color:#1F4172;">Program Pembinaan Unggulan</h2>
            <p class="text-muted mx-auto" style="max-width:580px;">
                Program pembentukan karakter, kepemimpinan, dan kualitas akademik
                siswa terintegrasi.
            </p>
        </div>

        {{-- Grid 6 Card — 3 Kolom × 2 Baris --}}
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach ($programs as $p)
            <div class="col">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden program-card bg-white position-relative">
                    {{-- Badge Kategori --}}
                    <span class="badge bg-dark bg-opacity-75 text-white fw-medium rounded-pill px-3 py-1 small position-absolute top-0 end-0 m-3" style="z-index: 2;">
                        {{ $p['tag'] }}
                    </span>

                    {{-- Area Foto Rencana / Placeholder --}}
                    <img src="{{ $p['img'] }}"
                         class="card-img-top program-img"
                         alt="{{ $p['judul'] }}"
                         style="height: 220px; object-fit: cover;">

                    {{-- Card Body --}}
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-2" style="color:#1F4172;">
                            {{ $p['judul'] }}
                        </h5>
                        <p class="mb-0 small" style="color:#64748B; line-height:1.75;">
                            {{ $p['deskripsi'] }}
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</section>
{{-- /PROGRAM PEMBINAAN UNGGULAN --}}


{{-- ═══════════════════════════════════════════════════════════════
     SECTION 2 — EKSTRAKURIKULER
════════════════════════════════════════════════════════════════ --}}
<section id="ekskul" class="py-5" style="background-color:#F8FAFC;">
    <div class="container-fluid px-4 px-md-5">

        {{-- Heading --}}
        <div class="text-center mb-5">
            <span class="badge rounded-pill fw-semibold px-3 py-2 mb-2"
                  style="background:#DBEAFE; color:#1D4ED8; letter-spacing:0.05em;">
                <i class="bi bi-stars me-1"></i> KEGIATAN SISWA
            </span>
            <h2 class="fw-bold" style="color:#1F4172;">Ekstrakurikuler</h2>
            <p class="text-muted mx-auto" style="max-width:560px;">
                Pengembangan bakat dan potensi siswa di luar jam akademik melalui
                {{ count($ekskul) }} kegiatan terstruktur yang dipandu oleh pelatih berpengalaman.
            </p>
        </div>

        {{-- ── Horizontal Scroll Gallery (Square Cards 1:1) ──── --}}
        <div class="ekskul-scroll-wrapper">

            <div class="d-flex flex-nowrap overflow-x-auto pb-4 gap-3 ekskul-scroll"
                 style="scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch; cursor: grab;">

                @foreach ($ekskul as $e)
                <div class="rounded-4 overflow-hidden position-relative shadow-sm border-0 ekskul-card-sq flex-shrink-0 text-white p-4 d-flex flex-column justify-content-between"
                     style="width:260px; height:240px; flex:0 0 auto; scroll-snap-align:start; background: {{ $e['gradient'] }};">

                    {{-- Top Tag & Icon --}}
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-1 small fw-medium">
                            {{ $e['kategori'] }}
                        </span>
                        <div class="rounded-circle bg-white bg-opacity-20 d-flex align-items-center justify-content-center" style="width:42px; height:42px;">
                            <i class="bi {{ $e['icon'] }} fs-5"></i>
                        </div>
                    </div>

                    {{-- Label Nama Ekskul --}}
                    <div>
                        <div class="text-white-50 small mb-1">Ekstrakurikuler</div>
                        <h5 class="fw-bold text-white mb-0 lh-sm">
                            {{ $e['nama'] }}
                        </h5>
                    </div>

                </div>
                @endforeach

            </div>{{-- /ekskul-scroll --}}

            {{-- Hint geser — hanya tampil di mobile --}}
            <p class="text-center text-muted small mt-2 d-lg-none mb-0">
                <i class="bi bi-arrow-left-right me-1"></i>Geser untuk melihat semua ekskul
            </p>

        </div>{{-- /ekskul-scroll-wrapper --}}

    </div>
</section>
{{-- /EKSTRAKURIKULER --}}


{{-- ═══════════════════════════════════════════════════════════════
     SECTION 3 — PENCAPAIAN
════════════════════════════════════════════════════════════════ --}}
<section id="prestasi" class="py-5">
    <div class="container" id="pencapaian">

        {{-- Heading Section (Rata Kiri, Warna #1E3A8A) --}}
        <div class="mb-4 text-start">
            <h2 class="fw-bold mb-0" style="color: #1E3A8A;">Pencapaian</h2>
        </div>

        {{-- Grid 3 Kolom x 2 Baris — Galeri Foto Poster 4:5 --}}
        <div class="row g-4">
            @for ($i = 1; $i <= $prestasiCount; $i++)
                @php
                    $fileNama = "prestasi-{$i}.jpg";
                    $fotoTersedia = file_exists(public_path("images/prestasi/{$fileNama}"));
                @endphp
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="pencapaian-box rounded-4 overflow-hidden shadow-sm">
                        @if ($fotoTersedia)
                            <img src="{{ asset("images/prestasi/{$fileNama}") }}" alt="Prestasi {{ $i }}" class="w-100 h-100 object-fit-cover">
                        @else
                            {{-- Kotak placeholder abu-abu (#808080) bergaris silang diagonal --}}
                            <div class="pencapaian-placeholder d-flex flex-column align-items-center justify-content-center text-center p-3 text-white"
                                 style="background-color: #808080;">

                                {{-- Garis silang diagonal --}}
                                <svg class="position-absolute top-0 start-0 w-100 h-100" style="pointer-events: none;" xmlns="http://www.w3.org/2000/svg">
                                    <line x1="0" y1="0" x2="100%" y2="100%" stroke="rgba(255, 255, 255, 0.4)" stroke-width="2" />
                                    <line x1="0" y1="100%" x2="100%" y2="0" stroke="rgba(255, 255, 255, 0.4)" stroke-width="2" />
                                </svg>

                                {{-- Konten Placeholder: Ikon Gambar, Nama File Target, Label Ukuran --}}
                                <div class="position-relative z-1 d-flex flex-column align-items-center text-white" style="text-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);">
                                    <i class="bi bi-image display-5 mb-2"></i>
                                    <span class="fw-bold fs-6 mb-1">{{ $fileNama }}</span>
                                    <span class="small opacity-75">1080 × 1350 px (4:5)</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endfor
        </div>

    </div>
</section>
{{-- /PENCAPAIAN --}}

@endsection

@section('styles')
<style>
/* ── Scrollbar tipis ekskul ──────────────────────────── */
.ekskul-scroll {
    scrollbar-width: thin;
    scrollbar-color: #CBD5E1 transparent;
}
.ekskul-scroll::-webkit-scrollbar       { height: 4px; }
.ekskul-scroll::-webkit-scrollbar-track { background: transparent; }
.ekskul-scroll::-webkit-scrollbar-thumb {
    background: #CBD5E1;
    border-radius: 99px;
}
.ekskul-scroll::-webkit-scrollbar-thumb:hover { background: #94A3B8; }

/* Grab cursor */
.ekskul-scroll:active { cursor: grabbing; }

/* Hover — zoom gambar saja */
.ekskul-card-sq:hover .ekskul-sq-img { transform: scale(1.06); }

/* Hover efek card program */
.program-card { transition: transform .3s ease, box-shadow .3s ease; }
.program-card:hover { transform: translateY(-5px); box-shadow: 0 12px 32px rgba(31,65,114,.12) !important; }
.program-card:hover .program-img { transform: scale(1.06); }

/* Galeri Pencapaian Poster 4:5 */
.pencapaian-box {
    aspect-ratio: 4 / 5;
    width: 100%;
    min-height: unset !important;
    height: auto !important;
    position: relative;
    transition: transform .3s ease, box-shadow .3s ease;
}
.pencapaian-box:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(30, 58, 138, 0.15) !important;
}
.pencapaian-placeholder {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
}
.pencapaian-box img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .4s ease;
}
.pencapaian-box:hover img {
    transform: scale(1.05);
}
</style>
@endsection

@section('scripts')
<script>
/* Drag-to-scroll (mouse desktop) */
(function () {
    const el = document.querySelector('.ekskul-scroll');
    if (!el) return;
    let down = false, startX, left;
    el.addEventListener('mousedown',  e => { down = true;  startX = e.pageX - el.offsetLeft; left = el.scrollLeft; });
    el.addEventListener('mouseleave', ()  => { down = false; });
    el.addEventListener('mouseup',    ()  => { down = false; });
    el.addEventListener('mousemove',  e => {
        if (!down) return;
        e.preventDefault();
        el.scrollLeft = left - (e.pageX - el.offsetLeft - startX) * 1.4;
    });
})();
</script>
@endsection
