<?php

namespace App\Enums;

enum KualitasLevel: string
{
    case Lancar = 'Lancar';
    case KurangLancar = 'Kurang Lancar';
    case TidakLancar = 'Tidak Lancar';
}
