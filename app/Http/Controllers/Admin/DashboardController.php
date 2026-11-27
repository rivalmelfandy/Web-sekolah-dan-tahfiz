<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Halaqah;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

    class DashboardController extends Controller
    {
        public function index(Request $request): View
        {
            // Kartu ringkasan
            $totalSiswaDalamHalaqah = User::where('role', UserRole::Siswa)
                ->whereNotNull('halaqah_id')
                ->count();

            $totalUstadz = User::where('role', UserRole::Ustadz)->count();

            $totalHalaqah = Halaqah::count();

            // Rekap jumlah setoran dan setoran berkualitas Lancar
            $halaqahList = Halaqah::with(['ustadz:id,nama_lengkap'])
                ->withCount([
                    'siswa',
                    'setoranHafalan as total_setoran_count',
                    'setoranHafalan as setoran_lancar_count' => function ($query) {
                        $query->where('nilai_hafalan', '>=', 80);
                    },
                ])
                ->orderBy('kode_halaqah')
                ->get([
                    'id',
                    'kode_halaqah',
                    'nama_halaqah',
                    'ustadz_id',
                    'target_juz',
                ]);

            // Persentase setoran dengan kualitas Lancar
            $listCapaian = $halaqahList->map(function ($h) {
                if ($h->total_setoran_count == 0) {
                    return 0;
                }

                return round(
                    ($h->setoran_lancar_count / $h->total_setoran_count) * 100,
                    1
                );
            });

            $rataCapaianKeseluruhan = $listCapaian->isNotEmpty()
                ? round($listCapaian->average(), 1)
                : 0.0;

            // Ringkasan per halaqah
            $ringkasanHalaqah = $halaqahList->map(function (Halaqah $h) {
                $capaian = $h->total_setoran_count > 0
                    ? round(
                        ($h->setoran_lancar_count / $h->total_setoran_count) * 100,
                        1
                    )
                    : 0;

                return [
                    'nama_halaqah' => $h->nama_halaqah,
                    'kode_halaqah' => $h->kode_halaqah,
                    'ustadz' => $h->ustadz?->nama_lengkap ?? '—',
                    'target_juz' => $h->target_juz,
                    'capaian' => $capaian,
                    'jumlah_siswa' => $h->siswa_count,
                    'status' => $h->siswa_count > 0 ? 'Aktif' : 'Kosong',
                ];
            });

            // Data grafik
            $grafikLabels = $halaqahList->pluck('nama_halaqah')->all();
            $grafikData = $listCapaian->values()->all();

            return view('admin.dashboard', [
                'user' => $request->user(),
                'totalSiswaDalamHalaqah' => $totalSiswaDalamHalaqah,
                'totalUstadz' => $totalUstadz,
                'totalHalaqah' => $totalHalaqah,
                'rataCapaianKeseluruhan' => $rataCapaianKeseluruhan,
                'ringkasanHalaqah' => $ringkasanHalaqah,
                'grafikLabels' => $grafikLabels,
                'grafikData' => $grafikData,
                'periodeLabel' => now()->translatedFormat('F Y'),
            ]);
        }
    }
