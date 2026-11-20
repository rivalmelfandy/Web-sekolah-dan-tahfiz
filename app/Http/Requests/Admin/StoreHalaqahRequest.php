<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHalaqahRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'kode_halaqah' => ['required', 'string', 'max:20', Rule::unique('halaqah', 'kode_halaqah')],
            'nama_halaqah' => ['required', 'string', 'max:50', Rule::unique('halaqah', 'nama_halaqah')],
            'ustadz_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where('role', 'ustadz')],
            'target_juz' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kode_halaqah.required' => 'Kode halaqah wajib diisi.',
            'kode_halaqah.unique' => 'Kode halaqah sudah digunakan.',
            'nama_halaqah.required' => 'Nama halaqah wajib diisi.',
            'nama_halaqah.unique' => 'Nama halaqah sudah digunakan.',
            'ustadz_id.exists' => 'Ustadz yang dipilih tidak valid.',
        ];
    }
}
