@extends('layouts.admin', ['title' => 'Kelola Pengguna'])

@section('breadcrumb', 'Data Pengguna')

@section('styles')
<style>
/* ── Toast notifikasi ───────────────────────────────────────────── */
.user-toast {
    position: fixed; top: 1.25rem; right: 1.25rem; z-index: 2000;
    min-width: 300px; max-width: 400px;
    border-radius: 12px; padding: .85rem 1rem;
    display: flex; align-items: flex-start; gap: .65rem;
    box-shadow: 0 8px 28px rgba(0,0,0,.14);
    animation: slideInRight .3s ease;
}
.user-toast.success { background: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; }
.user-toast.error   { background: #FEF2F2; border: 1px solid #FCA5A5; color: #991B1B; }
.user-toast i { font-size: 1rem; margin-top: .05rem; flex-shrink: 0; }
.user-toast-close { background: none; border: none; padding: 0; margin-left: auto; cursor: pointer; opacity: .6; color: inherit; }
.user-toast-close:hover { opacity: 1; }
@keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
/* ── Badge aksi edit (amber) ────────────────────────────────────── */
.btn-edit-user { background: #FFFBEB; color: #D97706; border-color: #F59E0B; }
.btn-edit-user:hover { background: #FEF3C7; color: #D97706; border-color: #F59E0B; }
/* ── Tombol aksi hapus (merah lembut) ───────────────────────────── */
.btn-hapus-user { background: #FEF2F2; color: #DC2626; border: 1px solid #FCA5A5; }
.btn-hapus-user:hover { background: #FEE2E2; color: #DC2626; border-color: #FCA5A5; }
/* ── Tombol Tambah Pengguna Baru (hijau tema) ───────────────────── */
.btn-tambah-pengguna { background: #0D6B65; border-color: #0D6B65; color: #FFFFFF; }
.btn-tambah-pengguna:hover { background: #09524D; border-color: #09524D; color: #FFFFFF; }
</style>
@endsection

@section('content')

{{-- ── Toast Notifikasi ─────────────────────────────────────────────── --}}
@if(session('success'))
<div class="user-toast success" id="userToast" role="alert">
    <i class="bi bi-check-circle-fill"></i>
    <span class="small fw-semibold">{{ session('success') }}</span>
    <button type="button" class="user-toast-close" onclick="document.getElementById('userToast').remove()" aria-label="Tutup">
        <i class="bi bi-x-lg"></i>
    </button>
</div>
<script>setTimeout(function(){ var t=document.getElementById('userToast'); if(t) t.remove(); }, 4500);</script>
@endif

@if(session('error'))
<div class="user-toast error" id="userToastErr" role="alert">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <span class="small fw-semibold">{{ session('error') }}</span>
    <button type="button" class="user-toast-close" onclick="document.getElementById('userToastErr').remove()" aria-label="Tutup">
        <i class="bi bi-x-lg"></i>
    </button>
</div>
<script>setTimeout(function(){ var t=document.getElementById('userToastErr'); if(t) t.remove(); }, 5500);</script>
@endif

{{-- ── Header halaman ───────────────────────────────────────────────── --}}
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
    <div>
        <h2 class="fw-bold mb-0 h4" style="color:#1F4172;">
            <i class="bi bi-people me-2"></i>Data Pengguna
        </h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active">Pengguna</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('admin.users.create') }}"
       class="btn btn-sm fw-semibold rounded-pill px-3 btn-tambah-pengguna">
        <i class="bi bi-person-plus me-1"></i>Tambah Pengguna Baru
    </a>
</div>

{{-- ── Tabel Pengguna ──────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background:#F0F9F8;">
                <tr>
                    <th class="px-4 py-3 small fw-semibold text-uppercase" style="color:#000000; letter-spacing:.04em;">No</th>
                    <th class="px-3 py-3 small fw-semibold text-uppercase" style="color:#000000; letter-spacing:.04em;">NIP / NIS</th>
                    <th class="px-3 py-3 small fw-semibold text-uppercase" style="color:#000000; letter-spacing:.04em;">Nama Lengkap</th>
                    <th class="px-3 py-3 small fw-semibold text-uppercase d-none d-md-table-cell" style="color:#000000; letter-spacing:.04em;">No. HP</th>
                    <th class="px-3 py-3 small fw-semibold text-uppercase" style="color:#000000; letter-spacing:.04em;">Peran</th>
                    <th class="px-3 py-3 small fw-semibold text-uppercase d-none d-lg-table-cell" style="color:#000000; letter-spacing:.04em;">Halaqah</th>
                    <th class="px-4 py-3 small fw-semibold text-uppercase text-center" style="color:#000000; letter-spacing:.04em;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr>
                    <td class="px-4 py-3 text-muted small">{{ $users->firstItem() + $loop->index }}</td>
                    <td class="px-3 py-3 fw-semibold small" style="color:#111827;">{{ $u->nip_nis }}</td>
                    <td class="px-3 py-3">
                        <div class="fw-semibold small">{{ $u->nama_lengkap }}</div>
                    </td>
                    <td class="px-3 py-3 text-muted small d-none d-md-table-cell">{{ $u->no_hp ?? '–' }}</td>
                    <td class="px-3 py-3">
                        @php
                            $badgeStyle = match($u->role->value) {
                                'admin'  => 'background:#FEF2F2; color:#991B1B; border:1px solid #FECACA;',
                                'ustadz' => 'background:#EFF6FF; color:#1D4ED8; border:1px solid #BFDBFE;',
                                'siswa'  => 'background:#ECFDF5; color:#065F46; border:1px solid #A7F3D0;',
                                default  => 'background:#F1F5F9; color:#475569; border:1px solid #CBD5E1;',
                            };
                        @endphp
                        <span class="badge rounded-pill fw-semibold small px-3 py-1" style="{{ $badgeStyle }}">
                            {{ $u->role->label() }}
                        </span>
                    </td>
                    <td class="px-3 py-3 text-muted small d-none d-lg-table-cell">
                        {{ $u->halaqah?->nama_halaqah ?? '–' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="d-flex justify-content-center gap-1">
                            {{-- Tombol edit → menuju halaman edit (modal) --}}
                            <a href="{{ route('admin.users.edit', $u) }}"
                               class="btn btn-sm btn-edit-user py-1 px-2" title="Edit">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            {{-- Tombol hapus --}}
                            @if(Auth::id() !== $u->id)
                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                                  onsubmit="return confirm('Hapus pengguna {{ addslashes($u->nama_lengkap) }}?\nSemua setoran dan data terkait akan ikut terhapus.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-hapus-user py-1 px-2" title="Hapus">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                            @else
                            <button class="btn btn-sm btn-outline-secondary py-1 px-2" disabled
                                    title="Tidak dapat menghapus akun aktif Anda">
                                <i class="bi bi-trash"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-5 text-center text-muted small">
                        <i class="bi bi-people display-6 d-block mb-2 opacity-25"></i>
                        Belum ada pengguna terdaftar.
                        <a href="{{ route('admin.users.create') }}" style="color:#047857;" class="d-block mt-1">
                            Tambahkan pengguna pertama
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Paginasi --}}
@if($users->hasPages())
<div class="mt-3 d-flex justify-content-end">
    {{ $users->links() }}
</div>
@endif

@endsection
