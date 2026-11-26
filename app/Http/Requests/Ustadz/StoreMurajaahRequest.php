<?php

namespace App\Http\Requests\Ustadz;

use App\Enums\KualitasLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMurajaahRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isUstadz() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'siswa_id' => ['required', 'integer', Rule::exists('users', 'id')->where('role', 'siswa')],
            'setoran_id' => ['nullable', 'integer', Rule::exists('setoran_hafalan', 'id')],
            'surah' => ['required', 'string', 'max:50'],
            'ayat_range' => ['required', 'string', 'max:50'],
            'status_mutqin' => ['required', Rule::enum(KualitasLevel::class)],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'tanggal_murajaah' => ['required', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'siswa_id.required' => 'Pilih siswa terlebih dahulu.',
            'siswa_id.exists' => 'Data siswa tidak valid.',
            'surah.required' => 'Surah yang dimurajaah wajib diisi.',
            'ayat_range.required' => 'Rentang ayat wajib diisi.',
            'status_mutqin.required' => 'Status kelancaran/mutqin wajib ditentukan.',
            'tanggal_murajaah.required' => 'Tanggal murajaah wajib diisi.',
        ];
    }
}
