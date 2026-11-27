<?php

namespace App\Http\Controllers\Admin;

use App\Enums\KualitasLevel;
use App\Enums\TargetStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Halaqah;
use App\Models\SetoranHafalan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RekapTahfidzController extends Controller
{
    /**
     * Halaman Rekap Tahfidz – data grafik bulanan & ringkasan siswa.
     *
     * Query params:
     *   ?halaqah_id=  – filter seluruh data ke satu halaqah (opsional)
     *
     * Skema SQL terbaru:
     *   setoran_hafalan : kualitas (enum Lancar|Kurang Lancar|Tidak Lancar) — tidak ada nilai_hafalan
     */
    public function index(Request $request): View
    {
        $halaqahIdFilter = $request->integer('halaqah_id') ?: null;

        // ── Daftar halaqah untuk dropdown filter ────────────────────────
        $halaqahOptions = Halaqah::orderBy('kode_halaqah')
            ->get(['id', 'kode_halaqah', 'nama_halaqah']);

        // ── Grafik bulanan: jumlah setoran per bulan ─────────────────────
        // Sumbu X : bulan-tahun (12 bulan terakhir)
        // Sumbu Y : total baris setoran_hafalan
        // Tidak lagi menggunakan AVG(nilai_hafalan) — diganti COUNT per kualitas.

        $grafikQuery = SetoranHafalan::select(
            DB::raw("DATE_FORMAT(tanggal_setor, '%Y-%m') AS bulan"),
            DB::raw('COUNT(*) AS jumlah_setoran'),
            DB::raw("SUM(CASE WHEN kualitas = 'Lancar' THEN 1 ELSE 0 END) AS jumlah_lancar"),
            DB::raw("SUM(CASE WHEN kualitas = 'Kurang Lancar' THEN 1 ELSE 0 END) AS jumlah_kurang"),
            DB::raw("SUM(CASE WHEN kualitas = 'Tidak Lancar' THEN 1 ELSE 0 END) AS jumlah_tidak")
        )
            ->when($halaqahIdFilter, function ($q) use ($halaqahIdFilter) {
                $q->whereHas('siswa', fn($s) => $s->where('halaqah_id', $halaqahIdFilter));
            })
            ->where('tanggal_setor', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $grafikBulanan = $this->fillMissingMonths($grafikQuery);

        // ── Data siswa ───────────────────────────────────────────────────
        // Kolom yang disiapkan per siswa:
        //   - nama_lengkap, nip_nis
        //   - nama halaqah (via relasi)
        //   - target aktif (status='Aktif', satu per siswa)
        //   - setoran terakhir: surah, ayat, kualitas, tanggal
        //   - distribusi kualitas: hitung Lancar/KurangLancar/TidakLancar

        $siswaQuery = User::where('role', UserRole::Siswa)
            ->with([
                'halaqah:id,nama_halaqah,kode_halaqah',
                // Target aktif terbaru (satu)
                'targetHafalan' => fn($q) => $q
                    ->where('status', TargetStatus::Aktif)
                    ->orderByDesc('tanggal_mulai')
                    ->limit(1)
                    ->select(['id', 'siswa_id', 'surah_awal', 'surah_akhir', 'tanggal_mulai', 'tanggal_akhir', 'status']),
                // Setoran terakhir — kolom kualitas, bukan nilai_hafalan
                'setoranHafalan' => fn($q) => $q
                    ->orderByDesc('tanggal_setor')
                    ->orderByDesc('id')
                    ->limit(1)
                    ->select(['id', 'siswa_id', 'surah', 'ayat_awal', 'ayat_akhir', 'kualitas', 'tanggal_setor']),
            ])
            ->when($halaqahIdFilter, fn($q) => $q->where('halaqah_id', $halaqahIdFilter))
            ->orderBy('nama_lengkap');

        // Ambil distribusi kualitas per siswa dalam satu query GROUP BY
        $distribusiPerSiswa = DB::table('setoran_hafalan')
            ->select('siswa_id', 'kualitas', DB::raw('COUNT(*) as jumlah'))
            ->when($halaqahIdFilter, function ($q) use ($halaqahIdFilter) {
                $q->whereIn('siswa_id', function ($sub) use ($halaqahIdFilter) {
                    $sub->select('id')->from('users')->where('halaqah_id', $halaqahIdFilter);
                });
            })
            ->groupBy('siswa_id', 'kualitas')
            ->get()
            ->groupBy('siswa_id');

        $siswaList = $siswaQuery->get()->map(function (User $siswa) use ($distribusiPerSiswa) {
            $setoranTerakhir = $siswa->setoranHafalan->first();
            $targetAktif = $siswa->targetHafalan->first();

            // Distribusi kualitas untuk siswa ini
            $dist = $distribusiPerSiswa->get($siswa->id, collect());
            $totalSetoran = $dist->sum('jumlah');
            $lancar = $dist->where('kualitas', KualitasLevel::Lancar->value)->sum('jumlah');
            $kurang = $dist->where('kualitas', KualitasLevel::KurangLancar->value)->sum('jumlah');
            $tidak = $dist->where('kualitas', KualitasLevel::TidakLancar->value)->sum('jumlah');
            $pctLancar = $totalSetoran > 0 ? round($lancar / $totalSetoran * 100) : 0;

            // Kualitas terakhir
            $kualitasTerakhir = null;
            if ($setoranTerakhir) {
                $kualitasTerakhir = $setoranTerakhir->kualitas instanceof KualitasLevel
                    ? $setoranTerakhir->kualitas->value
                    : (string) $setoranTerakhir->kualitas;
            }

            return [
                'id' => $siswa->id,
                'nama_lengkap' => $siswa->nama_lengkap,
                'nip_nis' => $siswa->nip_nis,
                'nama_halaqah' => $siswa->halaqah?->nama_halaqah ?? '–',
                'kode_halaqah' => $siswa->halaqah?->kode_halaqah ?? '–',
                // Target aktif
                'target_aktif' => $targetAktif ? [
                    'surah_awal' => $targetAktif->surah_awal,
                    'surah_akhir' => $targetAktif->surah_akhir,
                    'tanggal_mulai' => $targetAktif->tanggal_mulai?->format('d/m/Y'),
                    'tanggal_akhir' => $targetAktif->tanggal_akhir?->format('d/m/Y'),
                ] : null,
                // Hafalan terakhir — kualitas, bukan nilai
                'hafalan_terakhir' => $setoranTerakhir ? [
                    'surah' => $setoranTerakhir->surah,
                    'ayat_awal' => $setoranTerakhir->ayat_awal,
                    'ayat_akhir' => $setoranTerakhir->ayat_akhir,
                    'kualitas' => $kualitasTerakhir,
                    'tanggal' => $setoranTerakhir->tanggal_setor?->format('d/m/Y'),
                ] : null,
                // Distribusi kualitas
                'total_setoran' => $totalSetoran,
                'jumlah_lancar' => $lancar,
                'jumlah_kurang' => $kurang,
                'jumlah_tidak' => $tidak,
                'pct_lancar' => $pctLancar,
            ];
        });

        // ── Statistik ringkas untuk header view ──────────────────────────
        $totalSetoranGlobal = array_sum(array_column($grafikBulanan, 'jumlah_setoran'));
        $avgPctLancar = $siswaList->isNotEmpty()
            ? round($siswaList->avg('pct_lancar'), 1)
            : 0.0;

        $statTotal = [
            'siswa' => $siswaList->count(),
            'halaqah' => $halaqahOptions->count(),
            'setoran' => $totalSetoranGlobal,
            'pct_lancar' => $avgPctLancar,
        ];

        return view('admin.rekap-tahfidz', [
            'halaqahOptions' => $halaqahOptions,
            'halaqahIdFilter' => $halaqahIdFilter,
            'grafikBulanan' => $grafikBulanan,
            'grafikLabels' => array_column($grafikBulanan, 'label'),
            'grafikSetoran' => array_column($grafikBulanan, 'jumlah_setoran'),
            'grafikLancar' => array_column($grafikBulanan, 'jumlah_lancar'),
            'siswaList' => $siswaList,
            'statTotal' => $statTotal,
        ]);
    }

    // ── Helper ───────────────────────────────────────────────────────────

    /**
     * Isi celah bulan yang tidak ada datanya dengan 0.
     *
     * @return array<int, array{bulan:string, label:string, jumlah_setoran:int, jumlah_lancar:int, jumlah_kurang:int, jumlah_tidak:int}>
     */
    private function fillMissingMonths(Collection $rows): array
    {
        $indexed = $rows->keyBy('bulan');

        $result = [];
        for ($i = 11; $i >= 0; $i--) {
            $key = now()->subMonths($i)->format('Y-m');
            $label = now()->subMonths($i)->translatedFormat('M Y');
            $row = $indexed->get($key);

            $result[] = [
                'bulan' => $key,
                'label' => $label,
                'jumlah_setoran' => $row ? (int) $row->jumlah_setoran : 0,
                'jumlah_lancar' => $row ? (int) $row->jumlah_lancar : 0,
                'jumlah_kurang' => $row ? (int) $row->jumlah_kurang : 0,
                'jumlah_tidak' => $row ? (int) $row->jumlah_tidak : 0,
            ];
        }

        return $result;
    }
}
