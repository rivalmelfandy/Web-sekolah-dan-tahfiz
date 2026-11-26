<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat akun Admin awal jika belum ada.
        // firstOrCreate: tidak duplikat jika dijalankan ulang.
        User::firstOrCreate(
            ['nip_nis' => 'admin'],
            [
                'password' => Hash::make('admin123'),
                'nama_lengkap' => 'Administrator',
                'no_hp' => null,
                'role' => UserRole::Admin,
            ]
        );
    }
}
