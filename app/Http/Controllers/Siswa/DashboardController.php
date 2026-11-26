<?php

namespace App\Http\Controllers\Siswa;

use App\Enums\NilaiIndeks;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Halaman utama dashboard siswa — Progres Hafalan.
     *
     * Kolom aktual database:
     *   - setoran_hafalan : siswa_id, surah, ayat_awal, ayat_akhir, nilai, kualitas, catatan, tanggal_setor, ustadz_id
     *   - murajaah        : siswa_id, nama_siswa, ustadz_id, nama_ustadz, surah, ayat_range, status_mutqin, tanggal_murajaah
     *   - target_hafalan  : siswa_id, ustadz_id, surah_awal, surah_akhir, tanggal_mulai, tanggal_akhir, status
     *   - users           : id, nama_lengkap, nip_nis, username, no_hp, role, halaqah (varchar), status
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // ── Total setoran ──────────────────────────────────────────────
        $totalSetoran = DB::table('setoran_hafalan')
            ->where('siswa_id', $user->id)
            ->count();

        // ── Rata-rata nilai (kolom aktual: `nilai`) ────────────────────
        $rataNilai = null;
        $predikatRata = null;
        if ($totalSetoran > 0) {
            $avg = DB::table('setoran_hafalan')
                ->where('siswa_id', $user->id)
                ->avg('nilai');
            $rataNilai = round((float) $avg, 1);
            $predikatRata = NilaiIndeks::dariNilai($rataNilai);
        }

        // ── Total murajaah langsung via siswa_id ───────────────────────
        $totalMurajaah = DB::table('murajaah')
            ->where('siswa_id', $user->id)
            ->count();

        // ── Target hafalan aktif ───────────────────────────────────────
        $targetAktifRaw = DB::table('target_hafalan')
            ->where('siswa_id', $user->id)
            ->where('status', 'Aktif')
            ->orderByDesc('tanggal_mulai')
            ->orderByDesc('id')
            ->first();

        // Nama ustadz untuk target aktif
        $namaUstadzTarget = null;
        if ($targetAktifRaw && $targetAktifRaw->ustadz_id) {
            $namaUstadzTarget = DB::table('users')
                ->where('id', $targetAktifRaw->ustadz_id)
                ->value('nama_lengkap');
        }

        // ── Hitung sisa hari target ────────────────────────────────────
        $hariSisaTarget = null;
        if ($targetAktifRaw) {
            $hariSisaTarget = (int) now()->diffInDays(
                \Carbon\Carbon::parse($targetAktifRaw->tanggal_akhir),
                false
            );
        }

        // ── Capaian target dalam persen ───────────────────────────────
        // Gunakan rata-rata nilai setoran sebagai proxy capaian (0–100).
        // Jika tidak ada setoran, capaian 0.
        $pctCapaian = $rataNilai !== null
            ? (int) min(100, max(0, $rataNilai))
            : 0;

        // ── 5 Setoran terbaru ──────────────────────────────────────────
        $setoranTerbaruRaw = DB::table('setoran_hafalan')
            ->where('siswa_id', $user->id)
            ->orderByDesc('tanggal_setor')
            ->orderByDesc('id')
            ->limit(5)
            ->get(['id', 'surah', 'ayat_awal', 'ayat_akhir', 'nilai', 'kualitas', 'tanggal_setor', 'ustadz_id']);

        // Map ke objek dengan nama ustadz + indeks nilai
        $ustadzIds = $setoranTerbaruRaw->pluck('ustadz_id')->filter()->unique()->values();
        $ustadzMap = DB::table('users')
            ->whereIn('id', $ustadzIds)
            ->pluck('nama_lengkap', 'id');

        $setoranTerbaru = $setoranTerbaruRaw->map(fn($s) => (object) [
            'id' => $s->id,
            'surah' => $s->surah,
            'ayat_awal' => $s->ayat_awal,
            'ayat_akhir' => $s->ayat_akhir,
            'nilai' => (int) $s->nilai,
            'kualitas' => $s->kualitas,
            'indeks' => NilaiIndeks::dariNilai((int) $s->nilai),
            'tanggal_setor' => \Carbon\Carbon::parse($s->tanggal_setor),
            'nama_ustadz' => $ustadzMap[$s->ustadz_id] ?? '–',
        ]);

        // ── Nama halaqah dari kolom users.halaqah (varchar) ───────────
        $namaHalaqah = $user->halaqah ?? null;

        // ── Setoran terbaru tunggal untuk panel "hafalan terakhir" ─────
        $setoranTerakhir = $setoranTerbaru->first();

        return view('siswa.dashboard', compact(
            'user',
            'totalSetoran',
            'rataNilai',
            'predikatRata',
            'totalMurajaah',
            'targetAktifRaw',
            'namaUstadzTarget',
            'hariSisaTarget',
            'setoranTerbaru',
            'setoranTerakhir',
            'namaHalaqah',
            'pctCapaian',
        ));
    }
}
