@extends('layouts.admin', ['title' => 'Edit Halaqah'])

@section('breadcrumb', 'Edit Halaqah')

@section('content')

<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <div>
        <h2 class="fw-bold mb-0 h4" style="color:#1F4172;">
            <i class="bi bi-pencil me-2"></i>Edit Halaqah
        </h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.halaqah.index') }}" class="text-decoration-none">Halaqah</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>
</div>

@include('admin._alert')

<div class="card border-0 shadow-sm rounded-4" style="max-width: 560px;">
    <div class="card-body p-4 p-md-5">
        <form method="POST" action="{{ route('admin.halaqah.update', $halaqah) }}">
            @csrf
            @method('PUT')

            {{-- Kode Halaqah --}}
            <div class="mb-3">
                <label for="kode_halaqah" class="form-label fw-semibold small">Kode Halaqah <span class="text-danger">*</span></label>
                <input type="text" id="kode_halaqah" name="kode_halaqah"
                       class="form-control @error('kode_halaqah') is-invalid @enderror"
                       value="{{ old('kode_halaqah', $halaqah->kode_halaqah) }}" maxlength="20" required>
                @error('kode_halaqah')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Nama Halaqah --}}
            <div class="mb-3">
                <label for="nama_halaqah" class="form-label fw-semibold small">Nama Halaqah <span class="text-danger">*</span></label>
                <input type="text" id="nama_halaqah" name="nama_halaqah"
                       class="form-control @error('nama_halaqah') is-invalid @enderror"
                       value="{{ old('nama_halaqah', $halaqah->nama_halaqah) }}" maxlength="50" required>
                @error('nama_halaqah')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Ustadz Pembimbing --}}
            <div class="mb-3">
                <label for="ustadz_id" class="form-label fw-semibold small">Ustadz Pembimbing</label>
                <select id="ustadz_id" name="ustadz_id"
                        class="form-select @error('ustadz_id') is-invalid @enderror">
                    <option value="">— Belum Ditentukan —</option>
                    @foreach($ustadzList as $u)
                        <option value="{{ $u->id }}"
                            {{ old('ustadz_id', $halaqah->ustadz_id) == $u->id ? 'selected' : '' }}>
                            {{ $u->nama_lengkap }} ({{ $u->nip_nis }})
                        </option>
                    @endforeach
                </select>
                @error('ustadz_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Target Juz --}}
            <div class="mb-4">
                <label for="target_juz" class="form-label fw-semibold small">Target Juz</label>
                <input type="text" id="target_juz" name="target_juz"
                       class="form-control @error('target_juz') is-invalid @enderror"
                       value="{{ old('target_juz', $halaqah->target_juz) }}" maxlength="50">
                @error('target_juz')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary fw-semibold">
                    <i class="bi bi-check-lg me-1"></i>Perbarui
                </button>
                <a href="{{ route('admin.halaqah.index') }}" class="btn btn-outline-secondary fw-semibold">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
