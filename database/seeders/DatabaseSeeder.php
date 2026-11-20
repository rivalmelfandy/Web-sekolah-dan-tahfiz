<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Tahap 2: hanya membuat 1 akun admin awal agar dapat login dan
     * mengelola data asli. Tidak ada data dummy siswa, setoran, atau murajaah.
     */
    public function run(): void
    {
        User::create([
            'nip_nis' => 'admin',
            'password' => Hash::make('admin123'),
            'nama_lengkap' => 'Administrator',
            'role' => UserRole::Admin,
            'halaqah_id' => null,
        ]);
    }
}
