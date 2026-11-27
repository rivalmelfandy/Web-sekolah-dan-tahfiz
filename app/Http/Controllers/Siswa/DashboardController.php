<?php

namespace App\Http\Controllers\Siswa;

use App\Enums\NilaiIndeks;
use App\Http\Controllers\Controller;
use App\Models\Murajaah;
use App\Models\SetoranHafalan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Halaman utama dashboard siswa — Progres Hafalan.
     *
     * Skema DBML terkini:
     *   setoran_hafalan : nilai_hafalan (tinyint default 80)
     *   murajaah        : setoran_id FK (tidak ada siswa_id langsung)
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // ── Total setoran ───────────────────────────────────────────────
        $totalSetoran = SetoranHafalan::where('siswa_id', $user->id)->count();

        // "Setoran baik" = nilai_hafalan >= 80 (predikat AB ke atas)
        $setoranLancar = SetoranHafalan::where('siswa_id', $user->id)
            ->where('nilai_hafalan', '>=', 80)
            ->count();

        // ── Total murajaah via setoran ──────────────────────────────────
        $totalMurajaah = Murajaah::whereHas(
            'setoran',
            fn ($q) => $q->where('siswa_id', $user->id)
        )->count();

        // ── Target hafalan aktif ────────────────────────────────────────
        $targetAktifRaw = DB::table('target_hafalan')
            ->where('siswa_id', $user->id)
            ->where('status', 'Aktif')
            ->orderByDesc('tanggal_mulai')
            ->orderByDesc('id')
            ->first();

        $namaUstadzTarget = null;
        if ($targetAktifRaw && $targetAktifRaw->ustadz_id) {
            $namaUstadzTarget = DB::table('users')
                ->where('id', $targetAktifRaw->ustadz_id)
                ->value('nama_lengkap');
        }

        // ── Sisa hari target ────────────────────────────────────────────
        $hariSisaTarget = null;
        if ($targetAktifRaw) {
            $hariSisaTarget = (int) now()->diffInDays(
                Carbon::parse($targetAktifRaw->tanggal_akhir),
                false
            );
        }

        // ── 5 Setoran terbaru ───────────────────────────────────────────
        $setoranTerbaruRaw = SetoranHafalan::where('siswa_id', $user->id)
            ->orderByDesc('tanggal_setor')
            ->orderByDesc('id')
            ->limit(5)
            ->get(['id', 'surah', 'ayat_awal', 'ayat_akhir', 'nilai_hafalan', 'tanggal_setor', 'ustadz_id']);

        $ustadzIds = $setoranTerbaruRaw->pluck('ustadz_id')->filter()->unique()->values();
        $ustadzMap = DB::table('users')
            ->whereIn('id', $ustadzIds)
            ->pluck('nama_lengkap', 'id');

        $setoranTerbaru = $setoranTerbaruRaw->map(fn ($s) => (object) [
            'id'           => $s->id,
            'surah'        => $s->surah,
            'ayat_awal'    => $s->ayat_awal,
            'ayat_akhir'   => $s->ayat_akhir,
            'nilai'        => $s->nilai_hafalan,
            'indeks'       => NilaiIndeks::dariNilai($s->nilai_hafalan),
            'tanggal_setor' => $s->tanggal_setor,
            'nama_ustadz'  => $ustadzMap[$s->ustadz_id] ?? '–',
        ]);

        $namaHalaqah     = $user->halaqah?->nama_halaqah ?? null;
        $setoranTerakhir = $setoranTerbaru->first();

        return view('siswa.dashboard', compact(
            'user',
            'totalSetoran',
            'setoranLancar',
            'totalMurajaah',
            'targetAktifRaw',
            'namaUstadzTarget',
            'hariSisaTarget',
            'setoranTerbaru',
            'setoranTerakhir',
            'namaHalaqah',
        ));
    }
}
