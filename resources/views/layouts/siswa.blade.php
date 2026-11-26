<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Dashboard Siswa – SMA IT Al-Fadhilah Pekanbaru.">
    <title>@yield('title', 'Siswa') – SMA IT Al-Fadhilah</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo-tahfiz-baru.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/public-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/siswa.css') }}">

    @yield('styles')
</head>
<body>

{{-- ── TOPBAR MOBILE ────────────────────────────────────────────────── --}}
<div id="siswaTopbar">
    <button class="sb-toggle" id="sbToggleSiswa" aria-label="Buka menu">
        <i class="bi bi-list"></i>
    </button>
    <img src="{{ asset('images/logo-tahfiz-baru.png') }}" alt="Logo" width="30" height="30"
         class="rounded-circle bg-white p-1 object-fit-contain">
    <span class="fw-bold text-white" style="font-size:.85rem;">SMA IT Al-Fadhilah</span>
    <span class="ms-auto text-white-50 small d-none d-sm-block">{{ Auth::user()->nama_lengkap }}</span>
</div>

{{-- ── OVERLAY (MOBILE) ────────────────────────────────────────────── --}}
<div id="sbOverlaySiswa"></div>

{{-- ═══════════════════════════════════════════════════════════════════
     SIDEBAR KIRI
═══════════════════════════════════════════════════════════════════ --}}
<nav id="siswaSidebar" aria-label="Navigasi siswa">

    {{-- Brand --}}
    <a class="sb-brand" href="{{ route('siswa.dashboard') }}">
        <img src="{{ asset('images/logo-tahfiz-baru.png') }}" alt="Logo" width="38" height="38"
             class="rounded-circle bg-white bg-opacity-10 p-1 object-fit-contain flex-shrink-0">
        <div class="sb-brand-text">
            <div class="sb-brand-title">TahfizKu</div>
            <div class="sb-brand-sub">Siswa Panel</div>
        </div>
    </a>

    {{-- ── Profil ──────────────────────────────────────────────────── --}}
    <a href="{{ route('siswa.profil') }}"
       class="sb-profile text-decoration-none {{ request()->routeIs('siswa.profil*') ? 'sb-profile-active' : '' }}"
       style="display:flex;">
        <div class="sb-avatar"><i class="bi bi-person-fill"></i></div>
        <div class="min-w-0">
            <div class="sb-profile-name text-truncate" style="max-width:160px;">{{ Auth::user()->nama_lengkap }}</div>
            <div class="sb-profile-role">{{ Auth::user()->role->label() }}</div>
        </div>
    </a>

    {{-- ── Menu Utama ──────────────────────────────────────────────── --}}
    <div class="sb-label">Menu Utama</div>
    <ul class="sb-nav">
        <li>
            <a href="{{ route('siswa.dashboard') }}"
               class="sb-link {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                Progres Hafalan
            </a>
        </li>
        <li>
            <a href="{{ route('siswa.riwayat') }}"
               class="sb-link {{ request()->routeIs('siswa.riwayat*') ? 'active' : '' }}">
                <i class="bi bi-journal-bookmark"></i>
                Riwayat Hafalan
            </a>
        </li>
    </ul>

    <div class="sb-spacer"></div>

    {{-- ── Tombol Keluar ───────────────────────────────────────────── --}}
    <div class="sb-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sb-logout">
                <i class="bi bi-box-arrow-left"></i>
                Keluar Sesi
            </button>
        </form>
    </div>

</nav>
{{-- /SIDEBAR --}}

{{-- ═══════════════════════════════════════════════════════════════════
     AREA KONTEN KANAN
═══════════════════════════════════════════════════════════════════ --}}
<div id="siswaContent">

    {{-- Topbar tipis (desktop) --}}
    <div class="siswa-topbar">
        <div class="d-flex align-items-center gap-2 text-muted small">
            <i class="bi bi-house-door"></i>
            <span>Ruang Siswa</span>
            <i class="bi bi-chevron-right" style="font-size:.6rem;"></i>
            <span class="fw-semibold text-dark">@yield('breadcrumb', 'Progres Hafalan')</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small"><i class="bi bi-calendar3 me-1"></i>{{ now()->translatedFormat('l, d F Y') }}</span>
            <span class="badge fw-semibold rounded-pill px-3 py-1"
                  style="background:#E8F5F4; color:#117A74; font-size:.75rem;">
                <i class="bi bi-mortarboard me-1"></i>{{ Auth::user()->nama_lengkap }}
            </span>
        </div>
    </div>

    {{-- Konten halaman --}}
    <main class="siswa-main">
        @yield('content')
    </main>

</div>
{{-- /KONTEN --}}

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmCKEEWCS5oVsBoMiCjE7I+Vb0Y"
        crossorigin="anonymous"></script>

<script>
/* ── Toggle sidebar mobile ──────────────────────────────────────── */
(function () {
    const sidebar  = document.getElementById('siswaSidebar');
    const overlay  = document.getElementById('sbOverlaySiswa');
    const toggle   = document.getElementById('sbToggleSiswa');

    if (!sidebar || !overlay) return;

    function open()  { sidebar.classList.add('open');  overlay.classList.add('open'); }
    function close() { sidebar.classList.remove('open'); overlay.classList.remove('open'); }

    if (toggle)  toggle.addEventListener('click', open);
    if (overlay) overlay.addEventListener('click', close);

    // Tutup otomatis saat link diklik (mobile)
    sidebar.querySelectorAll('.sb-link').forEach(function (a) {
        a.addEventListener('click', close);
    });
})();
</script>

@yield('scripts')
</body>
</html>
