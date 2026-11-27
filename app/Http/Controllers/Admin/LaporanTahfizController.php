<?php

namespace App\Http\Controllers\Admin;

use App\Enums\KualitasLevel;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Halaqah;
use App\Models\Murajaah;
use App\Models\SetoranHafalan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class LaporanTahfizController extends Controller
{
    /**
     * Daftar siswa dengan filter opsional per halaqah.
     */
    public function index(Request $request): View
    {
        $halaqahIdFilter = $request->integer('halaqah_id') ?: null;

        // Dropdown filter: semua halaqah
        $halaqahOptions = Halaqah::orderBy('kode_halaqah')
            ->get(['id', 'kode_halaqah', 'nama_halaqah']);

        // Daftar siswa — eager load halaqah + ustadz pembimbing halaqah
        $siswaList = User::where('role', UserRole::Siswa)
            ->with(['halaqah:id,nama_halaqah,kode_halaqah,ustadz_id', 'halaqah.ustadz:id,nama_lengkap'])
            ->when($halaqahIdFilter, fn($q) => $q->where('halaqah_id', $halaqahIdFilter))
            ->orderBy('nama_lengkap')
            ->get(['id', 'nip_nis', 'nama_lengkap', 'halaqah_id']);

        return view('admin.laporan-tahfiz.index', compact('halaqahOptions', 'halaqahIdFilter', 'siswaList'));
    }

    /**
     * Detail riwayat hafalan & murajaah satu siswa.
     *
     * @param  User  $siswa  Route model binding via {siswa}
     */
    public function show(User $siswa): View
    {
        // Pastikan yang ditampilkan benar-benar siswa.
        abort_if($siswa->role !== UserRole::Siswa, 404);

        $siswa->load(['halaqah:id,nama_halaqah,kode_halaqah,ustadz_id', 'halaqah.ustadz:id,nama_lengkap']);

        // ── Setoran hafalan siswa ────────────────────────────────────────
        // Kolom kualitas (enum: Lancar|Kurang Lancar|Tidak Lancar); tidak ada nilai_hafalan.
        $setoran = $siswa->setoranHafalan()
            ->orderByDesc('tanggal_setor')
            ->orderByDesc('id')
            ->get(['id', 'siswa_id', 'ustadz_id', 'surah', 'ayat_awal', 'ayat_akhir', 'kualitas', 'catatan', 'tanggal_setor']);

        // ── Murajaah siswa (murajaah punya siswa_id langsung) ───────────
        $murajaah = Murajaah::where('siswa_id', $siswa->id)
            ->orderByDesc('tanggal_murajaah')
            ->orderByDesc('id')
            ->get(['id', 'setoran_id', 'siswa_id', 'ustadz_id', 'surah', 'ayat_range', 'status_mutqin', 'catatan', 'tanggal_murajaah']);

        // ── Gabungkan ke satu koleksi terurut tanggal terbaru ────────────
        $aktivitas = $this->gabungkanAktivitas($setoran, $murajaah);

        // ── Statistik ringkas ────────────────────────────────────────────
        $stat = $this->hitungStat($setoran, $murajaah);

        return view('admin.laporan-tahfiz.show', compact('siswa', 'aktivitas', 'stat'));
    }

    // ── Helper ───────────────────────────────────────────────────────────

    /**
     * Gabungkan koleksi setoran dan murajaah ke satu Collection terurut terbaru.
     * Tiap item memuat: nama_surah, tanggal, kategori, kualitas (KualitasLevel), catatan.
     *
     * @param  Collection<int, SetoranHafalan>  $setoran
     * @param  Collection<int, Murajaah>  $murajaah
     * @return Collection<int, array<string, mixed>>
     */
    private function gabungkanAktivitas(Collection $setoran, Collection $murajaah): Collection
    {
        $barisSetoran = $setoran->map(function ($s) {
            $kualitas = $s->kualitas instanceof KualitasLevel
                ? $s->kualitas
                : KualitasLevel::tryFrom((string) $s->kualitas);

            return [
                'id' => $s->id,
                'kategori' => 'Hafalan',
                'nama_surah' => $s->surah . ' (' . $s->ayat_awal . '–' . $s->ayat_akhir . ')',
                'tanggal' => $s->tanggal_setor,  // Carbon (di-cast model)
                'kualitas' => $kualitas,
                'catatan' => $s->catatan,
                'ayat_range' => null,
            ];
        });

        $barisMurajaah = $murajaah->map(function ($m) {
            $kualitas = $m->status_mutqin instanceof KualitasLevel
                ? $m->status_mutqin
                : KualitasLevel::tryFrom((string) $m->status_mutqin);

            return [
                'id' => $m->id,
                'kategori' => 'Murajaah',
                'nama_surah' => ($m->surah ?? '') . ' (' . ($m->ayat_range ?? '–') . ')',
                'tanggal' => $m->tanggal_murajaah,  // Carbon (di-cast model)
                'kualitas' => $kualitas,
                'catatan' => $m->catatan,
                'ayat_range' => $m->ayat_range,
            ];
        });

        return $barisSetoran
            ->concat($barisMurajaah)
            ->sortByDesc('tanggal')
            ->values();
    }

    /**
     * Hitung statistik ringkas: total setoran, total murajaah,
     * dan distribusi kualitas (Lancar / Kurang Lancar / Tidak Lancar).
     *
     * @param  Collection<int, SetoranHafalan>  $setoran
     * @param  Collection<int, Murajaah>  $murajaah
     * @return array<string, mixed>
     */
    private function hitungStat(Collection $setoran, Collection $murajaah): array
    {
        $totalSetoran = $setoran->count();
        $totalMurajaah = $murajaah->count();

        // Distribusi kualitas setoran
        $distribusiSetoran = collect([
            KualitasLevel::Lancar->value => 0,
            KualitasLevel::KurangLancar->value => 0,
            KualitasLevel::TidakLancar->value => 0,
        ]);

        foreach ($setoran as $s) {
            $val = $s->kualitas instanceof KualitasLevel
                ? $s->kualitas->value
                : (string) $s->kualitas;
            if ($distribusiSetoran->has($val)) {
                $distribusiSetoran[$val]++;
            }
        }

        // Distribusi status_mutqin murajaah
        $distribusiMurajaah = collect([
            KualitasLevel::Lancar->value => 0,
            KualitasLevel::KurangLancar->value => 0,
            KualitasLevel::TidakLancar->value => 0,
        ]);

        foreach ($murajaah as $m) {
            $val = $m->status_mutqin instanceof KualitasLevel
                ? $m->status_mutqin->value
                : (string) $m->status_mutqin;
            if ($distribusiMurajaah->has($val)) {
                $distribusiMurajaah[$val]++;
            }
        }

        // Persen lancar setoran (untuk ringkasan)
        $pctLancarSetoran = $totalSetoran > 0
            ? round($distribusiSetoran[KualitasLevel::Lancar->value] / $totalSetoran * 100, 1)
            : 0.0;

        return [
            'total_setoran' => $totalSetoran,
            'total_murajaah' => $totalMurajaah,
            'distribusi_setoran' => $distribusiSetoran,
            'distribusi_murajaah' => $distribusiMurajaah,
            'pct_lancar_setoran' => $pctLancarSetoran,
        ];
    }
}
