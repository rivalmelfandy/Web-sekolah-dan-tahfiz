<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Ustadz = 'ustadz';
    case Siswa = 'siswa';

    /**
     * Label ramah pengguna untuk ditampilkan di antarmuka.
     */
    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Ustadz => 'Ustadz/Ustadzah',
            self::Siswa => 'Siswa',
        };
    }
}
