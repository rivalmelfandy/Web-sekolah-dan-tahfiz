<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Halaqah;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Daftar seluruh pengguna, diurutkan: admin → ustadz → siswa, lalu abjad nama.
     */
    public function index(Request $request): View
    {
        $users = User::with('halaqah')
            ->orderByRaw("FIELD(role, 'admin', 'ustadz', 'siswa')")
            ->orderBy('nama_lengkap')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Form tambah pengguna baru.
     */
    public function create(): View
    {
        $halaqahList = Halaqah::orderBy('nama_halaqah')->get(['id', 'nama_halaqah', 'kode_halaqah']);
        $roles = UserRole::cases();

        return view('admin.users.create', compact('halaqahList', 'roles'));
    }

    /**
     * Simpan pengguna baru.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        // Paksa halaqah_id = null jika bukan siswa.
        if ($data['role'] !== UserRole::Siswa->value) {
            $data['halaqah_id'] = null;
        }

        User::create($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Form edit pengguna.
     */
    public function edit(User $user): View
    {
        $halaqahList = Halaqah::orderBy('nama_halaqah')->get(['id', 'nama_halaqah', 'kode_halaqah']);
        $roles = UserRole::cases();

        return view('admin.users.edit', compact('user', 'halaqahList', 'roles'));
    }

    /**
     * Perbarui data pengguna.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        // Jika password dikosongkan, jangan timpa hash lama.
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        // Paksa halaqah_id = null jika bukan siswa.
        if ($data['role'] !== UserRole::Siswa->value) {
            $data['halaqah_id'] = null;
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Hapus pengguna.
     * Admin tidak boleh menghapus akun dirinya sendiri.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->id === $user->id) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}
