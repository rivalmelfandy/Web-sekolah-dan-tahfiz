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
     * Skema aktual (docs/Project tahfiz_yuk.dbml):
     *   setoran_hafalan : siswa_id, nilai_hafalan (tinyint default 80)
     *   murajaah        : setoran_id → setoran_hafalan.id (TIDAK ada siswa_id langsung)
     *   target_hafalan  : siswa_id, status (enum Aktif|Nonaktif)
     *   users           : id, nama_lengkap, nip_nis, no_hp, role, halaqah_id
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // ── Total setoran ──────────────────────────────────────────────
        $totalSetoran = SetoranHafalan::where('siswa_id', $user->id)->count();

        // ── Rata-rata nilai hafalan ────────────────────────────────────
        $rataNilai = null;
        $predikatRata = null;

        if ($totalSetoran > 0) {
            $avg = SetoranHafalan::where('siswa_id', $user->id)->avg('nilai_hafalan');
            $rataNilai = round((float) $avg, 1);
            $predikatRata = NilaiIndeks::dariNilai($rataNilai);
        }

        // ── Total murajaah (lewat setoran_hafalan.siswa_id) ────────────
        // Murajaah tidak punya siswa_id langsung; harus JOIN ke setoran_hafalan.
        $totalMurajaah = Murajaah::whereHas(
            'setoran',
            fn ($q) => $q->where('siswa_id', $user->id)
        )->count();

        // ── Target hafalan aktif ───────────────────────────────────────
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

        // ── Sisa hari target ───────────────────────────────────────────
        $hariSisaTarget = null;
        if ($targetAktifRaw) {
            $hariSisaTarget = (int) now()->diffInDays(
                Carbon::parse($targetAktifRaw->tanggal_akhir),
                false
            );
        }

        // ── Capaian target dalam persen (proxy: rata-rata nilai) ───────
        $pctCapaian = $rataNilai !== null
            ? (int) min(100, max(0, $rataNilai))
            : 0;

        // ── 5 Setoran terbaru ──────────────────────────────────────────
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
            'id' => $s->id,
            'surah' => $s->surah,
            'ayat_awal' => $s->ayat_awal,
            'ayat_akhir' => $s->ayat_akhir,
            'nilai' => $s->nilai_hafalan,
            'indeks' => NilaiIndeks::dariNilai($s->nilai_hafalan),
            'tanggal_setor' => $s->tanggal_setor,  // sudah di-cast Carbon oleh model
            'nama_ustadz' => $ustadzMap[$s->ustadz_id] ?? '–',
        ]);

        // ── Nama halaqah siswa ─────────────────────────────────────────
        $namaHalaqah = $user->halaqah?->nama_halaqah ?? null;

        $setoranTerakhir = $setoranTerbaru->first();

        return view('siswa.dashboard', compact(
            'user',
            'totalSetoran',
            'rataNilai',
            'predikatRata',
            'totalMurajaah',
            'targetAktifRaw',
            'namaUstadzTarget',
            'hariSisaTarget',
            'setoranTerbaru',
            'setoranTerakhir',
            'namaHalaqah',
            'pctCapaian',
        ));
    }
}
