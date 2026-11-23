<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil administrator yang sedang login.
     */
    public function index(): View
    {
        return view('admin.profil', ['user' => auth()->user()]);
    }

    /**
     * Simpan perubahan informasi akun (nama_lengkap, no_hp, nip_nis).
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'nip_nis' => [
                'required', 'string', 'max:30',
                Rule::unique('users', 'nip_nis')->ignore(auth()->id()),
            ],
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nip_nis.required' => 'NIP/NIS wajib diisi.',
            'nip_nis.unique' => 'NIP/NIS sudah digunakan oleh pengguna lain.',
        ]);

        auth()->user()->update([
            'nama_lengkap' => $request->nama_lengkap,
            'no_hp' => $request->no_hp,
            'nip_nis' => $request->nip_nis,
        ]);

        return back()->with('success', 'Informasi akun berhasil diperbarui.');
    }

    /**
     * Perbarui kata sandi administrator yang sedang login.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sesuai.',
        ]);

        if (! Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors([
                'current_password' => 'Kata sandi saat ini tidak sesuai.',
            ]);
        }

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success_password', 'Kata sandi berhasil diperbarui.');
    }
}
