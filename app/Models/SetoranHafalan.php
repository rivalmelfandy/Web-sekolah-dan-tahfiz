<?php

namespace App\Models;

use App\Enums\KualitasLevel;
use Database\Factories\SetoranHafalanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SetoranHafalan extends Model
{
    /** @use HasFactory<SetoranHafalanFactory> */
    use HasFactory;

    /**
     * Tabel yang diasosiasikan dengan model (nama DBML, bukan jamak default Eloquent).
     *
     * @var string
     */
    protected $table = 'setoran_hafalan';

    /**
     * Atribut yang boleh diisi secara massal.
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
        'nilai',
        'catatan',
        'tanggal_setor',
    ];

    /**
     * Nilai default yang diasumsikan model sebelum disimpan, mengikuti default database.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'nilai' => 85,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kualitas' => KualitasLevel::class,
            'nilai' => 'integer',
            'tanggal_setor' => 'date',
        ];
    }

    /**
     * Siswa pemilik setoran ini.
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    /**
     * Ustadz penerima (verifikator) setoran ini. Null jika ustadz sudah dihapus.
     */
    public function ustadz(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ustadz_id');
    }

    /**
     * Seluruh murajaah atas setoran ini (bisa berkali-kali).
     */
    public function murajaah(): HasMany
    {
        return $this->hasMany(Murajaah::class, 'setoran_id');
    }

    /**
     * Murajaah terakhir berdasarkan tanggal (dan id sebagai penentu urutan).
     */
    public function murajaahTerakhir(): HasOne
    {
        return $this->hasOne(Murajaah::class, 'setoran_id')
            ->orderByDesc('tanggal_murajaah')
            ->orderByDesc('id');
    }

    /**
     * Kualitas terkini: status_mutqin dari murajaah terakhir jika ada,
     * atau kembali ke kualitas awal (kolom kualitas) bila belum ada murajaah.
     */
    public function getKualitasTerkiniAttribute(): KualitasLevel
    {
        return $this->murajaahTerakhir?->status_mutqin ?? $this->kualitas;
    }
}
