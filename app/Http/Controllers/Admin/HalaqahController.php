<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHalaqahRequest;
use App\Http\Requests\Admin\UpdateHalaqahRequest;
use App\Models\Halaqah;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HalaqahController extends Controller
{
    /**
     * Daftar seluruh halaqah beserta ustadz pembimbing dan jumlah siswa.
     */
    public function index(Request $request): View
    {
        $halaqahList = Halaqah::with([
            'ustadz:id,nama_lengkap',
            'siswa' => fn ($q) => $q->orderBy('nama_lengkap')->select(['id', 'nama_lengkap', 'nip_nis', 'halaqah_id']),
        ])
            ->withCount(['siswa'])
            ->orderBy('kode_halaqah')
            ->paginate(20)
            ->withQueryString();

        return view('admin.halaqah.index', compact('halaqahList'));
    }

    /**
     * Form tambah halaqah baru.
     */
    public function create(): View
    {
        $ustadzList = User::where('role', UserRole::Ustadz)
            ->orderBy('nama_lengkap')
            ->get(['id', 'nama_lengkap', 'nip_nis']);

        // Semua siswa dengan info halaqah saat ini untuk panel pemilihan anggota.
        $siswaList = User::where('role', UserRole::Siswa)
            ->with('halaqah:id,nama_halaqah')
            ->orderBy('nama_lengkap')
            ->get(['id', 'nama_lengkap', 'nip_nis', 'halaqah_id']);

        return view('admin.halaqah.create', compact('ustadzList', 'siswaList'));
    }

    /**
     * Simpan halaqah baru.
     * Setelah record halaqah dibuat, perbarui halaqah_id untuk siswa yang dicentang.
     */
    public function store(StoreHalaqahRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['target_juz'])) {
            $data['target_juz'] = 'Juz 29 - 30';
        }

        $halaqah = Halaqah::create([
            'kode_halaqah' => $data['kode_halaqah'],
            'nama_halaqah' => $data['nama_halaqah'],
            'ustadz_id' => $data['ustadz_id'] ?? null,
            'target_juz' => $data['target_juz'],
        ]);

        // Tetapkan halaqah_id untuk siswa yang dicentang (hanya role=siswa).
        $siswaIds = $data['siswa_ids'] ?? [];
        if (! empty($siswaIds)) {
            User::where('role', UserRole::Siswa)
                ->whereIn('id', $siswaIds)
                ->update(['halaqah_id' => $halaqah->id]);
        }

        return redirect()
            ->route('admin.halaqah.index')
            ->with('success', 'Halaqah berhasil ditambahkan.');
    }

    /**
     * Form edit halaqah.
     */
    public function edit(Halaqah $halaqah): View
    {
        $ustadzList = User::where('role', UserRole::Ustadz)
            ->orderBy('nama_lengkap')
            ->get(['id', 'nama_lengkap', 'nip_nis']);

        // Semua siswa: anggota halaqah ini ditandai, siswa lain juga ditampilkan.
        $siswaList = User::where('role', UserRole::Siswa)
            ->with('halaqah:id,nama_halaqah')
            ->orderBy('nama_lengkap')
            ->get(['id', 'nama_lengkap', 'nip_nis', 'halaqah_id']);

        // ID siswa yang saat ini tergabung di halaqah ini (untuk state awal checkbox).
        $siswaHalaqahIni = $siswaList
            ->where('halaqah_id', $halaqah->id)
            ->pluck('id')
            ->toArray();

        return view('admin.halaqah.edit', compact('halaqah', 'ustadzList', 'siswaList', 'siswaHalaqahIni'));
    }

    /**
     * Perbarui data halaqah dan sinkronkan keanggotaan siswa.
     *
     * - Siswa dicentang → halaqah_id = $halaqah->id
     * - Siswa sebelumnya di halaqah ini tapi tidak dicentang → halaqah_id = null
     * - Siswa di halaqah lain yang tidak dicentang → tidak diubah
     */
    public function update(UpdateHalaqahRequest $request, Halaqah $halaqah): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['target_juz'])) {
            $data['target_juz'] = 'Juz 29 - 30';
        }

        $halaqah->update([
            'kode_halaqah' => $data['kode_halaqah'],
            'nama_halaqah' => $data['nama_halaqah'],
            'ustadz_id' => $data['ustadz_id'] ?? null,
            'target_juz' => $data['target_juz'],
        ]);

        $siswaIds = array_map('intval', $data['siswa_ids'] ?? []);

        // Tambahkan anggota baru yang dicentang.
        if (! empty($siswaIds)) {
            User::where('role', UserRole::Siswa)
                ->whereIn('id', $siswaIds)
                ->update(['halaqah_id' => $halaqah->id]);
        }

        // Lepaskan siswa yang sebelumnya di halaqah ini tapi kini tidak dicentang.
        User::where('role', UserRole::Siswa)
            ->where('halaqah_id', $halaqah->id)
            ->when(! empty($siswaIds), fn ($q) => $q->whereNotIn('id', $siswaIds))
            ->when(empty($siswaIds), fn ($q) => $q) // hapus semua jika kosong
            ->update(['halaqah_id' => null]);

        return redirect()
            ->route('admin.halaqah.index')
            ->with('success', 'Data halaqah berhasil diperbarui.');
    }

    /**
     * Hapus halaqah.
     * FK users.halaqah_id ON DELETE SET NULL ditangani database.
     */
    public function destroy(Halaqah $halaqah): RedirectResponse
    {
        $halaqah->delete();

        return redirect()
            ->route('admin.halaqah.index')
            ->with('success', 'Halaqah berhasil dihapus.');
    }
}
