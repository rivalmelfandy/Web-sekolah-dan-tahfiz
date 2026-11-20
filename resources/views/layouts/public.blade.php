<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Website Resmi SMA IT Al-Fadhilah Pekanbaru – Sekolah Islam Terpadu berbasis Tahfiz Al-Qur'an dan Karakter Pemimpin di Pekanbaru, Riau.">
    <meta name="author" content="SMA IT Al-Fadhilah Pekanbaru">
    <title>{{ isset($page_title) ? $page_title . ' – SMA IT Al-Fadhilah Pekanbaru' : 'SMA IT Al-Fadhilah Pekanbaru' }}</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('images/logo-sekolah.png') }}">

    {{-- Google Fonts: Plus Jakarta Sans & Amiri --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Amiri:wght@400;700&display=swap" rel="stylesheet">

    {{-- Bootstrap 5.3 CSS (CDN) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    {{-- Bootstrap Icons 1.11 (CDN) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Custom CSS Publik Sekolah --}}
    <link rel="stylesheet" href="{{ asset('css/public-style.css') }}">

    @yield('styles')
</head>
<body>

@php
    $navActive = fn (string $page): string => ($current_page ?? '') === $page
        ? 'active fw-bold text-primary active-nav-link'
        : '';
@endphp

{{-- ═══════════════════════════════════════════════════════════════════
     NAVBAR – Sticky Top | Latar Putih | Shadow Halus
═══════════════════════════════════════════════════════════════════ --}}
<nav class="navbar navbar-expand-lg bg-white sticky-top shadow-sm py-2" id="navbarUtama">
    <div class="container">

        {{-- BRAND / LOGO --}}
        <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none" href="{{ route('public.beranda') }}">
            <img src="{{ asset('images/logo-sekolah.png') }}" alt="Logo SMA IT Al-Fadhilah" width="46" height="46" class="rounded-circle object-fit-contain shadow-sm border border-light">
            <div class="d-flex flex-column">
                <span class="fw-extrabold lh-1" style="color: #1F4172; font-size: 1.15rem; letter-spacing: -0.02em;">
                    SMA IT AL-FADHILAH
                </span>
                <small class="fw-semibold text-muted" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                    PEKANBARU · RIAU
                </small>
            </div>
        </a>

        {{-- TOGGLER (Mobile) --}}
        <button
            class="navbar-toggler border-0 p-2"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navMenu"
            aria-controls="navMenu"
            aria-expanded="false"
            aria-label="Toggle navigasi"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- MENU NAVIGASI --}}
        <div class="collapse navbar-collapse justify-content-end" id="navMenu">
            <ul class="navbar-nav align-items-lg-center gap-lg-1 py-2 py-lg-0">

                {{-- Beranda --}}
                <li class="nav-item">
                    <a class="nav-link px-3 {{ $navActive('beranda') }}"
                       href="{{ route('public.beranda') }}"
                       @if(($current_page ?? '') === 'beranda') aria-current="page" @endif>
                        <i class="bi bi-house-door me-1"></i>Beranda
                    </a>
                </li>

                {{-- Akademik --}}
                <li class="nav-item">
                    <a class="nav-link px-3 {{ $navActive('akademik') }}"
                       href="{{ route('public.akademik') }}"
                       @if(($current_page ?? '') === 'akademik') aria-current="page" @endif>
                        <i class="bi bi-mortarboard me-1"></i>Akademik
                    </a>
                </li>

                {{-- SPMB --}}
                <li class="nav-item">
                    <a class="nav-link px-3 {{ $navActive('spmb') }}"
                       href="{{ route('public.spmb') }}"
                       @if(($current_page ?? '') === 'spmb') aria-current="page" @endif>
                        <i class="bi bi-pencil-square me-1"></i>SPMB Online
                    </a>
                </li>

                {{-- Tahfiz (Login Portal Multi-Peran dengan Nuansa Biru Elegan) --}}
                <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                    <a class="btn btn-sm px-4 py-2 fw-bold text-white rounded-pill shadow-sm"
                       style="background: linear-gradient(135deg, #1E40AF 0%, #1D4ED8 100%);"
                       href="{{ url('/login') }}"
                       @if(($current_page ?? '') === 'tahfiz') aria-current="page" @endif>
                        <i class="bi bi-book-half me-1 text-warning"></i>Portal Tahfiz
                    </a>
                </li>

            </ul>
        </div>
        {{-- /navMenu --}}

    </div>
</nav>
{{-- /NAVBAR --}}

{{-- Konten Halaman --}}
<main class="flex-shrink-0">
    @yield('content')
</main>

{{-- ═══════════════════════════════════════════════════════════════════
     FOOTER RESMI – SMA IT AL-FADHILAH PEKANBARU
     Multi-Kolom Institusional Pendidikan Islam
═══════════════════════════════════════════════════════════════════ --}}
<footer class="pt-5 pb-4 mt-auto text-white" style="background: linear-gradient(135deg, #1E3A8A 0%, #1E40AF 45%, #1D4ED8 100%) !important;">
    <div class="container">
        <div class="row g-4 pb-4 border-bottom border-white border-opacity-20">

            {{-- Kolom 1: Profil Sekolah & Akreditasi --}}
            <div class="col-12 col-lg-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="{{ asset('images/logo-sekolah.png') }}" alt="Logo SMA IT Al-Fadhilah" width="56" height="56" class="rounded-circle object-fit-contain bg-white p-1 shadow-sm">
                    <div>
                        <h5 class="fw-bold text-white mb-0 lh-1">SMA IT AL-FADHILAH</h5>
                        <small class="fw-semibold" style="color: #FACC15;">Yayasan Darel Fadhilah Riau</small>
                    </div>
                </div>
                <p class="small mb-3" style="color: rgba(255, 255, 255, 0.85); line-height: 1.7;">
                    Lembaga pendidikan Islam terpadu yang memadukan keunggulan akademik nasional dengan pembinaan intensif tahfiz Al-Qur'an dan karakter kepemimpinan islami.
                </p>
                <div class="d-flex gap-2">
                    <a href="https://www.instagram.com/smait_fadhilah" target="_blank" rel="noopener" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 36px; height: 36px; background: rgba(255, 255, 255, 0.10); border: 1px solid rgba(255, 255, 255, 0.22);" title="Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="https://www.facebook.com/smaitfadhilahpku" target="_blank" rel="noopener" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 36px; height: 36px; background: rgba(255, 255, 255, 0.10); border: 1px solid rgba(255, 255, 255, 0.22);" title="Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="https://www.tiktok.com/@smait_fadhilah" target="_blank" rel="noopener" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 36px; height: 36px; background: rgba(255, 255, 255, 0.10); border: 1px solid rgba(255, 255, 255, 0.22);" title="TikTok">
                        <i class="bi bi-tiktok"></i>
                    </a>
                </div>
            </div>

            {{-- Kolom 2: Tautan Program Pendidikan --}}
            <div class="col-12 col-sm-6 col-lg-2">
                <h6 class="fw-bold text-white text-uppercase mb-3" style="letter-spacing: 0.05em; font-size: 0.85rem;">
                    Program Sekolah
                </h6>
                <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                    <li><a href="{{ route('public.akademik') }}" class="text-decoration-none text-hover-white" style="color: rgba(255, 255, 255, 0.85);">&rsaquo; Tahfiz Al-Qur'an</a></li>
                    <li><a href="{{ route('public.akademik') }}" class="text-decoration-none text-hover-white" style="color: rgba(255, 255, 255, 0.85);">&rsaquo; Program Leadership</a></li>
                    <li><a href="{{ route('public.akademik') }}" class="text-decoration-none text-hover-white" style="color: rgba(255, 255, 255, 0.85);">&rsaquo; Bahasa &amp; Public Speaking</a></li>
                    <li><a href="{{ route('public.akademik') }}" class="text-decoration-none text-hover-white" style="color: rgba(255, 255, 255, 0.85);">&rsaquo; Bimbingan Sukses PTN</a></li>
                    <li><a href="{{ route('public.akademik') }}#ekskul" class="text-decoration-none text-hover-white" style="color: rgba(255, 255, 255, 0.85);">&rsaquo; 13 Ekstrakurikuler</a></li>
                    <li><a href="{{ route('public.spmb') }}" class="text-decoration-none fw-semibold" style="color: #FACC15;">&rsaquo; Pendaftaran SPMB</a></li>
                </ul>
            </div>

            {{-- Kolom 3: Layanan Kontak Resmi --}}
            <div class="col-12 col-sm-6 col-lg-3">
                <h6 class="fw-bold text-white text-uppercase mb-3" style="letter-spacing: 0.05em; font-size: 0.85rem;">
                    Layanan &amp; Kontak
                </h6>
                <div class="d-flex flex-column gap-3 small" style="color: rgba(255, 255, 255, 0.90);">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-geo-alt-fill mt-1 flex-shrink-0" style="color: #FACC15;"></i>
                        <span>Jl. Muhajirin No. RT/RW. 04/09 Kel. Sidomulyo Barat, Tuah Madani, Kota Pekanbaru, Riau 28294</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-telephone-fill flex-shrink-0" style="color: #FACC15;"></i>
                        <span>(0761) 840-7782 (Kantor TU)</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-whatsapp flex-shrink-0" style="color: #86EFAC;"></i>
                        <span>0812-3456-7890 (Layanan SPMB)</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-envelope-fill flex-shrink-0" style="color: #FACC15;"></i>
                        <span>info@alfadhilah.sch.id</span>
                    </div>
                </div>
            </div>

            {{-- Kolom 4: Jam Kunjungan & Akses Portal --}}
            <div class="col-12 col-lg-3">
                <h6 class="fw-bold text-white text-uppercase mb-3" style="letter-spacing: 0.05em; font-size: 0.85rem;">
                    Jam Layanan &amp; Portal
                </h6>
                <div class="p-3 rounded-3 small mb-3" style="background: rgba(255, 255, 255, 0.10); border: 1px solid rgba(255, 255, 255, 0.22);">
                    <div class="d-flex justify-content-between mb-1">
                        <span style="color: rgba(255, 255, 255, 0.85);">Senin – Kamis:</span>
                        <strong class="text-white">07.30 – 16.00 WIB</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span style="color: rgba(255, 255, 255, 0.85);">Jum'at:</span>
                        <strong class="text-white">07.30 – 11.30 WIB</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span style="color: rgba(255, 255, 255, 0.85);">Sabtu:</span>
                        <strong class="text-white">08.00 – 13.00 WIB</strong>
                    </div>
                </div>
                <a href="{{ url('/login') }}" class="btn btn-sm w-100 fw-bold shadow-sm btn-login-portal-white py-2" style="background: #FFFFFF; color: #1E3A8A; font-weight: 700; border: none; border-radius: 10px;">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Login Sistem Terpadu
                </a>
            </div>

        </div>

        {{-- Copyright Bawah --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center pt-3 small" style="border-top: 1px solid rgba(255, 255, 255, 0.16); color: rgba(255, 255, 255, 0.78);">
            <p class="mb-0">
                &copy; {{ date('Y') }} SMA IT Al-Fadhilah Pekanbaru. Hak cipta dilindungi.
            </p>
            <p class="mb-0">
                Kota Pekanbaru, Riau, Indonesia
            </p>
        </div>
    </div>
</footer>
{{-- /FOOTER --}}

{{-- ═══════════════════════════════════════════════════════════════════
     Bootstrap 5.3 JS Bundle (CDN) – termasuk Popper.js
═══════════════════════════════════════════════════════════════════ --}}
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmCKEEWCS5oVsBoMiCjE7I+Vb0Y"
    crossorigin="anonymous">
</script>

@yield('scripts')
</body>
</html>
