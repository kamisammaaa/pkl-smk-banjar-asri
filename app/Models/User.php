<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // =================================================================
    // 🔗 RELATIONSHIPS
    // =================================================================

    /**
     * User has one SiswaProfile (khusus role: siswa)
     * Mapping: users.id → siswa_profiles.user_id
     */
    public function siswaProfile()
    {
        return $this->hasOne(SiswaProfile::class, 'user_id');
    }

    /**
     * User has many Perusahaan (khusus role: pembimbing)
     * Mapping: users.id → perusahaan.pembimbing_id
     */
    public function perusahaan()
    {
        return $this->hasMany(Perusahaan::class, 'pembimbing_id');
    }

    /**
     * User has many SiswaProfile sebagai pembimbing (daftar siswa binaan)
     * Mapping: users.id → siswa_profiles.pembimbing_id
     */
    public function siswaBinaan()
    {
        return $this->hasMany(SiswaProfile::class, 'pembimbing_id');
    }

    /**
     * User has many Absensi (via siswa_user_id)
     * Untuk siswa: riwayat absensi sendiri
     * Untuk pembimbing/admin: akses via relasi lain
     */
    public function absensis()
    {
        return $this->hasMany(Absensi::class, 'siswa_user_id');
    }

    /**
     * User has many Jurnal (via siswa_user_id)
     */
    public function jurnals()
    {
        return $this->hasMany(Jurnal::class, 'siswa_user_id');
    }

    /**
     * User has many EOTag (via siswa_user_id)
     */
    public function eotags()
    {
        return $this->hasMany(EOTag::class, 'siswa_user_id');
    }

    /**
     * User has many Kunjungan sebagai pembimbing
     */
    public function kunjungans()
    {
        return $this->hasMany(Kunjungan::class, 'pembimbing_id');
    }

    /**
     * User has many Penilaian sebagai pembimbing
     */
    public function penilaians()
    {
        return $this->hasMany(Penilaian::class, 'pembimbing_id');
    }

    /**
     * User has many Pengumuman sebagai admin (pembuat)
     */
    public function pengumumans()
    {
        return $this->hasMany(Pengumuman::class, 'admin_id');
    }

    // =================================================================
    // 🔍 HELPERS / SCOPES
    // =================================================================

    /**
     * Scope: Hanya user dengan role tertentu
     * Usage: User::role('siswa')->get();
     */
    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope: Hanya user aktif
     * Usage: User::active()->get();
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Helper: Cek apakah user adalah siswa
     */
    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    /**
     * Helper: Cek apakah user adalah pembimbing
     */
    public function isPembimbing(): bool
    {
        return $this->role === 'pembimbing';
    }

    /**
     * Helper: Cek apakah user adalah admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Helper: Get nama role dalam format human-readable
     */
    public function getRoleNameAttribute(): string
    {
        return match($this->role) {
            'admin' => 'Administrator',
            'pembimbing' => 'Pembimbing',
            'siswa' => 'Siswa',
            default => ucfirst($this->role),
        };
    }
}