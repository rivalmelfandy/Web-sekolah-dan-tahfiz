<?php

namespace App\Models;

use App\Enums\KualitasLevel;
use Database\Factories\MurajaahFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Murajaah extends Model
{
    /** @use HasFactory<MurajaahFactory> */
    use HasFactory;

    /**
     * Tabel yang diasosiasikan dengan model.
     *
     * @var string
     */
    protected $table = 'murajaah';

    /**
     * Kolom updated_at tidak ada di tabel ini.
     *
     * @var string|null
     */
    public const UPDATED_AT = null;

    /**
     * Atribut yang boleh diisi secara massal.
     * Sesuai struktur SQL terbaru: siswa_id, setoran_id, status_mutqin, surah, ayat_range.
     *
     * @var list<string>
     */
    protected $fillable = [
        'setoran_id',
        'siswa_id',
        'ustadz_id',
        'surah',
        'ayat_range',
        'status_mutqin',
        'catatan',
        'tanggal_murajaah',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status_mutqin' => KualitasLevel::class,
            'tanggal_murajaah' => 'date',
        ];
    }

    // ── Relasi ──────────────────────────────────────────────────────────

    /**
     * Setoran hafalan yang dimurajaah (nullable — murajaah bisa berdiri sendiri).
     */
    public function setoran(): BelongsTo
    {
        return $this->belongsTo(SetoranHafalan::class, 'setoran_id');
    }

    /**
     * Siswa pemilik murajaah ini (langsung via siswa_id).
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    /**
     * Ustadz penilai murajaah. Null jika ustadz sudah dihapus.
     */
    public function ustadz(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ustadz_id');
    }
}
