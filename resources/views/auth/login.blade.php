<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login Sistem Terpadu SMA IT Al-Fadhilah Pekanbaru.">
    <title>Login – SMA IT Al-Fadhilah Pekanbaru</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo-sekolah.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/public-style.css') }}">
</head>
<body class="d-flex flex-column" style="min-height: 100vh; background: linear-gradient(135deg, #0D6B65 0%, #117A74 45%, #138C85 100%);">
<style>
    .btn-teal {
        background-color: #117A74;
        border-color: #117A74;
        color: #ffffff;
        transition: background-color .2s ease, border-color .2s ease, box-shadow .2s ease;
    }
    .btn-teal:hover, .btn-teal:focus {
        background-color: #0D6B65;
        border-color: #0D6B65;
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(17, 122, 116, 0.35);
    }
    .btn-teal:active {
        background-color: #0A5C57;
        border-color: #0A5C57;
        color: #ffffff;
    }
    .link-teal { color: #117A74 !important; }
    .link-teal:hover { color: #0D6B65 !important; }
</style>

<div class="container d-flex align-items-center justify-content-center flex-grow-1 py-5">
    <div class="w-100" style="max-width: 420px;">

        {{-- Logo & Judul --}}
        <div class="text-center mb-4">
            <img src="{{ asset('images/logo-tahfiz-baru.png') }}" alt="Logo Tahfiz SMA IT Al-Fadhilah" width="72" height="72" class="rounded-circle bg-white p-2 shadow-sm mb-3 d-block mx-auto object-fit-contain">
            <h1 class="fw-bold text-white mb-1" style="font-size: 1.4rem;">SMA IT Al-Fadhilah</h1>
            <p class="text-white-50 mb-0 small">Sistem Informasi Sekolah &amp; Monitoring Tahfiz</p>
        </div>

        {{-- Kartu Login --}}
        <div class="card border-0 shadow-lg rounded-4">
            <div class="card-body p-4 p-md-5">

                <h2 class="fw-bold mb-1" style="color:#117A74;">Login</h2>
                <p class="text-muted small mb-4">Masuk menggunakan NIP/NIS dan password Anda.</p>

                {{-- Pesan error global --}}
                @if ($errors->any())
                    <div class="alert alert-danger d-flex align-items-center gap-2 py-2" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span class="small">{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.attempt') }}">
                    @csrf

                    {{-- NIP/NIS --}}
                    <div class="mb-3">
                        <label for="nip_nis" class="form-label fw-semibold small">NIP / NIS</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-person-fill"></i></span>
                            <input type="text" name="nip_nis" id="nip_nis"
                                   class="form-control border-start-0 {{ $errors->has('nip_nis') ? 'is-invalid' : '' }}"
                                   value="{{ old('nip_nis') }}"
                                   placeholder="Masukkan NIP / NIS"
                                   autocomplete="username" autofocus required>
                        </div>
                        @error('nip_nis')
                            <div class="invalid-feedback d-block small">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="password" class="form-label fw-semibold small mb-0">Password</label>
                            <button type="button"
                                    class="btn btn-link btn-sm p-0 link-teal text-decoration-none"
                                    style="font-size: 0.78rem;"
                                    onclick="alert('Silakan hubungi Administrator Sekolah atau Ustadz Pembimbing untuk bantuan reset password.')">
                                Lupa password?
                            </button>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="password" id="password"
                                   class="form-control border-start-0 {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                   placeholder="Masukkan password"
                                   autocomplete="current-password" required>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block small">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Remember --}}
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
                        <label class="form-check-label small text-muted" for="remember">Ingat saya</label>
                    </div>

                    {{-- Tombol Login --}}
                    <button type="submit" class="btn btn-teal w-100 py-2 fw-bold">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Masuk
                    </button>
                </form>

                <hr class="my-4">

                <a href="{{ route('public.beranda') }}" class="text-decoration-none small d-flex align-items-center justify-content-center gap-1 link-teal">
                    <i class="bi bi-arrow-left"></i>Kembali ke Beranda
                </a>
            </div>
        </div>

        <p class="text-center text-white-50 small mt-4 mb-0">&copy; {{ date('Y') }} SMA IT Al-Fadhilah Pekanbaru</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmCKEEWCS5oVsBoMiCjE7I+Vb0Y" crossorigin="anonymous"></script>
</body>
</html>
