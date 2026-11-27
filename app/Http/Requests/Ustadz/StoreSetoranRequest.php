<?php

namespace App\Http\Requests\Ustadz;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSetoranRequest extends FormRequest
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
            'siswa_id'      => ['required', 'integer', Rule::exists('users', 'id')->where('role', 'siswa')],
            'surah'         => ['required', 'string', 'max:50'],
            'ayat_awal'     => ['required', 'integer', 'min:1'],
            'ayat_akhir'    => ['required', 'integer', 'gte:ayat_awal'],
            'nilai_hafalan' => ['required', 'integer', 'min:0', 'max:100'],
            'catatan'       => ['nullable', 'string', 'max:1000'],
            'tanggal_setor' => ['required', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'siswa_id.required'      => 'Pilih siswa terlebih dahulu.',
            'siswa_id.exists'        => 'Data siswa tidak valid.',
            'surah.required'         => 'Surah wajib dipilih.',
            'ayat_awal.required'     => 'Ayat awal wajib diisi.',
            'ayat_awal.min'          => 'Ayat awal minimal 1.',
            'ayat_akhir.required'    => 'Ayat akhir wajib diisi.',
            'ayat_akhir.gte'         => 'Ayat akhir harus lebih besar atau sama dengan ayat awal.',
            'nilai_hafalan.required' => 'Nilai hafalan (0–100) wajib diisi.',
            'nilai_hafalan.min'      => 'Nilai minimal 0.',
            'nilai_hafalan.max'      => 'Nilai maksimal 100.',
            'tanggal_setor.required' => 'Tanggal setor wajib diisi.',
        ];
    }
}
