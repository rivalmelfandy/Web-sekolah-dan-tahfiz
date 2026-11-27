<?php

namespace App\Http\Controllers\Ustadz;

use App\Enums\KualitasLevel;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Halaqah;
use App\Models\Murajaah;
use App\Models\SetoranHafalan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Halaman Rekapitulasi Tahfiz Halaqah Binaan Ustadz (Dashboard).
     * Terhubung 100% ke database, mengadaptasi tata letak dan kartu KPI dari referensi RPLBO.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // ── Halaqah yang diampu ustadz ini ──────────────────────────────
        $halaqah = Halaqah::where('ustadz_id', $user->id)->first();

        // ── Daftar santri binaan (dari database) ────────────────────────
        if ($halaqah) {
            $santriList = User::where('role', UserRole::Siswa)
                ->where('halaqah_id', $halaqah->id)
                ->orderBy('nama_lengkap')
                ->get();
        } else {
            // Jika ustadz belum dipasangkan ke halaqah spesifik, tampilkan seluruh siswa sebagai cadangan
            $santriList = User::where('role', UserRole::Siswa)
                ->orderBy('nama_lengkap')
                ->get();
        }

        $santriIds = $santriList->pluck('id');

        // ── 4 Indikator KPI ─────────────────────────────────────────────
        $totalSantri = $santriList->count();

        $totalSetoran = SetoranHafalan::whereIn('siswa_id', $santriIds)->count();

        $totalMurajaah = Murajaah::whereIn('siswa_id', $santriIds)->count();

        $setoranLancar = SetoranHafalan::whereIn('siswa_id', $santriIds)
            ->where('kualitas', KualitasLevel::Lancar->value)
            ->count();

        $persentaseKelancaran = $totalSetoran > 0
            ? round(($setoranLancar / $totalSetoran) * 100, 1)
            : 0;

        // ── Data rekapitulasi per santri beserta riwayat lengkap ────────
        // Eager load setoran dan murajaah agar query efisien (tanpa N+1 problem)
        $setoranMap = SetoranHafalan::whereIn('siswa_id', $santriIds)
            ->orderByDesc('tanggal_setor')
            ->orderByDesc('id')
            ->get()
            ->groupBy('siswa_id');

        $murajaahMap = Murajaah::whereIn('siswa_id', $santriIds)
            ->orderByDesc('tanggal_murajaah')
            ->orderByDesc('id')
            ->get()
            ->groupBy('siswa_id');

        $rekapSantri = $santriList->map(function ($santri) use ($setoranMap, $murajaahMap) {
            $setoranSiswa = $setoranMap->get($santri->id, collect());
            $murajaahSiswa = $murajaahMap->get($santri->id, collect());

            $totalSetor = $setoranSiswa->count();
            $lancarCount = $setoranSiswa->where('kualitas', KualitasLevel::Lancar)->count();

            $capaian = $totalSetor > 0
                ? round(($lancarCount / $totalSetor) * 100)
                : 0;

            $terakhirSetor = $setoranSiswa->first();

            return (object) [
                'id' => $santri->id,
                'nip_nis' => $santri->nip_nis,
                'nama_lengkap' => $santri->nama_lengkap,
                'capaian' => $capaian,
                'total_setoran' => $totalSetor,
                'total_murajaah' => $murajaahSiswa->count(),
                'tanggal_terakhir' => $terakhirSetor?->tanggal_setor?->translatedFormat('d M Y') ?? '–',
                'surah_terakhir' => $terakhirSetor ? "{$terakhirSetor->surah} (Ayat {$terakhirSetor->ayat_awal}–{$terakhirSetor->ayat_akhir})" : 'Belum ada setoran',
                'riwayat_setoran' => $setoranSiswa->take(5),
                'riwayat_murajaah' => $murajaahSiswa->take(5),
            ];
        });

        return view('ustadz.dashboard', [
            'user' => $user,
            'halaqah' => $halaqah,
            'totalSantri' => $totalSantri,
            'totalSetoran' => $totalSetoran,
            'totalMurajaah' => $totalMurajaah,
            'persentaseKelancaran' => $persentaseKelancaran,
            'rekapSantri' => $rekapSantri,
        ]);
    }
}
