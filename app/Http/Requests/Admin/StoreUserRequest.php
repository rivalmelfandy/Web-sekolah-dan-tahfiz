<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
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
            'nip_nis' => ['required', 'string', 'max:30', Rule::unique('users', 'nip_nis')],
            'password' => ['required', 'string', Password::min(6)],
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'role' => ['required', Rule::enum(UserRole::class)],
            'halaqah_id' => [
                'nullable',
                'integer',
                Rule::exists('halaqah', 'id'),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nip_nis.required' => 'NIP/NIS wajib diisi.',
            'nip_nis.unique' => 'NIP/NIS sudah digunakan oleh pengguna lain.',
            'password.required' => 'Password wajib diisi.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'role.required' => 'Peran wajib dipilih.',
            'halaqah_id.exists' => 'Halaqah yang dipilih tidak ditemukan.',
        ];
    }
}
