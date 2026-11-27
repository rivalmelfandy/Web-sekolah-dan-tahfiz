<?php

namespace App\Models;

use App\Enums\NilaiIndeks;
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
     * Sesuai skema DBML: nilai_hafalan (tinyint default 80), tidak ada kolom kualitas/nilai.
     *
     * @var list<string>
     */
    protected $fillable = [
        'siswa_id',
        'ustadz_id',
        'surah',
        'ayat_awal',
        'ayat_akhir',
        'nilai_hafalan',
        'catatan',
        'tanggal_setor',
    ];

    /**
     * Nilai default sebelum disimpan, mengikuti default database.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'nilai_hafalan' => 80,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nilai_hafalan' => 'integer',
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

    /**
     * Murajaah terakhir berdasarkan tanggal (id sebagai penentu urutan saat tanggal sama).
     */
    public function murajaahTerakhir(): HasOne
    {
        return $this->hasOne(Murajaah::class, 'setoran_id')
            ->orderByDesc('tanggal_murajaah')
            ->orderByDesc('id');
    }

    // ── Accessor ────────────────────────────────────────────────────────

    /**
     * Nilai terkini (0–100):
     *   - nilai_murajaah dari murajaah terakhir jika sudah pernah dimurajaah
     *   - nilai_hafalan (setoran awal) jika belum ada murajaah
     */
    public function getNilaiTerkiniAttribute(): int
    {
        return $this->murajaahTerakhir?->nilai_murajaah ?? $this->nilai_hafalan;
    }

    /**
     * Indeks huruf dari nilai terkini (A / AB / B / BC / C / D / E).
     */
    public function getIndeksTerkiniAttribute(): NilaiIndeks
    {
        return NilaiIndeks::dariNilai($this->nilai_terkini);
    }

    /**
     * Indeks huruf dari nilai_hafalan awal (tidak berubah walau ada murajaah).
     */
    public function getIndeksHafalanAttribute(): NilaiIndeks
    {
        return NilaiIndeks::dariNilai($this->nilai_hafalan);
    }
}
