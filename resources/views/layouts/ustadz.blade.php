<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Dashboard Ustadz – SMA IT Al-Fadhilah Pekanbaru.">
    <title>@yield('title', 'Ustadz') – SMA IT Al-Fadhilah</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo-tahfiz-baru.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/public-style.css') }}">

    <style>
        /* ── Variabel warna tema hijau sidebar ──────────────────────── */
        :root {
            --sb-bg:        #117A74;
            --sb-bg-dark:   #0D6B65;
            --sb-bg-deeper: #0A5C57;
            --sb-text:      rgba(255,255,255,.88);
            --sb-text-dim:  rgba(255,255,255,.55);
            --sb-active-bg: rgba(255,255,255,.18);
            --sb-hover-bg:  rgba(255,255,255,.10);
            --sb-border:    rgba(255,255,255,.12);
            --sb-width:     240px;
            --topbar-h:     0px;
        }

        /* ── Reset body ke flex baris ───────────────────────────────── */
        body { display:flex; flex-direction:row; min-height:100vh; margin:0; }

        /* ── Sidebar ────────────────────────────────────────────────── */
        #ustadzSidebar {
            width: var(--sb-width);
            min-height: 100vh;
            background: var(--sb-bg);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 1040;
            transition: transform .25s ease;
            overflow-y: auto;
            overflow-x: hidden;
        }
        #ustadzSidebar::-webkit-scrollbar { width: 4px; }
        #ustadzSidebar::-webkit-scrollbar-thumb { background: var(--sb-border); border-radius: 99px; }

        /* ── Brand di atas sidebar ──────────────────────────────────── */
        .sb-brand {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: 1.25rem 1rem 1rem;
            border-bottom: 1px solid var(--sb-border);
            text-decoration: none;
            flex-shrink: 0;
        }
        .sb-brand-text { line-height: 1.1; }
        .sb-brand-title { font-size: .85rem; font-weight: 700; color: #fff; letter-spacing: -.01em; }
        .sb-brand-sub   { font-size: .65rem; font-weight: 500; color: var(--sb-text-dim); letter-spacing: .04em; text-transform: uppercase; }

        /* ── Profil di atas menu ────────────────────────────────────── */
        .sb-profile {
            padding: .9rem 1rem;
            border-bottom: 1px solid var(--sb-border);
            display: flex;
            align-items: center;
            gap: .65rem;
        }
        .sb-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: var(--sb-active-bg);
            border: 1.5px solid var(--sb-border);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            color: #fff; font-size: .9rem;
        }
        .sb-profile-name { font-size: .78rem; font-weight: 600; color: #fff; line-height: 1.2; }
        .sb-profile-role { font-size: .68rem; color: var(--sb-text-dim); }
        a.sb-profile:hover { background: var(--sb-hover-bg); }
        a.sb-profile.sb-profile-active { background: var(--sb-active-bg); }

        /* ── Label grup ─────────────────────────────────────────────── */
        .sb-label {
            font-size: .62rem;
            font-weight: 700;
            color: var(--sb-text-dim);
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: 1rem 1rem .35rem;
        }

        /* ── Item menu ──────────────────────────────────────────────── */
        .sb-nav { list-style: none; padding: 0 .65rem; margin: 0; }
        .sb-nav li + li { margin-top: .15rem; }

        .sb-link {
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: .55rem .75rem;
            border-radius: 8px;
            font-size: .82rem;
            font-weight: 500;
            color: var(--sb-text);
            text-decoration: none;
            transition: background .15s, color .15s;
        }
        .sb-link:hover  { background: var(--sb-hover-bg); color: #fff; }
        .sb-link.active { background: var(--sb-active-bg); color: #fff; font-weight: 700;
                          box-shadow: inset 3px 0 0 rgba(255,255,255,.7); }
        .sb-link i { font-size: .95rem; width: 18px; text-align: center; flex-shrink: 0; }

        /* ── Spacer fleksibel + tombol logout di bawah ──────────────── */
        .sb-spacer { flex: 1; }
        .sb-footer {
            padding: .75rem .65rem 1rem;
            border-top: 1px solid var(--sb-border);
        }
        .sb-logout {
            display: flex; align-items: center; gap: .6rem;
            width: 100%; padding: .55rem .75rem;
            border-radius: 8px; border: 1px solid rgba(252,165,165,.35);
            background: transparent;
            font-size: .82rem; font-weight: 500;
            color: #FCA5A5;
            cursor: pointer;
            transition: background .15s, color .15s, border-color .15s;
        }
        #ustadzSidebar .sb-logout:hover,
        #ustadzSidebar .sb-logout:focus-visible {
            background: #DC2626;
            color: #FFFFFF;
            border-color: #DC2626;
        }
        #ustadzSidebar .sb-logout:active {
            background: #B91C1C;
            color: #FFFFFF;
            border-color: #B91C1C;
        }

        /* ── Topbar tipis (mobile only) ─────────────────────────────── */
        #ustadzTopbar {
            display: none;
            position: fixed; top: 0; left: 0; right: 0;
            height: 52px;
            background: var(--sb-bg);
            z-index: 1050;
            align-items: center;
            padding: 0 1rem;
            gap: .75rem;
            box-shadow: 0 2px 8px rgba(0,0,0,.12);
        }
        .sb-toggle {
            background: none; border: none; padding: .4rem;
            color: #fff; font-size: 1.3rem; cursor: pointer; line-height: 1;
        }

        /* ── Overlay (mobile) ───────────────────────────────────────── */
        #sbOverlayUstadz {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,.35);
            z-index: 1039;
        }

        /* ── Area konten utama ──────────────────────────────────────── */
        #ustadzContent {
            margin-left: var(--sb-width);
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            background: #F8FAFC;
        }

        .ustadz-main {
            flex: 1;
            padding: 24px 28px 48px;
        }

        /* ── Topbar konten (breadcrumb + identitas) ─────────────────── */
        .ustadz-topbar {
            background: #fff;
            border-bottom: 1px solid #E2E8F0;
            padding: .6rem 1.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        /* ── Responsive: layar < 992px ──────────────────────────────── */
        @media (max-width: 991.98px) {
            :root { --topbar-h: 52px; }
            #ustadzTopbar  { display: flex; }
            #ustadzSidebar { transform: translateX(-100%); top: 0; }
            #ustadzSidebar.open { transform: translateX(0); }
            #sbOverlayUstadz.open { display: block; }
            #ustadzContent { margin-left: 0; padding-top: var(--topbar-h); }
            .ustadz-topbar { display: none; }
            .ustadz-main { padding: 1.25rem 1rem 1.5rem; }
        }
    </style>

    @yield('styles')
</head>
<body>

{{-- ── TOPBAR MOBILE ────────────────────────────────────────────────── --}}
<div id="ustadzTopbar">
    <button class="sb-toggle" id="sbToggleUstadz" aria-label="Buka menu">
        <i class="bi bi-list"></i>
    </button>
    <img src="{{ asset('images/logo-tahfiz-baru.png') }}" alt="Logo" width="30" height="30"
         class="rounded-circle bg-white p-1 object-fit-contain">
    <span class="fw-bold text-white" style="font-size:.85rem;">SMA IT Al-Fadhilah</span>
    <span class="ms-auto text-white-50 small d-none d-sm-block">{{ Auth::user()->nama_lengkap }}</span>
</div>

{{-- ── OVERLAY (MOBILE) ────────────────────────────────────────────── --}}
<div id="sbOverlayUstadz"></div>

{{-- ═══════════════════════════════════════════════════════════════════
     SIDEBAR KIRI
═══════════════════════════════════════════════════════════════════ --}}
<nav id="ustadzSidebar" aria-label="Navigasi ustadz">

    {{-- Brand --}}
    <a class="sb-brand" href="{{ route('ustadz.dashboard') }}">
        <img src="{{ asset('images/logo-tahfiz-baru.png') }}" alt="Logo" width="38" height="38"
             class="rounded-circle bg-white bg-opacity-10 p-1 object-fit-contain flex-shrink-0">
        <div class="sb-brand-text">
            <div class="sb-brand-title">SMA IT Al-Fadhilah</div>
            <div class="sb-brand-sub">Ustadz Panel</div>
        </div>
    </a>

    {{-- ── Profil ──────────────────────────────────────────────────── --}}
    <a href="{{ route('ustadz.profil') }}"
       class="sb-profile text-decoration-none {{ request()->routeIs('ustadz.profil') ? 'sb-profile-active' : '' }}"
       style="display:flex;">
        <div class="sb-avatar"><i class="bi bi-person-fill"></i></div>
        <div class="min-w-0">
            <div class="sb-profile-name text-truncate" style="max-width:160px;">{{ Auth::user()->nama_lengkap }}</div>
            <div class="sb-profile-role">Ustadz/Ustadzah</div>
        </div>
    </a>

    {{-- ── Menu Utama (kosong, siap diisi menu khusus ustadz) ─────── --}}
    <div class="sb-label">Menu Utama</div>
    <ul class="sb-nav">
        {{-- Menu ustadz akan ditambahkan di sini --}}
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
<div id="ustadzContent">

    {{-- Topbar tipis (desktop) --}}
    <div class="ustadz-topbar">
        <div class="d-flex align-items-center gap-2 text-muted small">
            <i class="bi bi-house-door"></i>
            <span>Ruang Ustadz</span>
            <i class="bi bi-chevron-right" style="font-size:.6rem;"></i>
            <span class="fw-semibold text-dark">@yield('breadcrumb', 'Dashboard')</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small"><i class="bi bi-calendar3 me-1"></i>{{ now()->translatedFormat('l, d F Y') }}</span>
            <span class="badge fw-semibold rounded-pill px-3 py-1"
                  style="background:#E8F5F4; color:#117A74; font-size:.75rem;">
                <i class="bi bi-person-check me-1"></i>Ustadz/Ustadzah
            </span>
        </div>
    </div>

    {{-- Konten halaman --}}
    <main class="ustadz-main">
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
    const sidebar  = document.getElementById('ustadzSidebar');
    const overlay  = document.getElementById('sbOverlayUstadz');
    const toggle   = document.getElementById('sbToggleUstadz');

    function open()  { sidebar.classList.add('open');  overlay.classList.add('open'); }
    function close() { sidebar.classList.remove('open'); overlay.classList.remove('open'); }

    if (toggle)  toggle.addEventListener('click', open);
    if (overlay) overlay.addEventListener('click', close);

    sidebar.querySelectorAll('.sb-link').forEach(a => a.addEventListener('click', close));
})();
</script>

@yield('scripts')
</body>
</html>
