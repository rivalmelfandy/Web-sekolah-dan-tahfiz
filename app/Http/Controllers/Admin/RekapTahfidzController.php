<?php

namespace App\Http\Controllers\Admin;

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
     */
    public function index(Request $request): View
    {
        $halaqahIdFilter = $request->integer('halaqah_id') ?: null;

        // ── Daftar halaqah untuk dropdown filter ────────────────────────
        $halaqahOptions = Halaqah::orderBy('kode_halaqah')
            ->get(['id', 'kode_halaqah', 'nama_halaqah']);

        // ── Grafik bulanan: jumlah setoran per bulan ─────────────────────
        //
        // Sumbu X : bulan-tahun dari tanggal_setor (12 bulan terakhir)
        // Sumbu Y : total baris setoran_hafalan (satu baris = satu sesi setor ke ustadz)
        // Filter  : jika halaqah_id dipilih, hanya ambil setoran siswa di halaqah itu.
        //
        // Strategi: satu query GROUP BY bulan, hasilkan array terindeks agar
        // view bisa langsung di-json_encode ke Chart.js tanpa logika di Blade.

        $grafikQuery = SetoranHafalan::select(
            DB::raw("DATE_FORMAT(tanggal_setor, '%Y-%m') AS bulan"),
            DB::raw('COUNT(*) AS jumlah_setoran'),
            DB::raw('AVG(nilai_hafalan) AS rata_nilai')
        )
            ->when($halaqahIdFilter, function ($q) use ($halaqahIdFilter) {
                // Batasi ke siswa yang terdaftar di halaqah yang dipilih.
                $q->whereHas('siswa', fn ($s) => $s->where('halaqah_id', $halaqahIdFilter));
            })
            ->where('tanggal_setor', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // Isi bulan yang tidak ada setoran dengan 0 agar grafik tidak loncat.
        $grafikBulanan = $this->fillMissingMonths($grafikQuery);

        // ── Data siswa ───────────────────────────────────────────────────
        //
        // Kolom yang disiapkan per siswa:
        //   - nama_lengkap, nip_nis
        //   - nama halaqah (via relasi)
        //   - target aktif (status='Aktif', satu per siswa)
        //   - setoran terakhir: surah, tanggal_setor, nilai_hafalan
        //   - persentase capaian = rata-rata nilai_hafalan seluruh setoran siswa itu
        //
        // Eager load: halaqah, targetAktif, setoranTerakhir.
        // withAvg untuk capaian agar tidak ada N+1.

        $siswaQuery = User::where('role', UserRole::Siswa)
            ->with([
                'halaqah:id,nama_halaqah,kode_halaqah',
                // Target aktif terbaru (satu)
                'targetHafalan' => fn ($q) => $q
                    ->where('status', TargetStatus::Aktif)
                    ->orderByDesc('tanggal_mulai')
                    ->limit(1)
                    ->select(['id', 'siswa_id', 'surah_awal', 'surah_akhir', 'tanggal_mulai', 'tanggal_akhir', 'status']),
                // Setoran terakhir
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
            $targetAktif = $siswa->targetHafalan->first();
            $avgNilai = round($siswa->setoran_hafalan_avg_nilai_hafalan ?? 0, 1);

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
                // Hafalan terakhir
                'hafalan_terakhir' => $setoranTerakhir ? [
                    'surah' => $setoranTerakhir->surah,
                    'ayat_awal' => $setoranTerakhir->ayat_awal,
                    'ayat_akhir' => $setoranTerakhir->ayat_akhir,
                    'nilai' => $setoranTerakhir->nilai_hafalan,
                    'tanggal' => $setoranTerakhir->tanggal_setor?->format('d/m/Y'),
                ] : null,
                // Capaian rata-rata 0–100
                'persen_capaian' => $avgNilai,
            ];
        });

        // ── Statistik ringkas untuk header view (opsional) ───────────────

        $statTotal = [
            'siswa' => $siswaList->count(),
            'halaqah' => $halaqahOptions->count(),
            'setoran' => array_sum(array_column($grafikBulanan, 'jumlah_setoran')),
            'rata_nilai' => $siswaList->isNotEmpty()
                ? round($siswaList->avg('persen_capaian'), 1)
                : 0.0,
        ];

        return view('admin.rekap-tahfidz', [
            'halaqahOptions' => $halaqahOptions,
            'halaqahIdFilter' => $halaqahIdFilter,
            'grafikBulanan' => $grafikBulanan,
            'grafikLabels' => array_column($grafikBulanan, 'label'),
            'grafikSetoran' => array_column($grafikBulanan, 'jumlah_setoran'),
            'grafikRataNilai' => array_column($grafikBulanan, 'rata_nilai'),
            'siswaList' => $siswaList,
            'statTotal' => $statTotal,
        ]);
    }

    // ── Helper ───────────────────────────────────────────────────────────

    /**
     * Isi celah bulan yang tidak ada datanya dengan 0 sehingga grafik mulus.
     *
     * @return array<int, array{bulan:string, label:string, jumlah_setoran:int, rata_nilai:float}>
     */
    private function fillMissingMonths(Collection $rows): array
    {
        // Indeks hasil DB: ['2026-08' => row, ...]
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
                'rata_nilai' => $row ? round((float) $row->rata_nilai, 1) : 0.0,
            ];
        }

        return $result;
    }
}
