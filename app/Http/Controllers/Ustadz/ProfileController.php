<?php

namespace App\Http\Controllers\Ustadz;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil ustadz yang sedang login.
     */
    public function index(): View
    {
        return view('ustadz.profil', ['user' => auth()->user()]);
    }

    /**
     * Simpan perubahan nomor HP (satu-satunya kolom yang bisa diubah ustadz).
     * Nama lengkap dan NIP dikunci — hanya admin yang bisa mengubahnya.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $request->validate([
            'no_hp' => ['nullable', 'string', 'max:20'],
        ]);

        auth()->user()->update([
            'no_hp' => $request->no_hp,
        ]);

        return back()->with('success', 'Informasi akun berhasil diperbarui.');
    }

    /**
     * Perbarui kata sandi ustadz yang sedang login.
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
