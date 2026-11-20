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
    /**
     * Dashboard rekap administrator.
     *
     * Semua query agregat dijalankan di controller; tidak ada query di Blade.
     */
    public function index(Request $request): View
    {
        // ── Kartu ringkasan ──────────────────────────────────────────────

        /** Siswa yang sudah terdaftar dalam halaqah */
        $totalSiswaDalamHalaqah = User::where('role', UserRole::Siswa)
            ->whereNotNull('halaqah_id')
            ->count();

        /** Seluruh ustadz/ustadzah */
        $totalUstadz = User::where('role', UserRole::Ustadz)->count();

        /** Seluruh halaqah */
        $totalHalaqah = Halaqah::count();

        // ── Capaian per halaqah ──────────────────────────────────────────
        //
        // Capaian per halaqah = rata-rata kolom `nilai` dari setoran_hafalan
        // seluruh siswa di halaqah tersebut.
        // Jika halaqah belum punya siswa atau setoran → nilai_avg NULL → 0.
        //
        // Strategi query:
        //   - Load semua halaqah dengan ustadz (eager, hanya kolom yang dibutuhkan).
        //   - Hitung rata-rata `nilai` via subquery withAvg yang di-scope ke siswa
        //     dalam halaqah itu melalui hasManyThrough (SetoranHafalan via User).
        //   - Satu roundtrip query tambahan dengan withAvg = 1 extra LEFT JOIN query.

        $halaqahList = Halaqah::with(['ustadz:id,nama_lengkap'])
            ->withCount(['siswa'])
            ->withAvg(
                // withAvg pada relasi hasManyThrough
                'setoranHafalan',
                'nilai'
            )
            ->orderBy('kode_halaqah')
            ->get(['id', 'kode_halaqah', 'nama_halaqah', 'ustadz_id', 'target_juz']);

        // Rata-rata capaian keseluruhan (hindari division-by-zero jika semua avg NULL)
        $listCapaian = $halaqahList
            ->map(fn ($h) => round($h->setoran_hafalan_avg_nilai ?? 0, 1));

        $rataCapaianKeseluruhan = $listCapaian->isNotEmpty()
            ? round($listCapaian->average(), 1)
            : 0.0;

        // ── Ringkasan per halaqah (untuk tabel di view) ──────────────────
        //
        // Setiap item: nama halaqah, ustadz, target_juz, persen capaian, status
        // Status = "Aktif" jika ada ≥1 siswa, "Kosong" jika belum ada siswa.

        $ringkasanHalaqah = $halaqahList->map(function (Halaqah $h) {
            $capaian = round($h->setoran_hafalan_avg_nilai ?? 0, 1);

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

        // ── Data grafik batang (label & nilai dalam format yang siap di-encode) ─

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
