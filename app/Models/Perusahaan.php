<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Perusahaan extends Model
{
    // 🔥 Paksa nama tabel singular sesuai migration
    protected $table = 'perusahaan';
    protected $fillable = [
        'nama',
        'alamat',
        'kontak',
        'pembimbing_id',
        'periode_pkl_id',
        'jam_masuk',         // Jam masuk wajib (HH:MM)
        'toleransi_menit',   // Toleransi keterlambatan dalam menit
    ];

    protected $casts = [
        'toleransi_menit' => 'integer',
    ];

    public function pembimbing()
    {
        return $this->belongsTo(User::class, 'pembimbing_id');
    }

    public function periodePKL()
    {
        return $this->belongsTo(PeriodePKL::class, 'periode_pkl_id');
    }

    public function siswaProfiles()
    {
        return $this->hasMany(SiswaProfile::class, 'perusahaan_id')->with('user');
    }

    /**
     * Dapatkan jam masuk sebagai Carbon (menggunakan tanggal hari ini).
     */
    public function getJamMasukCarbon(): Carbon
    {
        $jam = $this->jam_masuk ?? '07:30:00';
        return Carbon::today()->setTimeFromTimeString($jam);
    }

    /**
     * Cek apakah waktu check-in terlambat (melewati jam_masuk + toleransi_menit).
     *
     * @param Carbon|string $checkInTime
     */
    public function isLate($checkInTime): bool
    {
        if (!$checkInTime) return false;

        $checkIn  = $checkInTime instanceof Carbon ? $checkInTime : Carbon::parse($checkInTime);
        $deadline = $this->getJamMasukCarbon()->addMinutes($this->toleransi_menit ?? 15);

        return $checkIn->gt($deadline);
    }

    /**
     * Hitung berapa menit keterlambatan dari batas toleransi.
     * Mengembalikan 0 jika tidak terlambat.
     *
     * @param Carbon|string $checkInTime
     */
    public function meniTerlambat($checkInTime): int
    {
        if (!$checkInTime) return 0;

        $checkIn  = $checkInTime instanceof Carbon ? $checkInTime : Carbon::parse($checkInTime);
        $deadline = $this->getJamMasukCarbon()->addMinutes($this->toleransi_menit ?? 15);

        if ($checkIn->lte($deadline)) return 0;

        return (int) $deadline->diffInMinutes($checkIn);
    }

    /**
     * Dapatkan label jam masuk yang mudah dibaca (misal: "07:30").
     */
    public function getJamMasukLabel(): string
    {
        $jam = $this->jam_masuk ?? '07:30:00';
        return Carbon::today()->setTimeFromTimeString($jam)->format('H:i');
    }
}