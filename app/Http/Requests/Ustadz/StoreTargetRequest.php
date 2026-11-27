<?php

namespace App\Http\Requests\Ustadz;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTargetRequest extends FormRequest
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
            'surah_awal' => ['required', 'string', 'max:180'],
            'surah_akhir' => ['required', 'string', 'max:180'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_akhir' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
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
            'surah_awal.required' => 'Surah/Juz awal wajib diisi.',
            'surah_akhir.required' => 'Surah/Juz akhir wajib diisi.',
            'tanggal_mulai.required' => 'Tanggal mulai target wajib diisi.',
            'tanggal_akhir.required' => 'Batas akhir target wajib diisi.',
            'tanggal_akhir.after_or_equal' => 'Batas akhir harus sama dengan atau setelah tanggal mulai.',
        ];
    }
}
