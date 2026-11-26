@extends('layouts.siswa', ['title' => 'Profil & Kata Sandi'])

@section('breadcrumb', 'Profil & Kata Sandi')

@section('styles')
    <style>
        .profil-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        @media (max-width: 991.98px) {
            .profil-grid {
                grid-template-columns: 1fr;
            }
        }

        .profil-card {
            background: #FFFFFF;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #E2E8F0;
        }

        .profil-card .form-label {
            font-size: .82rem;
            font-weight: 600;
            color: #000;
            margin-bottom: .3rem;
        }

        .profil-card .form-control:focus {
            border-color: #0D6B65;
            box-shadow: 0 0 0 3px rgba(13, 107, 101, .12);
        }

        .form-control-locked {
            background: #F8FAFC !important;
            cursor: not-allowed !important;
            color: #64748B;
            border-color: #E2E8F0;
        }

        .btn-simpan {
            background: #0D6B65;
            border: none;
            color: #fff;
            border-radius: 8px;
            padding: .5rem 1.1rem;
            font-size: .84rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            transition: background .15s;
        }

        .btn-simpan:hover {
            background: #09524D;
            color: #fff;
        }

        .btn-batal {
            background: #F1F5F9;
            border: none;
            color: #000;
            border-radius: 8px;
            padding: .5rem 1.1rem;
            font-size: .84rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s;
        }

        .btn-batal:hover {
            background: #E2E8F0;
        }
    </style>
@endsection

