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
     * Tabel yang diasosiasikan dengan model (nama DBML, bukan jamak default Eloquent).
     *
     * @var string
     */
    protected $table = 'murajaah';

    /**
     * Atribut yang boleh diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'setoran_id',
        'ustadz_id',
        'ayat_range',
        'status_mutqin',
        'catatan',
        'tanggal_murajaah',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status_mutqin' => KualitasLevel::class,
            'tanggal_murajaah' => 'date',
        ];
    }

    /**
     * Setoran hafalan yang dimurajaah.
     */
    public function setoran(): BelongsTo
    {
        return $this->belongsTo(SetoranHafalan::class, 'setoran_id');
    }

    /**
     * Ustadz penilai murajaah. Null jika ustadz sudah dihapus.
     */
    public function ustadz(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ustadz_id');
    }
}
