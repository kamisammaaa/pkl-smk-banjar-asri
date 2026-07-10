<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $fillable = [
        'siswa_user_id',
        'tanggal',
        'check_in',
        'check_out',
        'latitude',
        'longitude',
        'lokasi_nama',
        'foto',
        'ip_address',
        'eotag_token',
        'is_verified',
        'status',            // hadir, sakit, izin, libur, alpha
        'alasan',            // keterangan teks
        'bukti_file',        // path file upload
        'keterangan_status', // legacy/optional
        'verified_by',
        'verified_at',
        'is_late',           // apakah terlambat (boolean)
        'terlambat_menit',   // berapa menit terlambat dari batas toleransi
    ];

    protected $casts = [
        'is_verified'     => 'boolean',
        'is_late'         => 'boolean',
        'terlambat_menit' => 'integer',
        'tanggal'         => 'date',
        'verified_at'     => 'datetime',
        'check_in'        => 'datetime:H:i:s',
        'check_out'       => 'datetime:H:i:s',
    ];

    // 🔗 Relasi ke Siswa
    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_user_id');
    }

    // 🔗 Relasi ke Pembimbing yang verifikasi
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // ✅ Helper: Opsi Status yang bisa diinput siswa (alpha hanya dari sistem)
    public static function getStatusOptions(): array
    {
        return [
            'hadir' => '✅ Hadir',
            'sakit' => '🤒 Sakit',
            'izin'  => '📝 Izin',
            'libur' => '🏖️ Libur',
            'alpha' => '❌ Alpha',
        ];
    }

    // ✅ Opsi yang bisa diinput oleh siswa sendiri (tidak termasuk alpha)
    public static function getStatusInputOptions(): array
    {
        return [
            'hadir' => '✅ Hadir',
            'sakit' => '🤒 Sakit',
            'izin'  => '📝 Izin',
            'libur' => '🏖️ Libur',
        ];
    }

    // ✅ Helper: Badge Class untuk UI (Tailwind)
    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'hadir' => 'bg-green-100 text-green-800 border-green-200',
            'sakit' => 'bg-orange-100 text-orange-800 border-orange-200',
            'izin'  => 'bg-blue-100 text-blue-800 border-blue-200',
            'libur' => 'bg-purple-100 text-purple-800 border-purple-200',
            'alpha' => 'bg-red-100 text-red-800 border-red-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }

    // ✅ Helper: Label Status
    public function getStatusLabel(): string
    {
        return self::getStatusOptions()[$this->status] ?? ucfirst($this->status);
    }

    // ✅ Helper: Label Keterlambatan
    public function getLateLabel(): ?string
    {
        if (!$this->is_late || $this->status !== 'hadir') {
            return null;
        }
        $menit = $this->terlambat_menit ?? 0;
        return $menit > 0 ? "Terlambat {$menit} mnt" : 'Terlambat';
    }

    // ✅ Helper: Badge class untuk keterlambatan
    public function getLateBadgeClass(): string
    {
        return 'bg-yellow-100 text-yellow-800 border-yellow-200';
    }

    /**
     * Apakah status ini dihitung sebagai "hari aktif" untuk persentase kehadiran?
     * Libur TIDAK dihitung sebagai hari aktif (tidak mempengaruhi pembagi).
     * Alpha, Sakit, Izin dihitung sebagai hari aktif tapi bukan hadir.
     */
    public function isHariAktif(): bool
    {
        return in_array($this->status, ['hadir', 'sakit', 'izin', 'alpha']);
    }

    /**
     * Apakah status ini butuh persetujuan pembimbing?
     * Alpha (otomatis sistem) tidak perlu disetujui.
     */
    public function needsApproval(): bool
    {
        return !$this->is_verified && $this->status !== 'alpha';
    }
}