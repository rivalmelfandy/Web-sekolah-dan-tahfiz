<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHalaqahRequest extends FormRequest
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
        $halaqahId = $this->route('halaqah')?->id;

        return [
            'kode_halaqah' => ['required', 'string', 'max:20', Rule::unique('halaqah', 'kode_halaqah')->ignore($halaqahId)],
            'nama_halaqah' => ['required', 'string', 'max:50', Rule::unique('halaqah', 'nama_halaqah')->ignore($halaqahId)],
            'ustadz_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where('role', 'ustadz')],
            'target_juz' => ['nullable', 'string', 'max:50'],
            'siswa_ids' => ['nullable', 'array'],
            'siswa_ids.*' => ['integer', Rule::exists('users', 'id')->where('role', 'siswa')],
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
