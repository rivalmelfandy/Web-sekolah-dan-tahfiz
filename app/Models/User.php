<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Tabel yang diasosiasikan dengan model.
     *
     * @var string
     */
    protected $table = 'users';
    public const UPDATED_AT = null;

    /**
     * Atribut yang boleh diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nip_nis',
        'password',
        'nama_lengkap',
        'no_hp',
        'role',
        'halaqah_id',
    ];

    /**
     * Atribut yang disembunyikan dari serialisasi.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => UserRole::class,
        ];
    }

    // ── Relasi sebagai siswa ────────────────────────────────────────

    /**
     * Halaqah tempat siswa ini terdaftar (hanya untuk role siswa).
     */
    public function halaqah(): BelongsTo
    {
        return $this->belongsTo(Halaqah::class, 'halaqah_id');
    }

    /**
     * Setoran hafalan milik siswa ini.
     */
    public function setoranHafalan(): HasMany
    {
        return $this->hasMany(SetoranHafalan::class, 'siswa_id');
    }

    /**
     * Target hafalan milik siswa ini.
     */
    public function targetHafalan(): HasMany
    {
        return $this->hasMany(TargetHafalan::class, 'siswa_id');
    }

    // ── Relasi sebagai ustadz ───────────────────────────────────────

    /**
     * Halaqah yang dibimbing ustadz ini.
     */
    public function halaqahDibimbing(): HasMany
    {
        return $this->hasMany(Halaqah::class, 'ustadz_id');
    }

    /**
     * Setoran hafalan yang diterima (diverifikasi) ustadz ini.
     */
    public function setoranDiterima(): HasMany
    {
        return $this->hasMany(SetoranHafalan::class, 'ustadz_id');
    }

    /**
     * Murajaah yang dinilai ustadz ini.
     */
    public function murajaahDinilai(): HasMany
    {
        return $this->hasMany(Murajaah::class, 'ustadz_id');
    }

    /**
     * Target hafalan yang ditetapkan ustadz ini.
     */
    public function targetDitetapkan(): HasMany
    {
        return $this->hasMany(TargetHafalan::class, 'ustadz_id');
    }

    // ── Pemeriksaan peran ────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isUstadz(): bool
    {
        return $this->role === UserRole::Ustadz;
    }

    public function isSiswa(): bool
    {
        return $this->role === UserRole::Siswa;
    }
}
