<?php

namespace App\Services;

use Illuminate\Support\Collection;

class PenilaianService
{
    public function calculateNilaiJurnal(Collection $jurnalDisetujui, int $hariAktif = 0): int
    {
        if ($hariAktif <= 0) {
            return 0;
        }

        if ($jurnalDisetujui->isEmpty()) {
            return 0;
        }

        $nilai = $jurnalDisetujui->avg('nilai');
        $jumlahJurnal = $jurnalDisetujui->count();

        $persentaseKelengkapan = min(100, round(($jumlahJurnal / $hariAktif) * 100));

        return (int) round(((float) $nilai * 0.7) + ($persentaseKelengkapan * 0.3));
    }

    public function calculateNilaiAkhir(int $nilaiAbsensi, int $nilaiJurnal, int $nilaiSikap): int
    {
        return (int) round((0.3 * $nilaiAbsensi) + (0.4 * $nilaiJurnal) + (0.3 * $nilaiSikap));
    }

    public function getGrade(int $nilai): string
    {
        if ($nilai >= 90) {
            return 'A';
        }

        if ($nilai >= 80) {
            return 'B';
        }

        if ($nilai >= 70) {
            return 'C';
        }

        if ($nilai >= 60) {
            return 'D';
        }

        return 'E';
    }
}
