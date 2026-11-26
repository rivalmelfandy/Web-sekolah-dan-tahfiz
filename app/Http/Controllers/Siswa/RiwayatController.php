<?php

namespace App\Http\Controllers\Siswa;

use App\Enums\NilaiIndeks;
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
     * Skema aktual (docs/Project tahfiz_yuk.dbml):
     *   setoran_hafalan : siswa_id, surah, ayat_awal, ayat_akhir, nilai_hafalan, catatan, tanggal_setor, ustadz_id
     *   murajaah        : setoran_id → setoran_hafalan.id, nilai_murajaah, ayat_range, catatan, tanggal_murajaah, ustadz_id
     *                     (TIDAK ada siswa_id langsung di murajaah)
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // ── Ambil semua setoran hafalan milik siswa ───────────────────
        $semuaSetoran = SetoranHafalan::where('siswa_id', $user->id)
            ->orderByDesc('tanggal_setor')
            ->orderByDesc('id')
            ->get(['id', 'surah', 'ayat_awal', 'ayat_akhir', 'nilai_hafalan', 'catatan', 'tanggal_setor', 'ustadz_id']);

        // ── Ambil semua murajaah milik siswa via setoran_hafalan ──────
        $setoranIds = $semuaSetoran->pluck('id');

        $semuaMurajaah = Murajaah::with('setoran:id,surah,ayat_awal,ayat_akhir')
            ->whereIn('setoran_id', $setoranIds)
            ->orderByDesc('tanggal_murajaah')
            ->orderByDesc('id')
            ->get(['id', 'setoran_id', 'ustadz_id', 'ayat_range', 'nilai_murajaah', 'catatan', 'tanggal_murajaah']);

        // ── Satu kali query nama ustadz ───────────────────────────────
        $ustadzIds = $semuaSetoran->pluck('ustadz_id')
            ->concat($semuaMurajaah->pluck('ustadz_id'))
            ->filter()
            ->unique()
            ->values();

        $ustadzMap = DB::table('users')
            ->whereIn('id', $ustadzIds)
            ->pluck('nama_lengkap', 'id');

        // ── Statistik ─────────────────────────────────────────────────
        $totalSetoran = $semuaSetoran->count();
        $totalMurajaah = $semuaMurajaah->count();

        // Rata-rata gabungan: nilai_hafalan dari setoran + nilai_murajaah dari murajaah
        $semuaNilai = $semuaSetoran->pluck('nilai_hafalan')
            ->concat($semuaMurajaah->pluck('nilai_murajaah'))
            ->filter(fn ($v) => $v !== null)
            ->map(fn ($v) => (int) $v);

        $rataNilai = $semuaNilai->isNotEmpty() ? round($semuaNilai->average(), 1) : null;
        $predikatRata = $rataNilai !== null ? NilaiIndeks::dariNilai($rataNilai) : null;

        // ── Gabungkan ke satu koleksi aktivitas terurut ───────────────
        $aktivitas = $this->gabungkanAktivitas($semuaSetoran, $semuaMurajaah, $ustadzMap);

        // ── Filter kategori ───────────────────────────────────────────
        $filterKategori = $request->input('kategori', 'semua');
        if ($filterKategori !== 'semua') {
            $aktivitas = $aktivitas->filter(
                fn ($item) => strtolower($item['kategori']) === strtolower($filterKategori)
            )->values();
        }

        // ── Filter surah ──────────────────────────────────────────────
        $filterSurah = trim($request->input('surah', ''));
        if ($filterSurah !== '') {
            $aktivitas = $aktivitas->filter(
                fn ($item) => str_contains(
                    mb_strtolower($item['nama_surah']),
                    mb_strtolower($filterSurah)
                )
            )->values();
        }

        return view('siswa.riwayat', compact(
            'aktivitas',
            'totalSetoran',
            'totalMurajaah',
            'rataNilai',
            'predikatRata',
            'filterKategori',
            'filterSurah',
        ));
    }

    // ── Helper ────────────────────────────────────────────────────────

    /**
     * Gabungkan Collection setoran (Eloquent) dan murajaah (Eloquent) ke satu
     * Collection terurut dari tanggal terbaru.
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
        $barisSetoran = $setoran->map(fn ($s) => [
            'id' => $s->id,
            'kategori' => 'Hafalan',
            'nama_surah' => $s->surah.' ('.$s->ayat_awal.'–'.$s->ayat_akhir.')',
            'ayat_awal' => $s->ayat_awal,
            'ayat_akhir' => $s->ayat_akhir,
            'nilai' => $s->nilai_hafalan,
            'nilai_terkini' => $s->nilai_hafalan,
            'kualitas_terkini' => NilaiIndeks::dariNilai($s->nilai_hafalan),
            'tanggal' => $s->tanggal_setor,  // Carbon (di-cast model)
            'ustadz' => $ustadzMap[$s->ustadz_id] ?? '–',
            'catatan' => $s->catatan,
        ]);

        $barisMurajaah = $murajaah->map(fn ($m) => [
            'id' => $m->id,
            'kategori' => 'Murajaah',
            'nama_surah' => $m->setoran
                ? $m->setoran->surah.' ('.$m->setoran->ayat_awal.'–'.$m->setoran->ayat_akhir.')'
                : ($m->ayat_range ?? '–'),
            'ayat_awal' => null,
            'ayat_akhir' => null,
            'nilai' => $m->nilai_murajaah,
            'nilai_terkini' => $m->nilai_murajaah,
            'kualitas_terkini' => NilaiIndeks::dariNilai($m->nilai_murajaah),
            'tanggal' => $m->tanggal_murajaah,  // Carbon (di-cast model)
            'ustadz' => $ustadzMap[$m->ustadz_id] ?? '–',
            'catatan' => $m->catatan,
        ]);

        return $barisSetoran
            ->concat($barisMurajaah)
            ->sortByDesc('tanggal')
            ->values();
    }
}
