<?php

namespace App\Http\Controllers\Ustadz;

use App\Enums\TargetStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ustadz\StoreSetoranRequest;
use App\Models\Halaqah;
use App\Models\SetoranHafalan;
use App\Models\TargetHafalan;
use App\Models\User;
use App\Support\QuranHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HafalanController extends Controller
{
    /**
     * Halaman Penerimaan Setoran Hafalan Santri (Layout 2 Kolom RPLBO).
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

        $santriIds = $santriList->pluck('id');

        // ── Santri yang sedang dipilih (null jika baru buka halaman) ────
        $selectedSiswaId = $request->integer('siswa_id') ?: null;
        $selectedSiswa = $selectedSiswaId ? $santriList->firstWhere('id', $selectedSiswaId) : null;

        // ── Peta target aktif per santri ───────────────────────────────
        $targetAktifMap = TargetHafalan::whereIn('siswa_id', $santriIds)
            ->where('status', TargetStatus::Aktif)
            ->orderByDesc('tanggal_mulai')
            ->get()
            ->keyBy('siswa_id');

        // ── Peta setoran per santri (untuk riwayat di panel kanan) ──────
        $semuaSetoran = SetoranHafalan::whereIn('siswa_id', $santriIds)
            ->orderByDesc('tanggal_setor')
            ->orderByDesc('id')
            ->get();
        $setoranPerSiswa = $semuaSetoran->groupBy('siswa_id');

        $surahList = QuranHelper::getSurahList();

        return view('ustadz.hafalan', [
            'user' => $user,
            'halaqah' => $halaqah,
            'santriList' => $santriList,
            'selectedSiswa' => $selectedSiswa,
            'targetAktifMap' => $targetAktifMap,
            'setoranPerSiswa' => $setoranPerSiswa,
            'surahList' => $surahList,
        ]);
    }

    /**
     * Simpan setoran hafalan santri ke database.
     */
    public function store(StoreSetoranRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['ustadz_id'] = $request->user()->id;

        $setoran = SetoranHafalan::create($data);

        $siswa = User::find($data['siswa_id']);
        $namaSiswa = $siswa?->nama_lengkap ?? 'Siswa';

        return redirect()
            ->route('ustadz.hafalan.index', ['siswa_id' => $data['siswa_id']])
            ->with('success', "Alhamdulillah! Setoran {$namaSiswa} untuk Surah {$setoran->surah} (Ayat {$setoran->ayat_awal}–{$setoran->ayat_akhir}) kualitas '{$setoran->kualitas->value}' berhasil dicatat.");
    }
}
