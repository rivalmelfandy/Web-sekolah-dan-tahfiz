<?php

namespace App\Support;

class QuranHelper
{
    /**
     * Cache daftar surah.
     *
     * @var array<int, array{nomor: int, nama: string, arab: string, ayat: int}>|null
     */
    private static ?array $surahList = null;

    /**
     * Dapatkan semua daftar 114 surah dari file JSON.
     *
     * @return array<int, array{nomor: int, nama: string, arab: string, ayat: int}>
     */
    public static function getSurahList(): array
    {
        if (self::$surahList !== null) {
            return self::$surahList;
        }

        $path = resource_path('data/surah.json');
        if (file_exists($path)) {
            $json = file_get_contents($path);
            self::$surahList = json_decode($json, true) ?: [];
        } else {
            self::$surahList = [];
        }

        return self::$surahList;
    }

    /**
     * Dapatkan daftar surah khusus Juz 30 (Surah 78 An-Naba s.d. 114 An-Nas).
     *
     * @return array<int, array{nomor: int, nama: string, arab: string, ayat: int}>
     */
    public static function getJuz30Surahs(): array
    {
        return array_values(array_filter(
            self::getSurahList(),
            fn ($s) => $s['nomor'] >= 78 && $s['nomor'] <= 114
        ));
    }

    /**
     * Dapatkan kurikulum target opsi per Juz.
     *
     * @return array<string, list<string>>
     */
    public static function getKurikulumTarget(): array
    {
        $juz30 = [];
        foreach (self::getJuz30Surahs() as $s) {
            $juz30[] = "Juz 30 – {$s['nama']} (Ayat 1–{$s['ayat']})";
        }

        return [
            'Juz 30' => $juz30,
            'Juz 29' => [
                'Juz 29 – Al-Mulk (Ayat 1–30)',
                'Juz 29 – Al-Qalam (Ayat 1–52)',
                'Juz 29 – Al-Haqqah (Ayat 1–52)',
                'Juz 29 – Al-Ma\'arij (Ayat 1–44)',
                'Juz 29 – Nuh (Ayat 1–28)',
                'Juz 29 – Al-Jinn (Ayat 1–28)',
                'Juz 29 – Al-Muzzammil (Ayat 1–20)',
                'Juz 29 – Al-Muddassir (Ayat 1–56)',
                'Juz 29 – Al-Qiyamah (Ayat 1–40)',
                'Juz 29 – Al-Insan (Ayat 1–31)',
                'Juz 29 – Al-Mursalat (Ayat 1–50)',
            ],
            'Juz 1' => [
                'Juz 1 – Al-Fatihah (Ayat 1–7)',
                'Juz 1 – Al-Baqarah (Ayat 1–141)',
            ],
            'Juz 2' => [
                'Juz 2 – Al-Baqarah (Ayat 142–252)',
            ],
            'Juz 3' => [
                'Juz 3 – Al-Baqarah (Ayat 253–286)',
                'Juz 3 – Ali \'Imran (Ayat 1–92)',
            ],
        ];
    }
}
