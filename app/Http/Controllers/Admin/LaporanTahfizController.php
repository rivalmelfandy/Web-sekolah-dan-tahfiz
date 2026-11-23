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

        // Dropdown filter: semua halaqah
        $halaqahOptions = Halaqah::orderBy('kode_halaqah')
            ->get(['id', 'kode_halaqah', 'nama_halaqah']);

        // Daftar siswa — eager load halaqah + ustadz pembimbing halaqah
        $siswaList = User::where('role', UserRole::Siswa)
            ->with(['halaqah:id,nama_halaqah,kode_halaqah,ustadz_id', 'halaqah.ustadz:id,nama_lengkap'])
            ->when($halaqahIdFilter, fn ($q) => $q->where('halaqah_id', $halaqahIdFilter))
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
        $setoran = $siswa->setoranHafalan()
            ->orderByDesc('tanggal_setor')
            ->orderByDesc('id')
            ->get(['id', 'siswa_id', 'surah', 'ayat_awal', 'ayat_akhir', 'nilai_hafalan', 'tanggal_setor', 'catatan']);

        // ── Murajaah siswa (lewat setoran yang dimilikinya) ──────────────
        // Murajaah tidak langsung berelasi ke siswa; harus melalui setoran_hafalan.
        $setoranIds = $setoran->pluck('id');

        $murajaah = Murajaah::whereIn('setoran_id', $setoranIds)
            ->with('setoran:id,surah,ayat_awal,ayat_akhir')
            ->orderByDesc('tanggal_murajaah')
            ->orderByDesc('id')
            ->get(['id', 'setoran_id', 'ayat_range', 'nilai_murajaah', 'tanggal_murajaah', 'catatan']);

        // ── Gabungkan ke satu koleksi terurut tanggal terbaru ────────────
        $aktivitas = $this->gabungkanAktivitas($setoran, $murajaah);

        // ── Statistik ringkas ────────────────────────────────────────────
        $stat = $this->hitungStat($setoran, $murajaah);

        return view('admin.laporan-tahfiz.show', compact('siswa', 'aktivitas', 'stat'));
    }

    // ── Helper ───────────────────────────────────────────────────────────

    /**
     * Gabungkan koleksi setoran dan murajaah ke satu Collection terurut terbaru.
     * Tiap item memuat: nama_surah, tanggal, kategori, nilai, kualitas (NilaiIndeks).
     *
     * @param  Collection<int, SetoranHafalan>  $setoran
     * @param  Collection<int, Murajaah>  $murajaah
     * @return Collection<int, array<string, mixed>>
     */
    private function gabungkanAktivitas(Collection $setoran, Collection $murajaah): Collection
    {
        $barisSetoran = $setoran->map(fn ($s) => [
            'id' => $s->id,
            'kategori' => 'Hafalan',
            'nama_surah' => $s->surah.' ('.$s->ayat_awal.'–'.$s->ayat_akhir.')',
            'tanggal' => $s->tanggal_setor,          // Carbon instance (di-cast)
            'nilai' => $s->nilai_hafalan,
            'kualitas' => NilaiIndeks::dariNilai($s->nilai_hafalan),
            'catatan' => $s->catatan,
            'ayat_range' => null,
        ]);

        $barisMurajaah = $murajaah->map(fn ($m) => [
            'id' => $m->id,
            'kategori' => 'Murajaah',
            'nama_surah' => $m->setoran
                ? $m->setoran->surah.' ('.$m->setoran->ayat_awal.'–'.$m->setoran->ayat_akhir.')'
                : ($m->ayat_range ?? '–'),
            'tanggal' => $m->tanggal_murajaah,       // Carbon instance (di-cast)
            'nilai' => $m->nilai_murajaah,
            'kualitas' => NilaiIndeks::dariNilai($m->nilai_murajaah),
            'catatan' => $m->catatan,
            'ayat_range' => $m->ayat_range,
        ]);

        return $barisSetoran
            ->concat($barisMurajaah)
            ->sortByDesc('tanggal')
            ->values();
    }

    /**
     * Hitung statistik ringkas: total setoran, total murajaah, rata-rata nilai,
     * dan indeks huruf rata-rata.
     *
     * @param  Collection<int, SetoranHafalan>  $setoran
     * @param  Collection<int, Murajaah>  $murajaah
     * @return array<string, mixed>
     */
    private function hitungStat(Collection $setoran, Collection $murajaah): array
    {
        $totalSetoran = $setoran->count();
        $totalMurajaah = $murajaah->count();

        // Rata-rata gabungan: prioritaskan nilai murajaah (evaluasi terkini)
        $semuaNilai = $setoran->pluck('nilai_hafalan')
            ->concat($murajaah->pluck('nilai_murajaah'));

        $rataRata = $semuaNilai->isNotEmpty()
            ? round($semuaNilai->average(), 1)
            : 0.0;

        return [
            'total_setoran' => $totalSetoran,
            'total_murajaah' => $totalMurajaah,
            'rata_nilai' => $rataRata,
            'indeks' => NilaiIndeks::dariNilai($rataRata),
        ];
    }
}
