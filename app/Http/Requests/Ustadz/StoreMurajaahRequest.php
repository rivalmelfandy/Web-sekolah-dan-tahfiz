<?php

namespace App\Http\Requests\Ustadz;

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
            'setoran_id'       => ['required', 'integer', Rule::exists('setoran_hafalan', 'id')],
            'ayat_range'       => ['nullable', 'string', 'max:50'],
            'nilai_murajaah'   => ['required', 'integer', 'min:0', 'max:100'],
            'catatan'          => ['nullable', 'string', 'max:1000'],
            'tanggal_murajaah' => ['required', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'setoran_id.required'       => 'Pilih setoran yang akan dimurajaah.',
            'setoran_id.exists'         => 'Data setoran tidak valid.',
            'nilai_murajaah.required'   => 'Nilai murajaah (0–100) wajib diisi.',
            'nilai_murajaah.min'        => 'Nilai minimal 0.',
            'nilai_murajaah.max'        => 'Nilai maksimal 100.',
            'tanggal_murajaah.required' => 'Tanggal murajaah wajib diisi.',
        ];
    }
}
