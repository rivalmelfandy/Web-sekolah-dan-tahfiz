<?php

namespace App\Http\Controllers\Admin;

use App\Enums\NilaiIndeks;
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
     * Skema DBML terkini:
     *   setoran_hafalan : nilai_hafalan (tinyint 0-100, default 80)
     */
    public function index(Request $request): View
    {
        $halaqahIdFilter = $request->integer('halaqah_id') ?: null;

        // ── Daftar halaqah untuk dropdown filter ────────────────────────
        $halaqahOptions = Halaqah::orderBy('kode_halaqah')
            ->get(['id', 'kode_halaqah', 'nama_halaqah']);

        // ── Grafik bulanan: jumlah setoran & rata-rata nilai per bulan ───
        $grafikQuery = SetoranHafalan::select(
            DB::raw("DATE_FORMAT(tanggal_setor, '%Y-%m') AS bulan"),
            DB::raw('COUNT(*) AS jumlah_setoran'),
            DB::raw('ROUND(AVG(nilai_hafalan), 1) AS rata_nilai'),
            DB::raw('SUM(CASE WHEN nilai_hafalan >= 90 THEN 1 ELSE 0 END) AS jumlah_a'),
            DB::raw('SUM(CASE WHEN nilai_hafalan >= 80 AND nilai_hafalan < 90 THEN 1 ELSE 0 END) AS jumlah_ab'),
            DB::raw('SUM(CASE WHEN nilai_hafalan < 80 THEN 1 ELSE 0 END) AS jumlah_bawah')
        )
            ->when($halaqahIdFilter, function ($q) use ($halaqahIdFilter) {
                $q->whereHas('siswa', fn ($s) => $s->where('halaqah_id', $halaqahIdFilter));
            })
            ->where('tanggal_setor', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $grafikBulanan = $this->fillMissingMonths($grafikQuery);

        // ── Data siswa ───────────────────────────────────────────────────
        $siswaQuery = User::where('role', UserRole::Siswa)
            ->with([
                'halaqah:id,nama_halaqah,kode_halaqah',
                'targetHafalan' => fn ($q) => $q
                    ->where('status', TargetStatus::Aktif)
                    ->orderByDesc('tanggal_mulai')
                    ->limit(1)
                    ->select(['id', 'siswa_id', 'surah_awal', 'surah_akhir', 'tanggal_mulai', 'tanggal_akhir', 'status']),
                'setoranHafalan' => fn ($q) => $q
                    ->orderByDesc('tanggal_setor')
                    ->orderByDesc('id')
                    ->limit(1)
                    ->select(['id', 'siswa_id', 'surah', 'ayat_awal', 'ayat_akhir', 'nilai_hafalan', 'tanggal_setor']),
            ])
            ->withAvg('setoranHafalan', 'nilai_hafalan')
            ->when($halaqahIdFilter, fn ($q) => $q->where('halaqah_id', $halaqahIdFilter))
            ->orderBy('nama_lengkap');

        $siswaList = $siswaQuery->get()->map(function (User $siswa) {
            $setoranTerakhir = $siswa->setoranHafalan->first();
            $targetAktif     = $siswa->targetHafalan->first();
            $rataRata        = round((float) ($siswa->setoran_hafalan_avg_nilai_hafalan ?? 0), 1);
            $indeks          = NilaiIndeks::dariNilai($rataRata);

            return [
                'id'           => $siswa->id,
                'nama_lengkap' => $siswa->nama_lengkap,
                'nip_nis'      => $siswa->nip_nis,
                'nama_halaqah' => $siswa->halaqah?->nama_halaqah ?? '–',
                'kode_halaqah' => $siswa->halaqah?->kode_halaqah ?? '–',
                'target_aktif' => $targetAktif ? [
                    'surah_awal'    => $targetAktif->surah_awal,
                    'surah_akhir'   => $targetAktif->surah_akhir,
                    'tanggal_mulai' => $targetAktif->tanggal_mulai?->format('d/m/Y'),
                    'tanggal_akhir' => $targetAktif->tanggal_akhir?->format('d/m/Y'),
                ] : null,
                'hafalan_terakhir' => $setoranTerakhir ? [
                    'surah'    => $setoranTerakhir->surah,
                    'ayat_awal'  => $setoranTerakhir->ayat_awal,
                    'ayat_akhir' => $setoranTerakhir->ayat_akhir,
                    'nilai'      => $setoranTerakhir->nilai_hafalan,
                    'indeks'     => NilaiIndeks::dariNilai($setoranTerakhir->nilai_hafalan)->value,
                    'tanggal'    => $setoranTerakhir->tanggal_setor?->format('d/m/Y'),
                ] : null,
                'rata_nilai'    => $rataRata,
                'indeks'        => $indeks->value,
                'persen_capaian' => $rataRata,
            ];
        });

        $totalSetoranGlobal = array_sum(array_column($grafikBulanan, 'jumlah_setoran'));
        $avgNilai = $siswaList->isNotEmpty()
            ? round($siswaList->avg('rata_nilai'), 1)
            : 0.0;

        $statTotal = [
            'siswa'    => $siswaList->count(),
            'halaqah'  => $halaqahOptions->count(),
            'setoran'  => $totalSetoranGlobal,
            'rata_nilai' => $avgNilai,
        ];

        return view('admin.rekap-tahfidz', [
            'halaqahOptions'  => $halaqahOptions,
            'halaqahIdFilter' => $halaqahIdFilter,
            'grafikBulanan'   => $grafikBulanan,
            'grafikLabels'    => array_column($grafikBulanan, 'label'),
            'grafikSetoran'   => array_column($grafikBulanan, 'jumlah_setoran'),
            'siswaList'       => $siswaList,
            'statTotal'       => $statTotal,
        ]);
    }

    /**
     * @return array<int, array{bulan:string, label:string, jumlah_setoran:int, rata_nilai:float}>
     */
    private function fillMissingMonths(Collection $rows): array
    {
        $indexed = $rows->keyBy('bulan');
        $result  = [];

        for ($i = 11; $i >= 0; $i--) {
            $key   = now()->subMonths($i)->format('Y-m');
            $label = now()->subMonths($i)->translatedFormat('M Y');
            $row   = $indexed->get($key);

            $result[] = [
                'bulan'          => $key,
                'label'          => $label,
                'jumlah_setoran' => $row ? (int) $row->jumlah_setoran : 0,
                'rata_nilai'     => $row ? (float) $row->rata_nilai   : 0.0,
            ];
        }

        return $result;
    }
}
