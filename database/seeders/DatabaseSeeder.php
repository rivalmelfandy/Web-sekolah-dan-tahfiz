<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


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


    public function Siswa(): void
    {
        $nis = '999999';
        $username = 'siswa_uji_999999';

        // Periksa apakah NIS sudah digunakan
        if (DB::table('users')->where('nip_nis', $nis)->exists()) {
            dump('NIS sudah terdaftar. Tidak ada perubahan.');
            return;
        }

        // Periksa apakah username sudah digunakan
        if (DB::table('users')->where('username', $username)->exists()) {
            dump('Username sudah digunakan. Tidak ada perubahan.');
            return;
        }

        // Buat akun Siswa sesuai struktur database saat ini
        DB::table('users')->insert([
            'username' => $username,
            'nip_nis' => $nis,
            'password' => Hash::make('Siswa123'),
            'nama_lengkap' => 'Siswa Uji',
            'role' => UserRole::Siswa->value,
            'no_hp' => null,
        ]);

        dump('Akun Siswa berhasil dibuat!');
    }

}
