<?php

namespace App\Http\Controllers\Siswa;

use App\Enums\KualitasLevel;
use App\Http\Controllers\Controller;
use App\Models\Murajaah;
use App\Models\SetoranHafalan;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RiwayatController extends Controller
{
    /**
     * Halaman riwayat hafalan & murajaah siswa yang sedang login.
     *
     * Skema SQL terbaru:
     *   setoran_hafalan : siswa_id, surah, ayat_awal, ayat_akhir, kualitas (enum), catatan, tanggal_setor, ustadz_id
     *   murajaah        : siswa_id (langsung), setoran_id (nullable), surah, ayat_range,
     *                     status_mutqin (enum), catatan, tanggal_murajaah, ustadz_id
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // ── Ambil semua setoran hafalan milik siswa ─────────────────────
        $semuaSetoran = SetoranHafalan::where('siswa_id', $user->id)
            ->orderByDesc('tanggal_setor')
            ->orderByDesc('id')
            ->get(['id', 'surah', 'ayat_awal', 'ayat_akhir', 'kualitas', 'catatan', 'tanggal_setor', 'ustadz_id']);

        // ── Ambil semua murajaah milik siswa via siswa_id langsung ──────
        $semuaMurajaah = Murajaah::where('siswa_id', $user->id)
            ->orderByDesc('tanggal_murajaah')
            ->orderByDesc('id')
            ->get(['id', 'setoran_id', 'siswa_id', 'ustadz_id', 'surah', 'ayat_range', 'status_mutqin', 'catatan', 'tanggal_murajaah']);

        // ── Satu kali query nama ustadz ─────────────────────────────────
        $ustadzIds = $semuaSetoran->pluck('ustadz_id')
            ->concat($semuaMurajaah->pluck('ustadz_id'))
            ->filter()
            ->unique()
            ->values();

        $ustadzMap = DB::table('users')
            ->whereIn('id', $ustadzIds)
            ->pluck('nama_lengkap', 'id');

        // ── Statistik ───────────────────────────────────────────────────
        $totalSetoran = $semuaSetoran->count();
        $totalMurajaah = $semuaMurajaah->count();

        // Distribusi kualitas setoran (untuk panel kanan)
        $distribusiKualitas = $semuaSetoran
            ->groupBy(fn($s) => ($s->kualitas instanceof KualitasLevel
                ? $s->kualitas->value
                : (string) $s->kualitas) ?? '–')
            ->map->count();

        // ── Gabungkan ke satu koleksi aktivitas terurut ─────────────────
        $aktivitas = $this->gabungkanAktivitas($semuaSetoran, $semuaMurajaah, $ustadzMap);

        // ── Filter kategori ─────────────────────────────────────────────
        $filterKategori = $request->input('kategori', 'semua');
        if ($filterKategori !== 'semua') {
            $aktivitas = $aktivitas->filter(
                fn($item) => strtolower($item['kategori']) === strtolower($filterKategori)
            )->values();
        }

        // ── Filter surah ────────────────────────────────────────────────
        $filterSurah = trim($request->input('surah', ''));
        if ($filterSurah !== '') {
            $aktivitas = $aktivitas->filter(
                fn($item) => str_contains(
                    mb_strtolower($item['nama_surah']),
                    mb_strtolower($filterSurah)
                )
            )->values();
        }

        return view('siswa.riwayat', compact(
            'aktivitas',
            'totalSetoran',
            'totalMurajaah',
            'distribusiKualitas',
            'filterKategori',
            'filterSurah',
        ));
    }

    // ── Helper ─────────────────────────────────────────────────────────

    /**
     * Gabungkan Collection setoran dan murajaah ke satu Collection
     * terurut dari tanggal terbaru.
     *
     * @param  Collection<int, SetoranHafalan>  $setoran
     * @param  Collection<int, Murajaah>  $murajaah
     * @param  Collection  $ustadzMap  id→nama_lengkap
     */
    private function gabungkanAktivitas(
        Collection $setoran,
        Collection $murajaah,
        Collection $ustadzMap
    ): Collection {
        $barisSetoran = $setoran->map(function ($s) use ($ustadzMap) {
            $kualitas = $s->kualitas instanceof KualitasLevel
                ? $s->kualitas
                : KualitasLevel::tryFrom((string) $s->kualitas);

            return [
                'id' => $s->id,
                'kategori' => 'Hafalan',
                'nama_surah' => $s->surah . ' (' . $s->ayat_awal . '–' . $s->ayat_akhir . ')',
                'ayat_awal' => $s->ayat_awal,
                'ayat_akhir' => $s->ayat_akhir,
                'kualitas' => $kualitas,
                'tanggal' => $s->tanggal_setor,  // Carbon
                'ustadz' => $ustadzMap[$s->ustadz_id] ?? '–',
                'catatan' => $s->catatan,
            ];
        });

        $barisMurajaah = $murajaah->map(function ($m) use ($ustadzMap) {
            $statusMutqin = $m->status_mutqin instanceof KualitasLevel
                ? $m->status_mutqin
                : KualitasLevel::tryFrom((string) $m->status_mutqin);

            return [
                'id' => $m->id,
                'kategori' => 'Murajaah',
                'nama_surah' => ($m->surah ?? '') . ' (' . ($m->ayat_range ?? '–') . ')',
                'ayat_awal' => null,
                'ayat_akhir' => null,
                'kualitas' => $statusMutqin,
                'tanggal' => $m->tanggal_murajaah,  // Carbon
                'ustadz' => $ustadzMap[$m->ustadz_id] ?? '–',
                'catatan' => $m->catatan,
            ];
        });

        return $barisSetoran
            ->concat($barisMurajaah)
            ->sortByDesc('tanggal')
            ->values();
    }
}
