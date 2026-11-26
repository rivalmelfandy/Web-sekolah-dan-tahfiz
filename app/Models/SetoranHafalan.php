<?php

namespace App\Models;

use App\Enums\KualitasLevel;
use Database\Factories\SetoranHafalanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SetoranHafalan extends Model
{
    /** @use HasFactory<SetoranHafalanFactory> */
    use HasFactory;

    /**
     * Tabel yang diasosiasikan dengan model.
     *
     * @var string
     */
    protected $table = 'setoran_hafalan';

    /**
     * Kolom updated_at tidak ada di tabel ini.
     *
     * @var string|null
     */
    public const UPDATED_AT = null;

    /**
     * Atribut yang boleh diisi secara massal.
     * Sesuai struktur SQL terbaru: tidak ada nilai_hafalan.
     *
     * @var list<string>
     */
    protected $fillable = [
        'siswa_id',
        'ustadz_id',
        'surah',
        'ayat_awal',
        'ayat_akhir',
        'kualitas',
        'catatan',
        'tanggal_setor',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kualitas' => KualitasLevel::class,
            'ayat_awal' => 'integer',
            'ayat_akhir' => 'integer',
            'tanggal_setor' => 'date',
        ];
    }

    // ── Relasi ──────────────────────────────────────────────────────────

    /**
     * Siswa pemilik setoran ini.
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    /**
     * Ustadz penerima setoran. Null jika ustadz sudah dihapus.
     */
    public function ustadz(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ustadz_id');
    }

    /**
     * Seluruh murajaah atas setoran ini (satu setoran bisa dimurajaah berkali-kali).
     */
    public function murajaah(): HasMany
    {
        return $this->hasMany(Murajaah::class, 'setoran_id');
    }
}
