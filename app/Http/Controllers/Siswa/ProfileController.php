<?php

namespace App\Http\Controllers\Siswa;

use App\Enums\NilaiIndeks;
use App\Http\Controllers\Controller;
use App\Models\Murajaah;
use App\Models\SetoranHafalan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil siswa yang sedang login.
     */
    public function index(): View
    {
        $user = auth()->user();

        // Nama halaqah via relasi (halaqah_id FK, bukan varchar).
        $namaHalaqah = $user->halaqah?->nama_halaqah
            ?? 'Belum tergabung dalam halaqah';

        // ── Ringkasan capaian hafalan untuk kartu identitas ───────────
        $totalSetoran = SetoranHafalan::where('siswa_id', $user->id)->count();

        $rataNilai = null;
        $predikatRata = null;
        if ($totalSetoran > 0) {
            $avg = SetoranHafalan::where('siswa_id', $user->id)->avg('nilai_hafalan');
            $rataNilai = round((float) $avg, 1);
            $predikatRata = NilaiIndeks::dariNilai($rataNilai);
        }

        // Murajaah tidak punya siswa_id; harus JOIN ke setoran_hafalan.
        $totalMurajaah = Murajaah::whereHas(
            'setoran',
            fn ($q) => $q->where('siswa_id', $user->id)
        )->count();

        // ── Ustadz pembimbing dari target aktif (jika ada) ────────────
        $targetAktif = DB::table('target_hafalan')
            ->where('siswa_id', $user->id)
            ->where('status', 'Aktif')
            ->orderByDesc('tanggal_mulai')
            ->first();

        $namaUstadz = null;
        if ($targetAktif && $targetAktif->ustadz_id) {
            $namaUstadz = DB::table('users')
                ->where('id', $targetAktif->ustadz_id)
                ->value('nama_lengkap');
        }

        return view('siswa.profil', compact(
            'user',
            'namaHalaqah',
            'totalSetoran',
            'totalMurajaah',
            'rataNilai',
            'predikatRata',
            'namaUstadz',
        ));
    }

    /**
     * Simpan perubahan nomor HP (satu-satunya kolom yang bisa diubah siswa).
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
     * Perbarui kata sandi siswa yang sedang login.
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
