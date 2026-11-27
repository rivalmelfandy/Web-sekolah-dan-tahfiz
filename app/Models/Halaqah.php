<?php

namespace App\Models;

use Database\Factories\HalaqahFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Halaqah extends Model
{
    /** @use HasFactory<HalaqahFactory> */
    use HasFactory;

    /**
     * Tabel yang diasosiasikan dengan model.
     *
     * @var string
     */
    protected $table = 'halaqah';

    /**
     * Atribut yang boleh diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'kode_halaqah',
        'nama_halaqah',
        'ustadz_id',
        'target_juz',
    ];

    /**
     * Nilai default yang diasumsikan model sebelum disimpan.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'target_juz' => 'Juz 29 - 30',
    ];

    /**
     * Ustadz pembimbing halaqah ini.
     */
    public function ustadz(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ustadz_id');
    }

    /**
     * Siswa-siswa yang tergabung dalam halaqah ini.
     */
    public function siswa(): HasMany
    {
        return $this->hasMany(User::class, 'halaqah_id');
    }

    /**
     * Setoran hafalan dari seluruh siswa dalam halaqah ini.
     */
    public function setoranHafalan(): HasManyThrough
    {
        return $this->hasManyThrough(SetoranHafalan::class, User::class, 'halaqah_id', 'siswa_id');
    }
}
