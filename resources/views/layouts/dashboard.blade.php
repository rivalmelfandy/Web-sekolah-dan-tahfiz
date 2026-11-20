<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Dashboard Sistem Informasi Sekolah & Monitoring Tahfiz SMA IT Al-Fadhilah Pekanbaru.">
    <title>@yield('title', 'Dashboard') – SMA IT Al-Fadhilah Pekanbaru</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo-sekolah.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/public-style.css') }}">

    @yield('styles')
</head>
<body class="d-flex flex-column" style="min-height: 100vh;">

<nav class="navbar navbar-expand-lg sticky-top shadow-sm py-2" style="background: linear-gradient(135deg, #1E3A8A 0%, #1E40AF 45%, #1D4ED8 100%);">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none" href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : (Auth::user()->isUstadz() ? route('ustadz.dashboard') : route('siswa.dashboard')) }}">
            <img src="{{ asset('images/logo-sekolah.png') }}" alt="Logo" width="40" height="40" class="rounded-circle bg-white p-1">
            <div class="d-flex flex-column lh-1">
                <span class="fw-bold text-white" style="font-size: 1.05rem;">SMA IT Al-Fadhilah</span>
                <small class="text-white-50" style="font-size: 0.7rem; letter-spacing: 0.05em;">DASHBOARD TERPADU</small>
            </div>
        </a>

        @if(Auth::user()->isAdmin())
        <button class="navbar-toggler border-0 p-1" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav" aria-controls="adminNav" aria-expanded="false" aria-label="Toggle navigasi">
            <i class="bi bi-list fs-4 text-white"></i>
        </button>
        <div class="collapse navbar-collapse" id="adminNav">
            <ul class="navbar-nav me-auto ms-3 gap-lg-1 py-2 py-lg-0">
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold {{ request()->routeIs('admin.dashboard') ? 'opacity-100' : 'opacity-75' }} px-3 py-1 rounded-2 {{ request()->routeIs('admin.dashboard') ? 'bg-white bg-opacity-25' : '' }}"
                       href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold {{ request()->routeIs('admin.users.*') ? 'opacity-100' : 'opacity-75' }} px-3 py-1 rounded-2 {{ request()->routeIs('admin.users.*') ? 'bg-white bg-opacity-25' : '' }}"
                       href="{{ route('admin.users.index') }}">
                        <i class="bi bi-people me-1"></i>Pengguna
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold {{ request()->routeIs('admin.halaqah.*') ? 'opacity-100' : 'opacity-75' }} px-3 py-1 rounded-2 {{ request()->routeIs('admin.halaqah.*') ? 'bg-white bg-opacity-25' : '' }}"
                       href="{{ route('admin.halaqah.index') }}">
                        <i class="bi bi-collection me-1"></i>Halaqah
                    </a>
                </li>
            </ul>
        </div>
        @endif

        <div class="d-flex align-items-center gap-3 ms-auto">
            <div class="d-none d-md-flex flex-column text-end lh-1">
                <span class="fw-semibold text-white small">{{ Auth::user()->nama_lengkap }}</span>
                <span class="text-warning small">{{ Auth::user()->role->label() }}</span>
            </div>
            <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center text-white fw-bold" style="width: 42px; height: 42px;">
                <i class="bi bi-person-fill fs-5"></i>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-light fw-semibold" title="Keluar">
                    <i class="bi bi-box-arrow-right"></i><span class="d-none d-md-inline ms-1">Keluar</span>
                </button>
            </form>
        </div>
    </div>
</nav>

<main class="flex-grow-1 py-4" style="background-color: var(--color-canvas, #F8FAFC);">
    <div class="container">
        @yield('content')
    </div>
</main>

<footer class="py-3 text-center small text-muted border-top bg-white">
    &copy; {{ date('Y') }} SMA IT Al-Fadhilah Pekanbaru
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmCKEEWCS5oVsBoMiCjE7I+Vb0Y" crossorigin="anonymous"></script>
@yield('scripts')
</body>
</html>
