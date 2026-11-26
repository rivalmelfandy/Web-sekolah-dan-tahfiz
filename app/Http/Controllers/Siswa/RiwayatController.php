<?php

namespace App\Http\Controllers\Siswa;

use App\Enums\NilaiIndeks;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RiwayatController extends Controller
{
    /**
     * Halaman riwayat hafalan siswa yang sedang login.
     *
     * Semua query menggunakan kolom yang benar-benar ada di database aktual:
     *   - setoran_hafalan : siswa_id, surah, ayat_awal, ayat_akhir, nilai, kualitas, tanggal_setor
     *   - murajaah        : siswa_id, surah, ayat_range, status_mutqin, tanggal_murajaah
     *                       (TIDAK ada setoran_id, nilai_murajaah, atau relasi FK ke setoran_hafalan)
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // ── Ambil semua setoran hafalan milik siswa ini ───────────────
        $semuaSetoranRaw = DB::table('setoran_hafalan')
            ->where('siswa_id', $user->id)
            ->orderByDesc('tanggal_setor')
            ->orderByDesc('id')
            ->get(['id', 'surah', 'ayat_awal', 'ayat_akhir', 'nilai', 'kualitas', 'catatan', 'tanggal_setor', 'ustadz_id']);

        // ── Ambil semua murajaah milik siswa ini langsung via siswa_id ─
        // DB aktual: murajaah.siswa_id — tidak ada kolom setoran_id maupun catatan
        $semuaMurajaahRaw = DB::table('murajaah')
            ->where('siswa_id', $user->id)
            ->orderByDesc('tanggal_murajaah')
            ->orderByDesc('id')
            ->get(['id', 'surah', 'ayat_range', 'status_mutqin', 'tanggal_murajaah', 'ustadz_id']);

        // ── Kumpulkan semua ustadz_id untuk satu kali query ───────────
        $ustadzIds = $semuaSetoranRaw->pluck('ustadz_id')
            ->concat($semuaMurajaahRaw->pluck('ustadz_id'))
            ->filter()
            ->unique()
            ->values();
        $ustadzMap = DB::table('users')
            ->whereIn('id', $ustadzIds)
            ->pluck('nama_lengkap', 'id');

        // ── Statistik ─────────────────────────────────────────────────
        $totalSetoran = $semuaSetoranRaw->count();
        $totalMurajaah = $semuaMurajaahRaw->count();

        // Rata-rata gabungan: nilai numerik dari setoran + estimasi dari status_mutqin
        $nilaiSetoran = $semuaSetoranRaw->pluck('nilai')->map(fn($v) => (int) $v);
        $nilaiMurajaah = $semuaMurajaahRaw->pluck('status_mutqin')
            ->map(fn($s) => $this->statusMutqinKeAngka($s));
        $semuaNilai = $nilaiSetoran->concat($nilaiMurajaah)->filter(fn($v) => $v !== null);

        $rataNilai = $semuaNilai->isNotEmpty()
            ? round($semuaNilai->average(), 1)
            : null;
        $predikatRata = $rataNilai !== null
            ? NilaiIndeks::dariNilai($rataNilai)
            : null;

        // ── Gabungkan ke satu koleksi aktivitas ───────────────────────
        $aktivitas = $this->gabungkanAktivitas($semuaSetoranRaw, $semuaMurajaahRaw, $ustadzMap);

        // ── Filter kategori ───────────────────────────────────────────
        $filterKategori = $request->input('kategori', 'semua');
        if ($filterKategori !== 'semua') {
            $aktivitas = $aktivitas->filter(
                fn($item) => strtolower($item['kategori']) === strtolower($filterKategori)
            )->values();
        }

        // ── Filter surah ──────────────────────────────────────────────
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
            'rataNilai',
            'predikatRata',
            'filterKategori',
            'filterSurah',
        ));
    }

    // ── Helper ────────────────────────────────────────────────────────

    /**
     * Konversi status_mutqin (varchar lama) ke angka 0–100 agar bisa
     * dipakai NilaiIndeks. Mapping disesuaikan dengan nilai yang wajar
     * untuk tiap label kualitatif.
     */
    private function statusMutqinKeAngka(?string $status): ?int
    {
        if ($status === null)
            return null;
        return match (strtolower(trim($status))) {
            'mutqin', 'sangat baik', 'a' => 90,
            'baik sekali', 'ab' => 85,
            'baik', 'b' => 75,
            'cukup baik', 'bc' => 65,
            'cukup', 'c' => 55,
            'kurang', 'd' => 45,
            'sangat kurang', 'e', 'tidak mutqin' => 30,
            default => null,
        };
    }

    /**
     * Gabungkan koleksi setoran dan murajaah mentah (stdClass dari DB::table)
     * ke satu Collection terurut dari tanggal terbaru.
     */
    private function gabungkanAktivitas(
        Collection $setoran,
        Collection $murajaah,
        Collection $ustadzMap
    ): Collection {
        $barisSetoran = $setoran->map(fn($s) => [
            'id' => $s->id,
            'kategori' => 'Hafalan',
            'nama_surah' => $s->surah,
            'ayat_awal' => $s->ayat_awal,
            'ayat_akhir' => $s->ayat_akhir,
            'nilai' => (int) $s->nilai,
            'nilai_terkini' => (int) $s->nilai,
            'kualitas_terkini' => NilaiIndeks::dariNilai((int) $s->nilai),
            'tanggal' => \Carbon\Carbon::parse($s->tanggal_setor),
            'ustadz' => $ustadzMap[$s->ustadz_id] ?? '–',
            'catatan' => $s->catatan,
            'ada_murajaah' => false,
        ]);

        $barisMurajaah = $murajaah->map(function ($m) use ($ustadzMap) {
            $nilaiAngka = $this->statusMutqinKeAngka($m->status_mutqin);
            return [
                'id' => $m->id,
                'kategori' => 'Murajaah',
                'nama_surah' => $m->surah ?: ($m->ayat_range ?? '–'),
                'ayat_awal' => null,
                'ayat_akhir' => null,
                'nilai' => $m->status_mutqin ?? '–',  // tampilkan label asli
                'nilai_terkini' => $nilaiAngka,
                'kualitas_terkini' => $nilaiAngka !== null
                    ? NilaiIndeks::dariNilai($nilaiAngka)
                    : null,
                'tanggal' => \Carbon\Carbon::parse($m->tanggal_murajaah),
                'ustadz' => $ustadzMap[$m->ustadz_id] ?? '–',
                'catatan' => null,   // kolom catatan tidak ada di tabel murajaah
                'ada_murajaah' => false,
            ];
        });

        return $barisSetoran
            ->concat($barisMurajaah)
            ->sortByDesc('tanggal')
            ->values();
    }
}
