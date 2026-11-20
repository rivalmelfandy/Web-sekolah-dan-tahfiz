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
        $halaqahList = Halaqah::with('ustadz')
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

        return view('admin.halaqah.create', compact('ustadzList'));
    }

    /**
     * Simpan halaqah baru.
     */
    public function store(StoreHalaqahRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Pastikan target_juz terisi default jika dikosongkan.
        if (empty($data['target_juz'])) {
            $data['target_juz'] = 'Juz 29 - 30';
        }

        Halaqah::create($data);

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

        return view('admin.halaqah.edit', compact('halaqah', 'ustadzList'));
    }

    /**
     * Perbarui data halaqah.
     */
    public function update(UpdateHalaqahRequest $request, Halaqah $halaqah): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['target_juz'])) {
            $data['target_juz'] = 'Juz 29 - 30';
        }

        $halaqah->update($data);

        return redirect()
            ->route('admin.halaqah.index')
            ->with('success', 'Data halaqah berhasil diperbarui.');
    }

    /**
     * Hapus halaqah.
     */
    public function destroy(Halaqah $halaqah): RedirectResponse
    {
        $halaqah->delete();

        return redirect()
            ->route('admin.halaqah.index')
            ->with('success', 'Halaqah berhasil dihapus.');
    }
}
