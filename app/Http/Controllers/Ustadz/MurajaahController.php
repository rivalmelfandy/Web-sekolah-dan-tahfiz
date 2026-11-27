<?php

namespace App\Http\Controllers\Ustadz;

use App\Enums\NilaiIndeks;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ustadz\StoreMurajaahRequest;
use App\Models\Halaqah;
use App\Models\Murajaah;
use App\Models\SetoranHafalan;
use App\Models\User;
use App\Support\QuranHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MurajaahController extends Controller
{
    /**
     * Halaman Penilaian Murajaah Santri (Layout 2 Kolom).
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // ── Halaqah dan santri binaan ───────────────────────────────────
        $halaqah = Halaqah::where('ustadz_id', $user->id)->first();

        if ($halaqah) {
            $santriList = User::where('role', UserRole::Siswa)
                ->where('halaqah_id', $halaqah->id)
                ->orderBy('nama_lengkap')
                ->get();
        } else {
            $santriList = User::where('role', UserRole::Siswa)
                ->orderBy('nama_lengkap')
                ->get();
        }

        $santriIds = $santriList->pluck('id');

        // ── Setoran per santri (sumber daftar setoran yang bisa dimurajaah) ──
        $semuaSetoran = SetoranHafalan::whereIn('siswa_id', $santriIds)
            ->orderByDesc('tanggal_setor')
            ->orderByDesc('id')
            ->get();
        $setoranPerSiswa = $semuaSetoran->groupBy('siswa_id');

        // ── Murajaah per santri via relasi setoran ──────────────────────
        $setoranIds = $semuaSetoran->pluck('id');

        $semuaMurajaah = Murajaah::with('setoran:id,siswa_id,surah,ayat_awal,ayat_akhir')
            ->whereIn('setoran_id', $setoranIds)
            ->orderByDesc('tanggal_murajaah')
            ->orderByDesc('id')
            ->get();

        // Kelompokkan murajaah berdasarkan siswa_id dari setoran
        $murajaahPerSiswa = $semuaMurajaah->groupBy(
            fn ($m) => $m->setoran?->siswa_id
        );

        // ── Santri yang sedang dipilih ──────────────────────────────────
        $selectedSiswaId = $request->integer('siswa_id') ?: null;
        $selectedSiswa   = $selectedSiswaId
            ? $santriList->firstWhere('id', $selectedSiswaId)
            : null;

        $surahList = QuranHelper::getSurahList();

        return view('ustadz.murajaah', [
            'user'             => $user,
            'halaqah'          => $halaqah,
            'santriList'       => $santriList,
            'selectedSiswa'    => $selectedSiswa,
            'setoranPerSiswa'  => $setoranPerSiswa,
            'murajaahPerSiswa' => $murajaahPerSiswa,
            'surahList'        => $surahList,
        ]);
    }

    /**
     * Simpan evaluasi murajaah ke database.
     * Kolom setoran_id wajib — murajaah selalu terkait setoran.
     * setoran_hafalan.nilai_hafalan TIDAK ditimpa (aturan bisnis DBML).
     */
    public function store(StoreMurajaahRequest $request): RedirectResponse
    {
        $data              = $request->validated();
        $data['ustadz_id'] = $request->user()->id;

        $murajaah = Murajaah::create($data);

        // Ambil nama siswa dari setoran yang dimurajaah
        $setoran   = SetoranHafalan::find($data['setoran_id']);
        $namaSiswa = $setoran?->siswa?->nama_lengkap ?? 'Siswa';
        $siswaId   = $setoran?->siswa_id;

        $indeks = NilaiIndeks::dariNilai($murajaah->nilai_murajaah);

        return redirect()
            ->route('ustadz.murajaah.index', array_filter(['siswa_id' => $siswaId]))
            ->with(
                'success',
                "Alhamdulillah! Evaluasi murajaah {$namaSiswa} untuk Surah {$setoran?->surah}"
                . " berhasil disimpan. Nilai: {$murajaah->nilai_murajaah} (Predikat {$indeks->value})."
            );
    }
}
