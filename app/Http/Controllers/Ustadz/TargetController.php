<?php

namespace App\Http\Controllers\Ustadz;

use App\Enums\TargetStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ustadz\StoreTargetRequest;
use App\Models\Halaqah;
use App\Models\TargetHafalan;
use App\Models\User;
use App\Support\QuranHelper;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TargetController extends Controller
{
    /**
     * Halaman Penetapan Target Hafalan Santri (Layout 2 Kolom RPLBO).
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

        // ── Peta target aktif per siswa untuk indikator badge di panel kiri ──
        $targetAktifMap = TargetHafalan::whereIn('siswa_id', $santriIds)
            ->where('status', TargetStatus::Aktif->value)
            ->orderByDesc('tanggal_mulai')
            ->get()
            ->keyBy('siswa_id');

        // ── Riwayat seluruh target per siswa ────────────────────────────
        $semuaTarget = TargetHafalan::whereIn('siswa_id', $santriIds)
            ->orderByDesc('created_at')
            ->get();
        $riwayatTargetMap = $semuaTarget->groupBy('siswa_id');

        // ── Santri yang sedang dipilih (null jika baru buka) ─────────────
        $selectedSiswaId = $request->integer('siswa_id') ?: null;
        $selectedSiswa = $selectedSiswaId ? $santriList->firstWhere('id', $selectedSiswaId) : null;

        // ── Detail target santri terpilih ───────────────────────────────
        $targetAktif = null;
        $riwayatTarget = collect();
        $hariSisa = null;

        if ($selectedSiswa) {
            $targetAktif = $targetAktifMap->get($selectedSiswa->id);

            if ($targetAktif) {
                $hariSisa = (int) now()->diffInDays(Carbon::parse($targetAktif->tanggal_akhir), false);
            }

            $riwayatTarget = $riwayatTargetMap->get($selectedSiswa->id, collect());
        }

        $kurikulumTarget = QuranHelper::getKurikulumTarget();
        $surahList = QuranHelper::getSurahList();

        return view('ustadz.target', [
            'user' => $user,
            'halaqah' => $halaqah,
            'santriList' => $santriList,
            'targetAktifMap' => $targetAktifMap,
            'riwayatTargetMap' => $riwayatTargetMap,
            'selectedSiswa' => $selectedSiswa,
            'targetAktif' => $targetAktif,
            'riwayatTarget' => $riwayatTarget,
            'hariSisa' => $hariSisa,
            'kurikulumTarget' => $kurikulumTarget,
            'surahList' => $surahList,
        ]);
    }

    /**
     * Simpan penetapan target hafalan baru ke database.
     */
    public function store(StoreTargetRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['ustadz_id'] = $request->user()->id;
        $data['status'] = TargetStatus::Aktif->value;

        // Nonaktifkan target aktif sebelumnya jika ada
        TargetHafalan::where('siswa_id', $data['siswa_id'])
            ->where('status', TargetStatus::Aktif->value)
            ->update(['status' => TargetStatus::Nonaktif->value]);

        $target = TargetHafalan::create($data);

        $siswa = User::find($data['siswa_id']);
        $namaSiswa = $siswa?->nama_lengkap ?? 'Siswa';

        return redirect()
            ->route('ustadz.target.index', ['siswa_id' => $data['siswa_id']])
            ->with('success', "Target hafalan untuk {$namaSiswa} ({$target->surah_awal} s.d. {$target->surah_akhir}) berhasil ditetapkan dan diaktifkan.");
    }

    /**
     * Tandai target hafalan sebagai Selesai.
     */
    public function markSelesai(Request $request, TargetHafalan $target): RedirectResponse
    {
        $target->update(['status' => TargetStatus::Selesai->value]);

        $siswa = User::find($target->siswa_id);
        $namaSiswa = $siswa?->nama_lengkap ?? 'Siswa';

        return redirect()
            ->route('ustadz.target.index', ['siswa_id' => $target->siswa_id])
            ->with('success', "Target hafalan {$namaSiswa} telah ditandai Selesai. Barakallahu fiik!");
    }
}
