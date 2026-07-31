<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Absensi;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use App\Helpers\AttendanceHelper;
use Carbon\Carbon;

class RekapAbsensiController extends Controller
{
    public function index(Request $request)
    {
        // Generate alpha untuk semua siswa aktif (berdasarkan periode masing-masing)
        AttendanceHelper::generateAlphaForAllActiveSiswa();

        $bulan          = $request->get('bulan', now()->format('Y-m'));
        $pembimbing_id  = $request->get('pembimbing_id');
        $jurusan_id     = $request->get('jurusan_id');
        $filter_masalah = $request->get('filter_masalah');

        $query = User::where('role', 'siswa')
            ->with('siswaProfile.jurusan', 'siswaProfile.pembimbing', 'siswaProfile.perusahaan.periodePKL');

        if ($pembimbing_id) {
            $query->whereHas('siswaProfile', fn($q) => $q->where('pembimbing_id', $pembimbing_id));
        }
        if ($jurusan_id) {
            $query->whereHas('siswaProfile', fn($q) => $q->where('jurusan_id', $jurusan_id));
        }

        $siswaList = $query->get();

        // ⚡ Optimalisasi: Ambil SEMUA absensi bulan ini sekaligus (satu query, bukan N query)
        $siswaIds   = $siswaList->pluck('id')->toArray();
        $allAbsensi = Absensi::whereIn('siswa_user_id', $siswaIds)
            ->where('tanggal', 'like', "{$bulan}%")
            ->get()
            ->groupBy('siswa_user_id');

        $rekap = [];
        foreach ($siswaList as $s) {
            $absenBulan = $allAbsensi->get($s->id, collect());

            $hadirVerified = $absenBulan->where('status', 'hadir')->where('is_verified', true);
            // Gunakan is_late konsisten dengan panel pembimbing
            $hadir      = $hadirVerified->where('is_late', false)->count();
            $terlambat  = $hadirVerified->where('is_late', true)->count();
            $sakit      = $absenBulan->where('status', 'sakit')->where('is_verified', true)->count();
            $izin       = $absenBulan->where('status', 'izin')->where('is_verified', true)->count();
            $libur      = $absenBulan->where('status', 'libur')->where('is_verified', true)->count();
            $alpha      = $absenBulan->where('status', 'alpha')->count();
            $total_hari = $hadir + $terlambat + $sakit + $izin + $libur + $alpha;

            // Hari aktif = total - libur (libur tidak dihitung dalam pembagi persentase)
            $hariAktif  = $total_hari - $libur;
            // Persentase kehadiran: (hadir + terlambat) / hari aktif — konsisten dengan panel pembimbing
            $persentase = $hariAktif > 0 ? round((($hadir + $terlambat) / $hariAktif) * 100, 1) : 0;

            if ($filter_masalah === 'alpha' && $alpha == 0) continue;
            if ($filter_masalah === 'sakit' && $sakit == 0) continue;
            if ($filter_masalah === 'izin' && $izin == 0) continue;
            if ($filter_masalah === 'terlambat' && $terlambat == 0) continue;

            $rekap[] = [
                'siswa'      => $s,
                'hadir'      => $hadir,
                'terlambat'  => $terlambat,
                'sakit'      => $sakit,
                'izin'       => $izin,
                'libur'      => $libur,
                'alpha'      => $alpha,
                'total_hari' => $total_hari,
                'hari_aktif' => $hariAktif,
                'persentase' => $persentase,
            ];
        }

        $pembimbingList = User::where('role', 'pembimbing')->orderBy('name')->get();
        $jurusanList    = Jurusan::orderBy('nama')->get();

        return view('admin.rekap-absensi.index', compact(
            'rekap', 'bulan', 'pembimbingList', 'jurusanList', 'pembimbing_id', 'jurusan_id', 'filter_masalah'
        ));
    }

    /**
     * Export rekap absensi ke CSV (Excel-compatible)
     */
    public function export(Request $request)
    {
        AttendanceHelper::generateAlphaForAllActiveSiswa();

        $bulan          = $request->get('bulan', now()->format('Y-m'));
        $pembimbing_id  = $request->get('pembimbing_id');
        $jurusan_id     = $request->get('jurusan_id');
        $filter_masalah = $request->get('filter_masalah');

        $query = User::where('role', 'siswa')
            ->with('siswaProfile.jurusan', 'siswaProfile.pembimbing', 'siswaProfile.perusahaan.periodePKL');

        if ($pembimbing_id) {
            $query->whereHas('siswaProfile', fn($q) => $q->where('pembimbing_id', $pembimbing_id));
        }
        if ($jurusan_id) {
            $query->whereHas('siswaProfile', fn($q) => $q->where('jurusan_id', $jurusan_id));
        }

        $siswaList = $query->get();

        // ⚡ Optimalisasi: Ambil SEMUA absensi sekaligus (satu query)
        $siswaIds   = $siswaList->pluck('id')->toArray();
        $allAbsensi = Absensi::whereIn('siswa_user_id', $siswaIds)
            ->where('tanggal', 'like', "{$bulan}%")
            ->get()
            ->groupBy('siswa_user_id');

        $filename = "Rekap_Absensi_{$bulan}" . ($pembimbing_id ? "_Pembimbing-{$pembimbing_id}" : '') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
            'Pragma'              => 'no-cache',
            'Expires'             => '0',
        ];

