<?php

namespace App\Enums;

enum TargetStatus: string
{
    case Aktif = 'Aktif';
    case Selesai = 'Selesai';
    case Nonaktif = 'Nonaktif';
}
