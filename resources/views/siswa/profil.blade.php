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
        .profil-grid { grid-template-columns: 1fr; }
    }
    .profil-card {
        background: #FFFFFF;
        border-radius: 16px;
        padding: 24px;
        border: 1px solid #E2E8F0;
    }
    .profil-card .form-label { font-size: .82rem; font-weight: 600; color: #000000; margin-bottom: .3rem; }
    .profil-card .form-control:focus { border-color: #0D6B65; box-shadow: 0 0 0 3px rgba(13,107,101,.12); }
    .form-control-locked {
        background: #F8FAFC !important;
        cursor: not-allowed !important;
        color: #64748B;
        border-color: #E2E8F0;
    }
    .btn-simpan {
        background: #0D6B65; border: none; color: #fff;
        border-radius: 8px; padding: .5rem 1.1rem;
        font-size: .84rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: .4rem;
        transition: background .15s;
    }
    .btn-simpan:hover { background: #09524D; color: #fff; }
    .btn-batal {
        background: #F1F5F9; border: none; color: #000000;
        border-radius: 8px; padding: .5rem 1.1rem;
        font-size: .84rem; font-weight: 600;
        cursor: pointer; transition: background .15s;
    }
    .btn-batal:hover { background: #E2E8F0; }
    .info-box {
        background: #F0FDF4; border: 1px solid #A7F3D0;
        border-radius: 8px; padding: .65rem .9rem;
        font-size: .78rem; color: #065F46; line-height: 1.55;
    }
</style>
@endsection

@section('content')

{{-- ── Header ──────────────────────────────────────────────────────── --}}
<div class="mb-4">
    <h2 class="fw-bold mb-1 h4" style="color:#000000;">
        <i class="bi bi-person-circle me-2" style="color:#000000;"></i>Profil Pribadi
    </h2>
    <p class="text-muted mb-0 small">Kelola informasi biodata siswa dan pembaruan kata sandi akun.</p>
</div>

{{-- ── Notifikasi sukses informasi akun ───────────────────────────── --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible d-flex align-items-center gap-2 py-2 mb-4" role="alert">
        <i class="bi bi-check-circle-fill flex-shrink-0"></i>
        <span class="small">{{ session('success') }}</span>
        <button type="button" class="btn-close btn-sm ms-auto" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ── Notifikasi sukses ganti password ───────────────────────────── --}}
@if(session('success_password'))
    <div class="alert alert-success alert-dismissible d-flex align-items-center gap-2 py-2 mb-4" role="alert">
        <i class="bi bi-shield-check flex-shrink-0"></i>
        <span class="small">{{ session('success_password') }}</span>
        <button type="button" class="btn-close btn-sm ms-auto" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ── Error validasi --}}
@if($errors->any() && !$errors->has('current_password'))
    <div class="alert alert-danger d-flex align-items-start gap-2 py-2 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-1"></i>
        <div class="small">
            <div class="fw-semibold mb-1">Mohon perbaiki kesalahan berikut:</div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    </div>
@endif

{{-- ── Grid 2 Kartu ────────────────────────────────────────────────── --}}
<div class="profil-grid">

    {{-- ── Kartu Kiri: Informasi Akun ─────────────────────────── --}}
    <div class="profil-card">
        <div class="d-flex align-items-center gap-2 mb-4">
            <div class="rounded-3 d-flex align-items-center justify-content-center"
                 style="width:40px; height:40px; background:linear-gradient(135deg,#10B981,#0D9488);">
                <i class="bi bi-person-fill" style="color:#fff; font-size:1rem;"></i>
            </div>
            <h3 class="fw-bold mb-0 h5" style="color:#000000;">Informasi Akun</h3>
        </div>

        <form method="POST" action="{{ route('siswa.profil.update') }}">
            @csrf
            @method('PUT')

            {{-- Nama Lengkap — dikunci, tidak bisa diubah siswa --}}
            <div class="mb-3">
                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text"
                       class="form-control form-control-locked"
                       value="{{ $user->nama_lengkap }}"
                       disabled readonly>
            </div>

            {{-- Nomor HP — satu-satunya yang bisa diubah --}}
            <div class="mb-3">
                <label for="no_hp" class="form-label">Nomor HP</label>
                <input type="text" id="no_hp" name="no_hp"
                       class="form-control @error('no_hp') is-invalid @enderror"
                       value="{{ old('no_hp', $user->no_hp) }}"
                       placeholder="Opsional"
                       maxlength="20">
                @error('no_hp')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- NIS / NIP — dikunci, tidak bisa diubah siswa --}}
            <div class="mb-3">
                <label class="form-label">NIS / NIP <span class="text-danger">*</span></label>
                <input type="text"
                       class="form-control form-control-locked"
                       value="{{ $user->nip_nis }}"
                       disabled readonly>
            </div>

            {{-- Nama Halaqah — dikunci, tidak bisa diubah siswa --}}
            <div class="mb-4">
                <label class="form-label">Nama Halaqah</label>
                <input type="text"
                       class="form-control form-control-locked"
                       value="{{ $namaHalaqah }}"
                       disabled readonly>
                <div class="form-text small text-muted mt-1">
                    Perubahan nama, NIS/NIP, dan penempatan halaqah dikelola oleh Admin.
                </div>
            </div>

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
                 style="width:40px; height:40px; background:linear-gradient(135deg,#F59E0B,#D97706);">
                <i class="bi bi-key-fill" style="color:#fff; font-size:1rem;"></i>
            </div>
            <h3 class="fw-bold mb-0 h5" style="color:#000000;">Ganti Password</h3>
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

            {{-- Kata Sandi Saat Ini --}}
            <div class="mb-3">
                <label for="current_password" class="form-label">Kata Sandi Saat Ini <span class="text-danger">*</span></label>
                <div style="position:relative;">
                    <input type="password" id="current_password" name="current_password"
                           class="form-control @error('current_password') is-invalid @enderror"
                           placeholder="Masukkan kata sandi lama"
                           style="padding-right:42px;"
                           autocomplete="current-password" required>
                    <button type="button" onclick="togglePassword(this)" aria-label="Lihat kata sandi"
                            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;padding:0;cursor:pointer;color:#64748B;display:flex;align-items:center;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="icon-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="icon-eye-off" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
                @error('current_password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Kata Sandi Baru --}}
            <div class="mb-3">
                <label for="password" class="form-label">Kata Sandi Baru <span class="text-danger">*</span></label>
                <div style="position:relative;">
                    <input type="password" id="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Minimal 6 karakter"
                           style="padding-right:42px;"
                           autocomplete="new-password" required>
                    <button type="button" onclick="togglePassword(this)" aria-label="Lihat kata sandi"
                            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;padding:0;cursor:pointer;color:#64748B;display:flex;align-items:center;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="icon-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="icon-eye-off" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Konfirmasi Kata Sandi --}}
            <div class="mb-4">
                <label for="password_confirmation" class="form-label">Konfirmasi Ganti Kata Sandi <span class="text-danger">*</span></label>
                <div style="position:relative;">
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="form-control"
                           placeholder="Ulangi kata sandi baru"
                           style="padding-right:42px;"
                           autocomplete="new-password" required>
                    <button type="button" onclick="togglePassword(this)" aria-label="Lihat kata sandi"
                            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;padding:0;cursor:pointer;color:#64748B;display:flex;align-items:center;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="icon-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="icon-eye-off" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
            </div>

            {{-- Catatan keamanan --}}
            <div class="info-box mb-4">
                <i class="bi bi-shield-lock-fill me-1"></i>
                Pastikan kata sandi Anda kuat dan tidak mudah ditebak demi keamanan akun siswa.
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
function togglePassword(btn) {
    var wrapper = btn.parentElement;
    var input   = wrapper.querySelector('input');
    var eyeOpen = btn.querySelector('.icon-eye');
    var eyeOff  = btn.querySelector('.icon-eye-off');
    var show    = input.type === 'password';
    input.type            = show ? 'text' : 'password';
    eyeOpen.style.display = show ? 'none' : '';
    eyeOff.style.display  = show ? ''     : 'none';
    btn.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Lihat kata sandi');
}
</script>
@endsection
