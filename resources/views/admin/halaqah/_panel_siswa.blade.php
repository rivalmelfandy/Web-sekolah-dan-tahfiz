{{--
    Partial: Panel Anggota Siswa
    Variabel wajib dari view induk:
      $siswaList       — Collection<User> (role=siswa, with halaqah:id,nama_halaqah)
      $checkedIds      — array<int> ID siswa yang harus dicentang (awal / old)
      $currentHalaqahId — int|null  ID halaqah yang sedang diedit (null saat create)
--}}
<div class="card border-0 shadow-sm rounded-4 h-100 d-flex flex-column" id="panelSiswa">

    {{-- ── Header panel ──────────────────────────────────────────── --}}
    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2 d-flex align-items-center justify-content-between">
        <span class="fw-bold small" style="color:#1F4172;">
            <i class="bi bi-people-fill me-1" style="color:#047857;"></i>Anggota Siswa
        </span>
        <span class="badge rounded-pill fw-bold px-3 py-1" id="badgeSiswa"
              style="background:#ECFDF5; color:#065F46; font-size:.72rem;">
            0 siswa dipilih
        </span>
    </div>

    {{-- ── Kolom pencarian real-time ─────────────────────────────── --}}
    <div class="px-4 pb-2">
        <div class="input-group input-group-sm">
            <span class="input-group-text bg-white border-end-0" style="border-color:#D1D5DB;">
                <i class="bi bi-search text-muted" style="font-size:.8rem;"></i>
            </span>
            <input type="text" id="cariSiswa"
                   class="form-control border-start-0 ps-1"
                   style="border-color:#D1D5DB; font-size:.82rem;"
                   placeholder="Cari nama atau NIS siswa…"
                   oninput="filterSiswa(this.value)">
        </div>
    </div>

    {{-- ── Daftar checkbox siswa (scrollable) ──────────────────────── --}}
    <div class="px-3 pb-1 flex-grow-1" style="overflow-y:auto; max-height:340px; min-height:120px;" id="daftarSiswa">

        @forelse($siswaList as $siswa)
        @php
            // Centang jika: (a) ada di $checkedIds, atau (b) old('siswa_ids') mengandung id-nya
            $oldIds   = array_map('intval', old('siswa_ids', []));
            $isOld    = count($oldIds) > 0;
            $checked  = $isOld
                ? in_array($siswa->id, $oldIds)
                : in_array($siswa->id, $checkedIds);

            // Label halaqah saat ini (selain halaqah yang sedang diedit)
            $labelHalaqah = null;
            if ($siswa->halaqah_id && $siswa->halaqah_id !== $currentHalaqahId) {
                $labelHalaqah = $siswa->halaqah?->nama_halaqah;
            } elseif ($siswa->halaqah_id && $siswa->halaqah_id === $currentHalaqahId) {
                $labelHalaqah = null; // sudah di halaqah ini, tidak perlu label tambahan
            }
        @endphp
        <div class="siswa-item d-flex align-items-center gap-2 px-2 py-2 rounded-3 mb-1"
             style="transition: background .12s;"
             data-nama="{{ strtolower($siswa->nama_lengkap) }}"
             data-nis="{{ strtolower($siswa->nip_nis) }}">

            <input type="checkbox"
                   name="siswa_ids[]"
                   value="{{ $siswa->id }}"
                   id="siswa_{{ $siswa->id }}"
                   class="form-check-input flex-shrink-0 siswa-checkbox"
                   style="width:17px; height:17px; border-color:#047857; cursor:pointer;"
                   {{ $checked ? 'checked' : '' }}
                   onchange="updateBadge()">

            <label for="siswa_{{ $siswa->id }}" class="flex-grow-1 d-flex align-items-center justify-content-between gap-2 mb-0" style="cursor:pointer; min-width:0;">
                <div class="min-w-0">
                    <div class="fw-semibold text-truncate" style="font-size:.82rem; color:#1F4172; max-width:160px;">
                        {{ $siswa->nama_lengkap }}
                    </div>
                    <div style="font-size:.71rem; color:#64748B;">NIS: {{ $siswa->nip_nis }}</div>
                </div>
                @if($labelHalaqah)
                    <span class="flex-shrink-0 badge rounded-pill fw-semibold"
                          style="background:#FEF9C3; color:#92400E; font-size:.65rem; border:1px solid #FDE68A; white-space:nowrap;">
                        {{ Str::limit($labelHalaqah, 18) }}
                    </span>
                @endif
            </label>
        </div>
        @empty
        <div class="text-center py-4 text-muted small">
            <i class="bi bi-person-x d-block mb-1 opacity-50" style="font-size:1.5rem;"></i>
            Belum ada siswa terdaftar.
        </div>
        @endforelse

    </div>

    {{-- ── Catatan ────────────────────────────────────────────────── --}}
    <div class="px-4 pt-1 pb-3">
        <p class="text-muted mb-0" style="font-size:.7rem; line-height:1.5;">
            * Centang untuk menambahkan, hilangkan centang untuk mengeluarkan siswa dari kelompok ini.
        </p>
    </div>

</div>
