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
     * Skema DBML terkini:
     *   setoran_hafalan : nilai_hafalan (tinyint default 80)
     *   murajaah        : setoran_id FK, nilai_murajaah (tinyint default 80)
     *                     (tidak ada siswa_id langsung di murajaah)
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $semuaSetoran = SetoranHafalan::where('siswa_id', $user->id)
            ->orderByDesc('tanggal_setor')
            ->orderByDesc('id')
            ->get(['id', 'surah', 'ayat_awal', 'ayat_akhir', 'nilai_hafalan', 'catatan', 'tanggal_setor', 'ustadz_id']);

        $setoranIds = $semuaSetoran->pluck('id');

        $semuaMurajaah = Murajaah::with('setoran:id,surah,ayat_awal,ayat_akhir')
            ->whereIn('setoran_id', $setoranIds)
            ->orderByDesc('tanggal_murajaah')
            ->orderByDesc('id')
            ->get(['id', 'setoran_id', 'ustadz_id', 'ayat_range', 'nilai_murajaah', 'catatan', 'tanggal_murajaah']);

        $ustadzIds = $semuaSetoran->pluck('ustadz_id')
            ->concat($semuaMurajaah->pluck('ustadz_id'))
            ->filter()->unique()->values();

        $ustadzMap = DB::table('users')
            ->whereIn('id', $ustadzIds)
            ->pluck('nama_lengkap', 'id');

        $totalSetoran  = $semuaSetoran->count();
        $totalMurajaah = $semuaMurajaah->count();

        // Distribusi indeks berdasarkan nilai_hafalan
        $distribusiKualitas = $semuaSetoran
            ->groupBy(fn ($s) => NilaiIndeks::dariNilai($s->nilai_hafalan)->value)
            ->map->count();

        $semuaNilai = $semuaSetoran->pluck('nilai_hafalan')
            ->concat($semuaMurajaah->pluck('nilai_murajaah'))
            ->filter()->map(fn ($v) => (int) $v);
        $rataNilai    = $semuaNilai->isNotEmpty() ? round($semuaNilai->average(), 1) : null;
        $predikatRata = $rataNilai !== null ? NilaiIndeks::dariNilai($rataNilai) : null;

        $aktivitas = $this->gabungkanAktivitas($semuaSetoran, $semuaMurajaah, $ustadzMap);

        $filterKategori = $request->input('kategori', 'semua');
        if ($filterKategori !== 'semua') {
            $aktivitas = $aktivitas->filter(
                fn ($item) => strtolower($item['kategori']) === strtolower($filterKategori)
            )->values();
        }

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
            'distribusiKualitas',
            'rataNilai',
            'predikatRata',
            'filterKategori',
            'filterSurah',
        ));
    }

    private function gabungkanAktivitas(
        Collection $setoran,
        Collection $murajaah,
        Collection $ustadzMap
    ): Collection {
        $barisSetoran = $setoran->map(fn ($s) => [
            'id'            => $s->id,
            'kategori'      => 'Hafalan',
            'nama_surah'    => $s->surah . ' (' . $s->ayat_awal . '–' . $s->ayat_akhir . ')',
            'ayat_awal'     => $s->ayat_awal,
            'ayat_akhir'    => $s->ayat_akhir,
            'nilai'         => $s->nilai_hafalan,
            'kualitas_terkini' => NilaiIndeks::dariNilai($s->nilai_hafalan),
            'tanggal'       => $s->tanggal_setor,
            'ustadz'        => $ustadzMap[$s->ustadz_id] ?? '–',
            'catatan'       => $s->catatan,
        ]);

        $barisMurajaah = $murajaah->map(fn ($m) => [
            'id'            => $m->id,
            'kategori'      => 'Murajaah',
            'nama_surah'    => $m->setoran
                ? $m->setoran->surah . ' (' . $m->setoran->ayat_awal . '–' . $m->setoran->ayat_akhir . ')'
                : ($m->ayat_range ?? '–'),
            'ayat_awal'     => null,
            'ayat_akhir'    => null,
            'nilai'         => $m->nilai_murajaah,
            'kualitas_terkini' => NilaiIndeks::dariNilai($m->nilai_murajaah),
            'tanggal'       => $m->tanggal_murajaah,
            'ustadz'        => $ustadzMap[$m->ustadz_id] ?? '–',
            'catatan'       => $m->catatan,
        ]);

        return $barisSetoran->concat($barisMurajaah)->sortByDesc('tanggal')->values();
    }
}
