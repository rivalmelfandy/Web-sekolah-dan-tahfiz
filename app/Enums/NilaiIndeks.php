<?php

namespace App\Enums;

/**
 * Indeks huruf penilaian hafalan (0–100 → A/AB/B/BC/C/D/E).
 *
 * Konversi:
 *   90–100 → A
 *   80–89  → AB
 *   70–79  → B
 *   60–69  → BC
 *   50–59  → C
 *   40–49  → D
 *    0–39  → E
 */
enum NilaiIndeks: string
{
    case A = 'A';
    case AB = 'AB';
    case B = 'B';
    case BC = 'BC';
    case C = 'C';
    case D = 'D';
    case E = 'E';

    /**
     * Konversi nilai angka (0–100) ke indeks huruf.
     */
    public static function dariNilai(int|float $nilai): self
    {
        $n = (int) round($nilai);

        return match (true) {
            $n >= 90 => self::A,
            $n >= 80 => self::AB,
            $n >= 70 => self::B,
            $n >= 60 => self::BC,
            $n >= 50 => self::C,
            $n >= 40 => self::D,
            default => self::E,
        };
    }

    /**
     * Label lengkap untuk tampilan antarmuka.
     */
    public function label(): string
    {
        return match ($this) {
            self::A => 'A (Sangat Baik)',
            self::AB => 'AB (Baik Sekali)',
            self::B => 'B (Baik)',
            self::BC => 'BC (Cukup Baik)',
            self::C => 'C (Cukup)',
            self::D => 'D (Kurang)',
            self::E => 'E (Sangat Kurang)',
        };
    }

    /**
     * Warna Bootstrap/Tailwind untuk badge (kembalikan kode hex).
     */
    public function warnaBadge(): string
    {
        return match ($this) {
            self::A, self::AB => '#065F46',
            self::B, self::BC => '#1D4ED8',
            self::C => '#D97706',
            self::D => '#DC2626',
            self::E => '#6B7280',
        };
    }
}
