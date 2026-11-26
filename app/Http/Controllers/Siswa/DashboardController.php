<?php

namespace App\Http\Controllers\Siswa;

use App\Enums\KualitasLevel;
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
     * Skema SQL terbaru:
     *   users            : id, nip_nis, password, nama_lengkap, no_hp, role, halaqah_id
     *   setoran_hafalan  : id, siswa_id, ustadz_id, surah, ayat_awal, ayat_akhir, kualitas, catatan, tanggal_setor
     *   murajaah         : id, setoran_id, siswa_id, ustadz_id, surah, ayat_range, status_mutqin, catatan, tanggal_murajaah
     *   target_hafalan   : id, siswa_id, ustadz_id, surah_awal, surah_akhir, tanggal_mulai, tanggal_akhir, status (Aktif|Selesai|Nonaktif)
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // ── Total setoran ───────────────────────────────────────────────
        $totalSetoran = SetoranHafalan::where('siswa_id', $user->id)->count();

        // ── Setoran Lancar (kualitas = 'Lancar') ────────────────────────
        $setoranLancar = SetoranHafalan::where('siswa_id', $user->id)
            ->where('kualitas', KualitasLevel::Lancar->value)
            ->count();

        // ── Total murajaah (murajaah punya siswa_id langsung) ──────────
        $totalMurajaah = Murajaah::where('siswa_id', $user->id)->count();

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
            ->get(['id', 'surah', 'ayat_awal', 'ayat_akhir', 'kualitas', 'tanggal_setor', 'ustadz_id']);

        $ustadzIds = $setoranTerbaruRaw->pluck('ustadz_id')->filter()->unique()->values();
        $ustadzMap = DB::table('users')
            ->whereIn('id', $ustadzIds)
            ->pluck('nama_lengkap', 'id');

        $setoranTerbaru = $setoranTerbaruRaw->map(fn($s) => (object) [
            'id' => $s->id,
            'surah' => $s->surah,
            'ayat_awal' => $s->ayat_awal,
            'ayat_akhir' => $s->ayat_akhir,
            'kualitas' => $s->kualitas instanceof KualitasLevel
                ? $s->kualitas
                : KualitasLevel::from($s->kualitas),
            'tanggal_setor' => $s->tanggal_setor,  // Carbon (di-cast model)
            'nama_ustadz' => $ustadzMap[$s->ustadz_id] ?? '–',
        ]);

        // ── Nama halaqah siswa (via halaqah_id FK) ──────────────────────
        $namaHalaqah = $user->halaqah?->nama_halaqah ?? null;

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
