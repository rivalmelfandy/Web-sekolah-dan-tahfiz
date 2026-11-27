<?php

namespace App\Http\Controllers\Admin;

use App\Enums\NilaiIndeks;
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

        $halaqahOptions = Halaqah::orderBy('kode_halaqah')
            ->get(['id', 'kode_halaqah', 'nama_halaqah']);

        $siswaList = User::where('role', UserRole::Siswa)
            ->with(['halaqah:id,nama_halaqah,kode_halaqah,ustadz_id', 'halaqah.ustadz:id,nama_lengkap'])
            ->when($halaqahIdFilter, fn ($q) => $q->where('halaqah_id', $halaqahIdFilter))
            ->orderBy('nama_lengkap')
            ->get(['id', 'nip_nis', 'nama_lengkap', 'halaqah_id']);

        return view('admin.laporan-tahfiz.index', compact('halaqahOptions', 'halaqahIdFilter', 'siswaList'));
    }

    /**
     * Detail riwayat hafalan & murajaah satu siswa.
     */
    public function show(User $siswa): View
    {
        abort_if($siswa->role !== UserRole::Siswa, 404);

        $siswa->load(['halaqah:id,nama_halaqah,kode_halaqah,ustadz_id', 'halaqah.ustadz:id,nama_lengkap']);

        // ── Setoran hafalan siswa ────────────────────────────────────────
        $setoran = $siswa->setoranHafalan()
            ->orderByDesc('tanggal_setor')
            ->orderByDesc('id')
            ->get(['id', 'siswa_id', 'ustadz_id', 'surah', 'ayat_awal', 'ayat_akhir', 'nilai_hafalan', 'catatan', 'tanggal_setor']);

        // ── Murajaah siswa via setoran_hafalan (tidak ada siswa_id di murajaah) ──
        $setoranIds = $setoran->pluck('id');

        $murajaah = Murajaah::with('setoran:id,surah,ayat_awal,ayat_akhir')
            ->whereIn('setoran_id', $setoranIds)
            ->orderByDesc('tanggal_murajaah')
            ->orderByDesc('id')
            ->get(['id', 'setoran_id', 'ustadz_id', 'ayat_range', 'nilai_murajaah', 'catatan', 'tanggal_murajaah']);

        $aktivitas = $this->gabungkanAktivitas($setoran, $murajaah);
        $stat      = $this->hitungStat($setoran, $murajaah);

        return view('admin.laporan-tahfiz.show', compact('siswa', 'aktivitas', 'stat'));
    }

    // ── Helper ───────────────────────────────────────────────────────────

    private function gabungkanAktivitas(Collection $setoran, Collection $murajaah): Collection
    {
        $barisSetoran = $setoran->map(fn ($s) => [
            'id'         => $s->id,
            'kategori'   => 'Hafalan',
            'nama_surah' => $s->surah . ' (' . $s->ayat_awal . '–' . $s->ayat_akhir . ')',
            'tanggal'    => $s->tanggal_setor,
            'nilai'      => $s->nilai_hafalan,
            'indeks'     => NilaiIndeks::dariNilai($s->nilai_hafalan),
            'catatan'    => $s->catatan,
            'ayat_range' => null,
        ]);

        $barisMurajaah = $murajaah->map(fn ($m) => [
            'id'         => $m->id,
            'kategori'   => 'Murajaah',
            'nama_surah' => $m->setoran
                ? $m->setoran->surah . ' (' . $m->setoran->ayat_awal . '–' . $m->setoran->ayat_akhir . ')'
                : ($m->ayat_range ?? '–'),
            'tanggal'    => $m->tanggal_murajaah,
            'nilai'      => $m->nilai_murajaah,
            'indeks'     => NilaiIndeks::dariNilai($m->nilai_murajaah),
            'catatan'    => $m->catatan,
            'ayat_range' => $m->ayat_range,
        ]);

        return $barisSetoran->concat($barisMurajaah)->sortByDesc('tanggal')->values();
    }

    private function hitungStat(Collection $setoran, Collection $murajaah): array
    {
        $totalSetoran  = $setoran->count();
        $totalMurajaah = $murajaah->count();

        $semuaNilai = $setoran->pluck('nilai_hafalan')
            ->concat($murajaah->pluck('nilai_murajaah'))
            ->filter()
            ->map(fn ($v) => (int) $v);

        $rataRata = $semuaNilai->isNotEmpty() ? round($semuaNilai->average(), 1) : 0.0;
        $indeks   = NilaiIndeks::dariNilai($rataRata);

        return [
            'total_setoran'  => $totalSetoran,
            'total_murajaah' => $totalMurajaah,
            'rata_nilai'     => $rataRata,
            'indeks'         => $indeks,
        ];
    }
}