@section('content')

    {{-- ── KARTU IDENTITAS SISWA ───────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4"
        style="background:linear-gradient(135deg,#0D6B65,#117A74,#138C85); overflow:hidden;">
        <div class="card-body p-4 p-md-5">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-4">

                {{-- Identitas kiri --}}
                <div class="d-flex align-items-center gap-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width:72px;height:72px;background:rgba(255,255,255,.18);border:2px solid rgba(255,255,255,.35);">
                        <i class="bi bi-person-fill text-white" style="font-size:2rem;"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold text-white mb-1 h4">{{ $user->nama_lengkap }}</h2>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            <span class="badge px-3 py-1 rounded-pill fw-semibold"
                                style="background:rgba(255,255,255,.18); color:#fff; font-size:.75rem;">
                                <i class="bi bi-credit-card me-1"></i>NIS: {{ $user->nip_nis }}</span>
                            @if($namaHalaqah && $namaHalaqah !== 'Belum tergabung dalam halaqah')
                                <span class="badge px-3 py-1 rounded-pill fw-semibold"
                                    style="background:rgba(255,255,255,.18); color:#fff; font-size:.75rem;">
                                    <i class="bi bi-people me-1"></i>{{ $namaHalaqah }}
                                </span>
                            @endif
                            @if($namaUstadz)
                                <span class="badge px-3 py-1 rounded-pill fw-semibold"
                                    style="background:rgba(255,255,255,.18); color:#fff; font-size:.75rem;">
                                    <i class="bi bi-person-check me-1"></i>{{ $namaUstadz }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Statistik kanan (hanya tampil jika ada data) --}}
                @if($totalSetoran > 0 || $totalMurajaah > 0)
                    <div class="d-flex flex-wrap gap-3">
                        <div class="text-center px-3 py-2 rounded-3" style="background:rgba(255,255,255,.12); min-width:80px;">
                            <div class="fw-bold text-white fs-5 lh-1">{{ $totalSetoran }}</div>
                            <div class="text-white-50 mt-1" style="font-size:.7rem;">Setoran</div>
                        </div>
                        <div class="text-center px-3 py-2 rounded-3" style="background:rgba(255,255,255,.12); min-width:80px;">
                            <div class="fw-bold text-white fs-5 lh-1">{{ $totalMurajaah }}</div>
                            <div class="text-white-50 mt-1" style="font-size:.7rem;">Murajaah</div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- ── NOTIFIKASI ──────────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible d-flex align-items-center gap-2 py-2 mb-4" role="alert">
            <i class="bi bi-check-circle-fill flex-shrink-0"></i>
            <span class="small">{{ session('success') }}</span>
            <button type="button" class="btn-close btn-sm ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('success_password'))
        <div class="alert alert-success alert-dismissible d-flex align-items-center gap-2 py-2 mb-4" role="alert">
            <i class="bi bi-shield-check flex-shrink-0"></i>
            <span class="small">{{ session('success_password') }}</span>
            <button type="button" class="btn-close btn-sm ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any() && !$errors->has('current_password'))
        <div class="alert alert-danger d-flex align-items-start gap-2 py-2 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-1"></i>
            <div class="small">
                <div class="fw-semibold mb-1">Mohon perbaiki kesalahan berikut:</div>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- ── GRID 2 KARTU ────────────────────────────────────────────────── --}}
    <div class="profil-grid">

        {{-- ── Kartu Kiri: Informasi Akun ────────────────────────────── --}}
        <div class="profil-card">
            <div class="d-flex align-items-center gap-2 mb-4">
                <div class="rounded-3 d-flex align-items-center justify-content-center"
                    style="width:40px;height:40px;background:linear-gradient(135deg,#10B981,#0D9488);">
                    <i class="bi bi-person-fill text-white" style="font-size:1rem;"></i>
                </div>
                <h3 class="fw-bold mb-0 h5 text-dark">Informasi Akun</h3>
            </div>

            <form method="POST" action="{{ route('siswa.profil.update') }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control form-control-locked" value="{{ $user->nama_lengkap }}" disabled
                        readonly>
                </div>

                <div class="mb-3">
                    <label for="no_hp" class="form-label">Nomor HP</label>
                    <input type="text" id="no_hp" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror"
                        value="{{ old('no_hp', $user->no_hp) }}" placeholder="Opsional" maxlength="20">
                    @error('no_hp')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">NIS / NIP</label>
                    <input type="text" class="form-control form-control-locked" value="{{ $user->nip_nis }}" disabled
                        readonly>
                </div>

                <div class="mb-4">
                    <label class="form-label">Halaqah</label>
                    <input type="text" class="form-control form-control-locked" value="{{ $namaHalaqah }}" disabled
                        readonly>
                    <div class="form-text small text-muted mt-1">
                        Nama, NIS/NIP, dan penempatan halaqah dikelola oleh Admin.
                    </div>
                </div>

                @if($namaUstadz)
                    <div class="mb-4">
                        <label class="form-label">Ustadz Pembimbing</label>
                        <input type="text" class="form-control form-control-locked" value="{{ $namaUstadz }}" disabled readonly>
                    </div>
                @endif

                <div class="d-flex gap-2">
                    <button type="submit" class="btn-simpan">
                        <i class="bi bi-check-lg"></i>Simpan Perubahan
                    </button>
                    <button type="reset" class="btn-batal">Batal</button>
                </div>
            </form>
        </div>

        {{-- ── Kartu Kanan: Ganti Password ────────────────────────── --}}
        <div class="profil-card">
            <div class="d-flex align-items-center gap-2 mb-4">
                <div class="rounded-3 d-flex align-items-center justify-content-center"
                    style="width:40px;height:40px;background:linear-gradient(135deg,#F59E0B,#D97706);">
                    <i class="bi bi-key-fill text-white" style="font-size:1rem;"></i>
                </div>
                <h3 class="fw-bold mb-0 h5 text-dark">Ganti Password</h3>
            </div>

            @error('current_password')
                <div class="alert alert-danger d-flex align-items-center gap-2 py-2 mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                    <span class="small">{{ $message }}</span>
                </div>
            @enderror

            <form method="POST" action="{{ route('siswa.profil.password') }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="current_password" class="form-label">
                        Kata Sandi Saat Ini <span class="text-danger">*</span>
                    </label>
                    <div style="position:relative;">
                        <input type="password" id="current_password" name="current_password"
                            class="form-control @error('current_password') is-invalid @enderror"
                            placeholder="Masukkan kata sandi lama" style="padding-right:42px;"
                            autocomplete="current-password" required>
                        <button type="button" onclick="togglePwd(this)" aria-label="Lihat kata sandi"
                            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;padding:0;cursor:pointer;color:#64748B;">
                            <i class="bi bi-eye icon-eye"></i>
                            <i class="bi bi-eye-slash icon-eye-off" style="display:none;"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">
                        Kata Sandi Baru <span class="text-danger">*</span>
                    </label>
                    <div style="position:relative;">
                        <input type="password" id="password" name="password"
                            class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter"
                            style="padding-right:42px;" autocomplete="new-password" required>
                        <button type="button" onclick="togglePwd(this)" aria-label="Lihat kata sandi"
                            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;padding:0;cursor:pointer;color:#64748B;">
                            <i class="bi bi-eye icon-eye"></i>
                            <i class="bi bi-eye-slash icon-eye-off" style="display:none;"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">
                        Konfirmasi Kata Sandi Baru <span class="text-danger">*</span>
                    </label>
                    <div style="position:relative;">
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                            placeholder="Ulangi kata sandi baru" style="padding-right:42px;" autocomplete="new-password"
                            required>
                        <button type="button" onclick="togglePwd(this)" aria-label="Lihat kata sandi"
                            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;padding:0;cursor:pointer;color:#64748B;">
                            <i class="bi bi-eye icon-eye"></i>
                            <i class="bi bi-eye-slash icon-eye-off" style="display:none;"></i>
                        </button>
                    </div>
                </div>

                {{-- Indikator kekuatan password --}}
                <div id="pwdWarn" class="d-none align-items-center gap-2 rounded-3 p-2 mb-3"
                    style="background:#FFFBEB; border:1px solid #FDE68A; font-size:.78rem; color:#B45309;">
                    <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                    Gunakan kombinasi huruf, angka, dan simbol untuk kata sandi yang lebih kuat.
                </div>

                <button type="submit" class="btn-simpan w-100 justify-content-center">
                    <i class="bi bi-lock-fill"></i>Perbarui Kata Sandi
                </button>
            </form>
        </div>

    </div>

@endsection

@section('scripts')
    <script>
        function togglePwd(btn) {
            var input = btn.parentElement.querySelector('input');
            var eyeOn = btn.querySelector('.icon-eye');
            var eyeOff = btn.querySelector('.icon-eye-off');
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            eyeOn.style.display = show ? 'none' : '';
            eyeOff.style.display = show ? '' : 'none';
        }
        (function () {
            var pwd = document.getElementById('password');
            var warn = document.getElementById('pwdWarn');
            if (!pwd || !warn) return;
            pwd.addEventListener('input', function () {
                var v = pwd.value;
                var weak = v.length > 0 && !(/[a-zA-Z]/.test(v) && /[0-9]/.test(v));
                warn.classList.toggle('d-none', !weak);
                warn.classList.toggle('d-flex', weak);
            });
        })();
    </script>
@endsection