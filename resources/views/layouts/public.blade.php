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
     FOOTER MINIMALIS RATA TENGAH
═══════════════════════════════════════════════════════════════════ --}}
<footer style="background:#E6EAF0; padding:48px 20px; text-align:center;">

    {{-- Baris 1: Logo + Nama Sekolah --}}
    <div style="display:inline-flex; align-items:center; gap:14px; margin-bottom:18px;">
        <img src="{{ asset('images/logo-sekolah.png') }}" alt="Logo SMA IT Al-Fadhilah"
             style="width:46px; height:46px; object-fit:contain; border-radius:50%; filter:brightness(0) invert(1);">
        <div style="text-align:left; font-weight:700; font-size:15px; line-height:1.35; color:#FFFFFF; letter-spacing:0.3px;">
            SMA IT AL-FADHILAH<br>PEKANBARU
        </div>
    </div>

    {{-- Baris 2: Alamat --}}
    <p style="max-width:360px; margin:0 auto; font-size:13.5px; line-height:1.6; color:#FFFFFF;">
        Jl. Muhajirin No.RT/RW. 04/09 Kel, Sidomulyo Bar., Tuah Madani, Kota Pekanbaru, Riau 28294
    </p>

    {{-- Baris 3: Garis Pemisah --}}
    <div style="width:260px; max-width:80%; height:1px; background:rgba(255,255,255,.6); margin:22px auto;"></div>

    {{-- Baris 4: Ikon Sosial Media --}}
    <div style="display:flex; align-items:center; justify-content:center; gap:22px;">

        {{-- Instagram --}}
        <a href="https://www.instagram.com/smait_fadhilah" target="_blank" rel="noopener"
           title="Instagram" style="color:#FFFFFF; display:inline-flex; transition:opacity .15s;"
           onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                <circle cx="12" cy="12" r="4.5"/>
                <circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/>
            </svg>
        </a>

        {{-- Facebook --}}
        <a href="https://www.facebook.com/smaitfadhilahpku" target="_blank" rel="noopener"
           title="Facebook" style="color:#FFFFFF; display:inline-flex; transition:opacity .15s;"
           onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
            </svg>
        </a>

        {{-- TikTok --}}
        <a href="https://www.tiktok.com/@smait_fadhilah" target="_blank" rel="noopener"
           title="TikTok" style="color:#FFFFFF; display:inline-flex; transition:opacity .15s;"
           onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.14 8.14 0 0 0 4.77 1.52V6.76a4.85 4.85 0 0 1-1-.07z"/>
            </svg>
        </a>

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
