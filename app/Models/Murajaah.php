<?php

namespace App\Models;

use App\Enums\NilaiIndeks;
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
     * Sesuai skema DBML: setoran_id FK ke setoran_hafalan, nilai_murajaah (tinyint default 80).
     * TIDAK ada siswa_id, surah, atau status_mutqin.
     *
     * @var list<string>
     */
    protected $fillable = [
        'setoran_id',
        'ustadz_id',
        'ayat_range',
        'nilai_murajaah',
        'catatan',
        'tanggal_murajaah',
    ];

    /**
     * Nilai default sebelum disimpan, mengikuti default database.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'nilai_murajaah' => 80,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nilai_murajaah' => 'integer',
            'tanggal_murajaah' => 'date',
        ];
    }

    // ── Relasi ──────────────────────────────────────────────────────────

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

    // ── Accessor ────────────────────────────────────────────────────────

    /**
     * Indeks huruf dari nilai_murajaah (A / AB / B / BC / C / D / E).
     */
    public function getIndeksMurajaahAttribute(): NilaiIndeks
    {
        return NilaiIndeks::dariNilai($this->nilai_murajaah);
    }
}
