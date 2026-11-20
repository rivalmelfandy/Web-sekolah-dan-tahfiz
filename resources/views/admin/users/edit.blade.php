@extends('layouts.admin', ['title' => 'Edit Pengguna'])

@section('breadcrumb', 'Edit Pengguna')

@section('styles')
<style>
/* ── Modal overlay ───────────────────────────────────────────────── */
.user-modal-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,.45);
    z-index: 1055;
    display: flex; align-items: center; justify-content: center;
    padding: 1rem;
}
/* ── Modal kartu ─────────────────────────────────────────────────── */
.user-modal {
    background: #fff;
    border-radius: 16px;
    width: 100%; max-width: 520px;
    max-height: 92vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0,0,0,.2);
    display: flex; flex-direction: column;
}
.user-modal::-webkit-scrollbar { width: 4px; }
.user-modal::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 99px; }
/* ── Header modal hijau ──────────────────────────────────────────── */
.user-modal-header {
    background: #047857;
    border-radius: 16px 16px 0 0;
    padding: 1rem 1.25rem .9rem;
    display: flex; align-items: center; justify-content: space-between;
    flex-shrink: 0;
}
.user-modal-title { font-size: .95rem; font-weight: 700; color: #fff; display: flex; align-items: center; gap: .5rem; }
.user-modal-close {
    background: none; border: none; padding: .25rem .4rem;
    color: rgba(255,255,255,.8); font-size: 1rem; cursor: pointer;
    border-radius: 6px; line-height: 1; transition: background .15s;
}
.user-modal-close:hover { background: rgba(255,255,255,.18); color: #fff; }
/* ── Body modal ──────────────────────────────────────────────────── */
.user-modal-body { padding: 1.25rem 1.25rem .5rem; flex: 1; }
/* ── Footer modal ────────────────────────────────────────────────── */
.user-modal-footer {
    padding: .9rem 1.25rem 1.1rem;
    display: flex; justify-content: flex-end; align-items: center; gap: .65rem;
    border-top: 1px solid #F1F5F9; flex-shrink: 0;
}
/* ── Tombol simpan hijau ─────────────────────────────────────────── */
.btn-modal-save {
    background: #047857; border: none; color: #fff;
    border-radius: 8px; padding: .5rem 1.1rem;
    font-size: .84rem; font-weight: 600;
    display: flex; align-items: center; gap: .4rem;
    transition: background .15s;
}
.btn-modal-save:hover  { background: #065F46; color: #fff; }
.btn-modal-save:active { background: #064E3B; color: #fff; }
.btn-modal-save:focus-visible { outline: 2px solid #047857; outline-offset: 2px; }
/* ── Label & input ───────────────────────────────────────────────── */
.user-modal-body .form-label { font-size: .78rem; font-weight: 600; color: #374151; margin-bottom: .3rem; }
.user-modal-body .form-control,
.user-modal-body .form-select { font-size: .84rem; border-radius: 8px; border-color: #D1D5DB; }
.user-modal-body .form-control:focus,
.user-modal-body .form-select:focus { border-color: #047857; box-shadow: 0 0 0 3px rgba(4,120,87,.12); }
.user-modal-body .form-text { font-size: .72rem; }
</style>
@endsection

@section('content')

{{-- ── Alert sukses/gagal dari session ───────────────────────────── --}}
@include('admin._alert')

{{-- ── OVERLAY + MODAL ─────────────────────────────────────────────── --}}
<div class="user-modal-overlay" id="userModalOverlay">
    <div class="user-modal" role="dialog" aria-modal="true" aria-labelledby="modalTitleEdit">

        {{-- Header --}}
        <div class="user-modal-header">
            <div class="user-modal-title">
                <i class="bi bi-pencil-square"></i>
                <span id="modalTitleEdit">Edit Data Pengguna</span>
            </div>
            <a href="{{ route('admin.users.index') }}" class="user-modal-close" aria-label="Tutup">
                <i class="bi bi-x-lg"></i>
            </a>
        </div>

        {{-- Body --}}
        <div class="user-modal-body">

            {{-- Error validasi --}}
            @if($errors->any())
            <div class="alert alert-danger d-flex align-items-start gap-2 py-2 mb-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-1"></i>
                <div class="small">
                    <div class="fw-semibold mb-1">Mohon perbaiki kesalahan berikut:</div>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.users.update', $user) }}" id="formEditUser">
                @csrf
                @method('PUT')

                {{-- Nama Lengkap --}}
                <div class="mb-3">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap"
                           class="form-control @error('nama_lengkap') is-invalid @enderror"
                           value="{{ old('nama_lengkap', $user->nama_lengkap) }}"
                           maxlength="100" required>
                    @error('nama_lengkap')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- NIP/NIS + Password opsional berdampingan --}}
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label for="nip_nis" class="form-label">NIP / NIS <span class="text-danger">*</span></label>
                        <input type="text" id="nip_nis" name="nip_nis"
                               class="form-control @error('nip_nis') is-invalid @enderror"
                               value="{{ old('nip_nis', $user->nip_nis) }}"
                               maxlength="30" required>
                        @error('nip_nis')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label for="password" class="form-label">Ganti Sandi <span class="text-muted fw-normal" style="font-size:.68rem;">(Opsional)</span></label>
                        <input type="password" id="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Kosongkan jika tidak ubah"
                               autocomplete="new-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Peran + No. HP berdampingan --}}
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label for="role" class="form-label">Peran / Role <span class="text-danger">*</span></label>
                        <select id="role" name="role"
                                class="form-select @error('role') is-invalid @enderror"
                                required onchange="toggleHalaqahEdit(this.value)">
                            @foreach($roles as $r)
                                <option value="{{ $r->value }}"
                                    {{ old('role', $user->role->value) === $r->value ? 'selected' : '' }}>
                                    {{ $r->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label for="no_hp" class="form-label">No. HP</label>
                        <input type="text" id="no_hp" name="no_hp"
                               class="form-control @error('no_hp') is-invalid @enderror"
                               value="{{ old('no_hp', $user->no_hp) }}"
                               placeholder="Opsional"
                               maxlength="20">
                        @error('no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Halaqah (hanya siswa) --}}
                @php
                    $initRole = old('role', $user->role->value);
                @endphp
                <div class="mb-2" id="halaqahFieldEdit"
                     style="{{ $initRole === 'siswa' ? '' : 'display:none;' }}">
                    <label for="halaqah_id" class="form-label">Kelompok Halaqah <span class="text-muted fw-normal" style="font-size:.7rem;">(opsional)</span></label>
                    <select id="halaqah_id" name="halaqah_id"
                            class="form-select @error('halaqah_id') is-invalid @enderror">
                        <option value="">— Belum Ditentukan —</option>
                        @foreach($halaqahList as $h)
                            <option value="{{ $h->id }}"
                                {{ old('halaqah_id', $user->halaqah_id) == $h->id ? 'selected' : '' }}>
                                {{ $h->nama_halaqah }} ({{ $h->kode_halaqah }})
                            </option>
                        @endforeach
                    </select>
                    @error('halaqah_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </form>
        </div>

        {{-- Footer --}}
        <div class="user-modal-footer">
            <a href="{{ route('admin.users.index') }}" class="btn btn-link text-secondary text-decoration-none fw-semibold" style="font-size:.84rem;">
                Batal
            </a>
            <button type="submit" form="formEditUser" class="btn-modal-save">
                <i class="bi bi-check-circle-fill"></i>Simpan Perubahan
            </button>
        </div>

    </div>{{-- /modal --}}
</div>{{-- /overlay --}}

@endsection

@section('scripts')
<script>
function toggleHalaqahEdit(role) {
    document.getElementById('halaqahFieldEdit').style.display = role === 'siswa' ? '' : 'none';
}
</script>
@endsection
