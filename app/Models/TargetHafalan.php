<?php

namespace App\Models;

use App\Enums\TargetStatus;
use Database\Factories\TargetHafalanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TargetHafalan extends Model
{
    /** @use HasFactory<TargetHafalanFactory> */
    use HasFactory;

    /**
     * Tabel yang diasosiasikan dengan model (nama DBML, bukan jamak default Eloquent).
     *
     * @var string
     */
    protected $table = 'target_hafalan';

    /**
     * Kolom updated_at tidak ada di tabel ini.
     *
     * @var string|null
     */
    public const UPDATED_AT = null;

    /**
     * Atribut yang boleh diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'siswa_id',
        'ustadz_id',
        'surah_awal',
        'surah_akhir',
        'tanggal_mulai',
        'tanggal_akhir',
        'status',
    ];

    /**
     * Nilai default yang diasumsikan model sebelum disimpan, mengikuti default database.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'Aktif',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => TargetStatus::class,
            'tanggal_mulai' => 'date',
            'tanggal_akhir' => 'date',
        ];
    }

    /**
     * Siswa pemilik target ini.
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    /**
     * Ustadz yang menetapkan target. Null jika ustadz sudah dihapus.
     */
    public function ustadz(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ustadz_id');
    }
}
