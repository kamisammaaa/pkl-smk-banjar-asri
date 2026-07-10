<?php

namespace App\Helpers;

use App\Models\Absensi;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AttendanceHelper
{
    /**
     * Generate alpha records for a specific student (siswa) for missing days.
     * 
     * Periode PKL diambil dari: User → SiswaProfile → Perusahaan → PeriodePKL
     * sehingga setiap siswa menggunakan periode PKL perusahaannya masing-masing.
     * Alpha HANYA digenerate jika:
     *  - Siswa memiliki profil dan perusahaan yang dikaitkan periode PKL
     *  - Periode PKL is_active = true
     *  - Hari ini sudah melewati/sama dengan tanggal_mulai periode
     */
    public static function generateAlphaForSiswa($siswaId)
    {
        $user = User::with('siswaProfile.perusahaan.periodePKL')->find($siswaId);

        if (!$user || $user->role !== 'siswa' || !$user->is_active) {
            return;
        }

        // Ambil periode PKL dari perusahaan siswa
        $profile    = $user->siswaProfile;
        $perusahaan = $profile?->perusahaan;
        $periode    = $perusahaan?->periodePKL;

        // Harus ada periode yang aktif
        if (!$periode || !$periode->is_active) {
            return;
        }

        $today     = Carbon::today();
        $startDate = Carbon::parse($periode->tanggal_mulai)->startOfDay();
        $endDate   = Carbon::parse($periode->tanggal_selesai)->endOfDay();

        // Jika hari ini belum mencapai tanggal mulai → jangan generate alpha
        if ($today->lt($startDate)) {
            return;
        }

        // Batas akhir generate alpha: kemarin atau akhir periode (mana yang lebih dulu)
        $alphaUntil = Carbon::yesterday();
        if ($endDate->lt($alphaUntil)) {
            $alphaUntil = $endDate->copy()->startOfDay();
        }

        // Jika startDate sudah melewati alphaUntil → tidak ada apa-apa untuk di-generate
        if ($startDate->gt($alphaUntil)) {
            return;
        }

        // Ambil tanggal yang sudah ada record absensi dalam rentang ini
        $existingDates = Absensi::where('siswa_user_id', $siswaId)
            ->whereBetween('tanggal', [
                $startDate->format('Y-m-d'),
                $alphaUntil->format('Y-m-d'),
            ])
            ->pluck('tanggal')
            ->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))
            ->toArray();

        // Iterasi setiap hari dalam rentang, generate alpha jika belum ada record
        $period    = CarbonPeriod::create($startDate, $alphaUntil);
        $alphaData = [];

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');

            // Lewati hari Minggu
            if ($date->dayOfWeek === Carbon::SUNDAY) {
                continue;
            }

            // Buat alpha jika belum ada record absensi di hari tersebut
            if (!in_array($dateStr, $existingDates)) {
                $alphaData[] = [
                    'siswa_user_id' => $siswaId,
                    'tanggal'       => $dateStr,
                    'status'        => 'alpha',
                    'is_verified'   => true, // alpha otomatis langsung verified
                    'lokasi_nama'   => 'Tidak mengisi absensi (Otomatis Alpa)',
                    'check_in'      => null,
                    'check_out'     => null,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }
        }

        if (!empty($alphaData)) {
            Absensi::insert($alphaData);
        }
    }

    /**
     * Generate alpha records for all active students.
     * Masing-masing siswa menggunakan periode PKL perusahaannya sendiri.
     */
    public static function generateAlphaForAllActiveSiswa()
    {
        $activeSiswaIds = User::where('role', 'siswa')
            ->where('is_active', true)
            ->pluck('id');

        foreach ($activeSiswaIds as $id) {
            self::generateAlphaForSiswa($id);
        }
    }

    /**
     * Hitung jumlah hari aktif (non-Minggu) dalam rentang periode PKL siswa.
     * Digunakan sebagai pembagi persentase kehadiran agar tidak menggunakan
     * total hari kalender penuh yang bisa membuat persentase tidak akurat.
     *
     * "Hari aktif" = hari kerja (Senin–Sabtu) dalam periode PKL yang sudah berlalu
     * (dari tanggal_mulai hingga kemarin atau tanggal_selesai, mana yang lebih dulu).
     */
    public static function hitungHariAktif($siswaId): int
    {
        $user    = User::with('siswaProfile.perusahaan.periodePKL')->find($siswaId);
        $periode = $user?->siswaProfile?->perusahaan?->periodePKL;

        if (!$periode) {
            return 0;
        }

        $today     = Carbon::today();
        $startDate = Carbon::parse($periode->tanggal_mulai)->startOfDay();
        $endDate   = Carbon::parse($periode->tanggal_selesai)->startOfDay();

        // Jika periode belum mulai
        if ($today->lt($startDate)) {
            return 0;
        }

        // Batas hitung: kemarin atau akhir periode (mana yang lebih dulu)
        $countUntil = Carbon::yesterday();
        if ($endDate->lt($countUntil)) {
            $countUntil = $endDate;
        }

        if ($startDate->gt($countUntil)) {
            return 0;
        }

        $count  = 0;
        $period = CarbonPeriod::create($startDate, $countUntil);
        foreach ($period as $date) {
            if ($date->dayOfWeek !== Carbon::SUNDAY) {
                $count++;
            }
        }

        return $count;
    }
}