        $callback = function () use ($siswaList, $allAbsensi, $filter_masalah) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'No',
                'Nama Siswa',
                'NIS',
                'Jurusan',
                'Pembimbing',
                'Perusahaan PKL',
                '✅ Hadir (Tepat Waktu)',
                '⏰ Terlambat',
                '🤒 Sakit',
                '📝 Izin',
                '🏖️ Libur',
                '❌ Alpha',
                'Total Hari',
                'Hari Aktif',
                'Persentase Kehadiran',
                'Keterangan',
            ]);

            $no = 1;
            foreach ($siswaList as $s) {
                $absenBulan = $allAbsensi->get($s->id, collect());

                $hadirVerified = $absenBulan->where('status', 'hadir')->where('is_verified', true);
                // Gunakan is_late konsisten dengan panel pembimbing
                $hadir      = $hadirVerified->where('is_late', false)->count();
                $terlambat  = $hadirVerified->where('is_late', true)->count();
                $sakit      = $absenBulan->where('status', 'sakit')->where('is_verified', true)->count();
                $izin       = $absenBulan->where('status', 'izin')->where('is_verified', true)->count();
                $libur      = $absenBulan->where('status', 'libur')->where('is_verified', true)->count();
                $alpha      = $absenBulan->where('status', 'alpha')->count();
                $total      = $hadir + $terlambat + $sakit + $izin + $libur + $alpha;

                $hariAktif  = $total - $libur;
                $persentase = $hariAktif > 0 ? round((($hadir + $terlambat) / $hariAktif) * 100, 1) : 0;

                if ($filter_masalah === 'alpha' && $alpha == 0) continue;
                if ($filter_masalah === 'sakit' && $sakit == 0) continue;
                if ($filter_masalah === 'izin' && $izin == 0) continue;
                if ($filter_masalah === 'terlambat' && $terlambat == 0) continue;

                $keterangan = match(true) {
                    $persentase >= 90 => 'Sangat Baik',
                    $persentase >= 75 => 'Baik',
                    $persentase >= 50 => 'Cukup',
                    $persentase >  0  => 'Kurang',
                    default           => 'Tidak Ada Data',
                };

                fputcsv($file, [
                    $no++,
                    $s->name,
                    $s->siswaProfile?->nis ?? '-',
                    $s->siswaProfile?->jurusan?->nama ?? '-',
                    $s->siswaProfile?->pembimbing?->name ?? '-',
                    $s->siswaProfile?->perusahaan?->nama ?? '-',
                    $hadir,
                    $terlambat,
                    $sakit,
                    $izin,
                    $libur,
                    $alpha,
                    $total,
                    $hariAktif,
                    "{$persentase}%",
                    $keterangan,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export rekap absensi ke PDF
     */
    public function exportPdf(Request $request)
    {
        AttendanceHelper::generateAlphaForAllActiveSiswa();

        $bulan          = $request->get('bulan', now()->format('Y-m'));
        $pembimbing_id  = $request->get('pembimbing_id');
        $jurusan_id     = $request->get('jurusan_id');
        $filter_masalah = $request->get('filter_masalah');

        $query = User::where('role', 'siswa')
            ->with('siswaProfile.jurusan', 'siswaProfile.pembimbing', 'siswaProfile.perusahaan.periodePKL');

        if ($pembimbing_id) {
            $query->whereHas('siswaProfile', fn($q) => $q->where('pembimbing_id', $pembimbing_id));
        }
        if ($jurusan_id) {
            $query->whereHas('siswaProfile', fn($q) => $q->where('jurusan_id', $jurusan_id));
        }

        $siswaList = $query->get();

        $siswaIds   = $siswaList->pluck('id')->toArray();
        $allAbsensi = Absensi::whereIn('siswa_user_id', $siswaIds)
            ->where('tanggal', 'like', "{$bulan}%")
            ->get()
            ->groupBy('siswa_user_id');

        $rekap = [];
        foreach ($siswaList as $s) {
            $absenBulan = $allAbsensi->get($s->id, collect());

            $hadirVerified = $absenBulan->where('status', 'hadir')->where('is_verified', true);
            $hadir      = $hadirVerified->where('is_late', false)->count();
            $terlambat  = $hadirVerified->where('is_late', true)->count();
            $sakit      = $absenBulan->where('status', 'sakit')->where('is_verified', true)->count();
            $izin       = $absenBulan->where('status', 'izin')->where('is_verified', true)->count();
            $libur      = $absenBulan->where('status', 'libur')->where('is_verified', true)->count();
            $alpha      = $absenBulan->where('status', 'alpha')->count();
            $total_hari = $hadir + $terlambat + $sakit + $izin + $libur + $alpha;

            $hariAktif  = $total_hari - $libur;
            $persentase = $hariAktif > 0 ? round((($hadir + $terlambat) / $hariAktif) * 100, 1) : 0;

            if ($filter_masalah === 'alpha' && $alpha == 0) continue;
            if ($filter_masalah === 'sakit' && $sakit == 0) continue;
            if ($filter_masalah === 'izin' && $izin == 0) continue;
            if ($filter_masalah === 'terlambat' && $terlambat == 0) continue;

            $rekap[] = [
                'siswa'      => $s,
                'hadir'      => $hadir,
                'terlambat'  => $terlambat,
                'sakit'      => $sakit,
                'izin'       => $izin,
                'libur'      => $libur,
                'alpha'      => $alpha,
                'total_hari' => $total_hari,
                'hari_aktif' => $hariAktif,
                'persentase' => $persentase,
            ];
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.rekap-absensi.pdf', compact(
            'rekap', 'bulan', 'request'
        ));
        
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('Rekap_Absensi_' . $bulan . '.pdf');
    }
}