<?php

namespace App\Http\Controllers\Ustadz;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ustadz\StoreMurajaahRequest;
use App\Models\Halaqah;
use App\Models\Murajaah;
use App\Models\SetoranHafalan;
use App\Models\User;
use App\Support\QuranHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MurajaahController extends Controller
{
    /**
     * Halaman Penilaian Murajaah Santri (Layout 2 Kolom RPLBO).
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // ── Ambil halaqah dan santri binaan ─────────────────────────────
        $halaqah = Halaqah::where('ustadz_id', $user->id)->first();

        if ($halaqah) {
            $santriList = User::where('role', UserRole::Siswa)
                ->where('halaqah_id', $halaqah->id)
                ->orderBy('nama_lengkap')
                ->get();
        } else {
            $santriList = User::where('role', UserRole::Siswa)
                ->orderBy('nama_lengkap')
                ->get();
        }

        // ── Santri yang sedang dipilih ──────────────────────────────────
        $selectedSiswaId = $request->integer('siswa_id') ?: $santriList->first()?->id;
        $selectedSiswa = $santriList->firstWhere('id', $selectedSiswaId);

        // ── Riwayat setoran untuk di-murajaah & riwayat murajaah yang sudah ada ──
        $riwayatSetoran = collect();
        $riwayatMurajaah = collect();

        if ($selectedSiswa) {
            $riwayatSetoran = SetoranHafalan::where('siswa_id', $selectedSiswa->id)
                ->orderByDesc('tanggal_setor')
                ->orderByDesc('id')
                ->limit(15)
                ->get();

            $riwayatMurajaah = Murajaah::where('siswa_id', $selectedSiswa->id)
                ->orderByDesc('tanggal_murajaah')
                ->orderByDesc('id')
                ->limit(15)
                ->get();
        }

        $surahList = QuranHelper::getSurahList();

        return view('ustadz.murajaah', [
            'user' => $user,
            'halaqah' => $halaqah,
            'santriList' => $santriList,
            'selectedSiswa' => $selectedSiswa,
            'riwayatSetoran' => $riwayatSetoran,
            'riwayatMurajaah' => $riwayatMurajaah,
            'surahList' => $surahList,
        ]);
    }

    /**
     * Simpan evaluasi murajaah ke database.
     */
    public function store(StoreMurajaahRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['ustadz_id'] = $request->user()->id;

        $murajaah = Murajaah::create($data);

        // Jika murajaah terkait dengan setoran tertentu, perbarui kualitas setoran jika mutqin
        if (!empty($data['setoran_id'])) {
            $setoran = SetoranHafalan::find($data['setoran_id']);
            if ($setoran) {
                $setoran->update(['kualitas' => $data['status_mutqin']]);
            }
        }

        $siswa = User::find($data['siswa_id']);
        $namaSiswa = $siswa?->nama_lengkap ?? 'Siswa';

        return redirect()
            ->route('ustadz.murajaah.index', ['siswa_id' => $data['siswa_id']])
            ->with('success', "Alhamdulillah! Evaluasi murajaah {$namaSiswa} untuk Surah {$murajaah->surah} ({$murajaah->ayat_range}) berhasil disimpan dengan status '{$murajaah->status_mutqin->value}'.");
    }
}
