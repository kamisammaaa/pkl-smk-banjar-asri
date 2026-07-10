<?php

namespace App\Http\Controllers\Pembimbing;

use App\Http\Controllers\Controller;
use App\Models\SiswaProfile;
use App\Models\Absensi;
use App\Models\Jurnal;
use App\Models\PenilaianAkhir;
use App\Helpers\AttendanceHelper;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = SiswaProfile::where('pembimbing_id', auth()->id())
            ->with(['user', 'perusahaan.periodePKL', 'jurusan']);

        // Filter
        if ($request->filled('perusahaan_id')) {
            $query->where('perusahaan_id', $request->perusahaan_id);
        }
        if ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
        }

        $siswaList = $query->get();

        // Hitung statistik untuk setiap siswa
        $laporanData = [];
        foreach ($siswaList as $siswa) {
            // Ambil semua absensi yang sudah diverifikasi
            $absensi = Absensi::where('siswa_user_id', $siswa->user_id)
                ->where('is_verified', true)
                ->get();

            // Hitung breakdown status
            $hadir     = $absensi->where('status', 'hadir')->where('check_in', '<=', '07:30:00')->count();
            $terlambat = $absensi->where('status', 'hadir')->where('check_in', '>', '07:30:00')->count();
            $sakit     = $absensi->where('status', 'sakit')->count();
            $izin      = $absensi->where('status', 'izin')->count();
            $libur     = $absensi->where('status', 'libur')->count();
            $alpha     = $absensi->where('status', 'alpha')->count();
            $totalAbsen = $absensi->count();

            // ✅ Hari aktif = total absensi - libur
            // Libur tidak dihitung dalam pembagi persentase/penilaian
            $hariAktif = $totalAbsen - $libur;

            // Hitung nilai kehadiran berbasis hari aktif
            // Hadir tepat waktu=100, Terlambat=75, Sakit/Izin=50, Alpha=0
            $totalPoin     = ($hadir * 100) + ($terlambat * 75) + (($sakit + $izin) * 50) + ($alpha * 0);
            $nilaiKehadiran = $hariAktif > 0 ? round($totalPoin / $hariAktif) : 0;

            // Persentase kehadiran murni (hadir / hari aktif)
            $persentaseHadir = $hariAktif > 0 ? round((($hadir + $terlambat) / $hariAktif) * 100, 1) : 0;

            // Nilai jurnal
            $jurnalDisetujui = Jurnal::where('siswa_user_id', $siswa->user_id)
                ->where('status', 'disetujui')
                ->get();

            $nilaiJurnal = $jurnalDisetujui->avg('nilai') ?? 0;

            // Nilai akhir jika ada
            $penilaian = PenilaianAkhir::where('siswa_user_id', $siswa->user_id)
                ->where('pembimbing_id', auth()->id())
                ->first();

            $laporanData[] = [
                'siswa'            => $siswa,
                // Absensi breakdown
                'hadir'            => $hadir,
                'terlambat'        => $terlambat,
                'sakit'            => $sakit,
                'izin'             => $izin,
                'libur'            => $libur,
                'alpha'            => $alpha,
                'total_absen'      => $totalAbsen,
                'hari_aktif'       => $hariAktif,
                'persentase_hadir' => $persentaseHadir,
                'nilai_kehadiran'  => $nilaiKehadiran,
                // Jurnal
                'nilai_jurnal'     => round($nilaiJurnal),
                'total_jurnal'     => $jurnalDisetujui->count(),
                // Nilai akhir
                'nilai_akhir'      => $penilaian?->nilai_akhir ?? null,
                'grade'            => $penilaian?->grade ?? '-',
            ];
        }

        $perusahaanList = \App\Models\Perusahaan::distinct()->pluck('nama', 'id');
        $jurusanList    = \App\Models\Jurusan::distinct()->pluck('nama', 'id');

        return view('pembimbing.laporan', compact('laporanData', 'perusahaanList', 'jurusanList'));
    }

    public function export(Request $request)
    {
        $query = SiswaProfile::where('pembimbing_id', auth()->id())
            ->with(['user', 'perusahaan.periodePKL', 'jurusan']);

        if ($request->filled('perusahaan_id')) {
            $query->where('perusahaan_id', $request->perusahaan_id);
        }
        if ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
        }

        $siswaList = $query->get();

        // ⚡ Ambil semua absensi & jurnal sekaligus
        $siswaIds   = $siswaList->pluck('user_id')->toArray();
        $allAbsensi = Absensi::whereIn('siswa_user_id', $siswaIds)
            ->where('is_verified', true)
            ->get()
            ->groupBy('siswa_user_id');
        $allJurnal  = Jurnal::whereIn('siswa_user_id', $siswaIds)
            ->where('status', 'disetujui')
            ->get()
            ->groupBy('siswa_user_id');
        $allNilai   = \App\Models\PenilaianAkhir::whereIn('siswa_user_id', $siswaIds)
            ->where('pembimbing_id', auth()->id())
            ->get()
            ->keyBy('siswa_user_id');

        $fileName = 'laporan_siswa_binaan_' . now()->format('Y-m-d') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
            'Pragma'              => 'no-cache',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($siswaList, $allAbsensi, $allJurnal, $allNilai) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8

            fputcsv($file, [
                'No', 'Nama Siswa', 'NIS', 'Jurusan', 'Perusahaan PKL',
                'Hadir', 'Terlambat', 'Sakit', 'Izin', 'Libur', 'Alpha',
                'Total Absen', '% Hadir', 'Nilai Kehadiran',
                'Total Jurnal', 'Nilai Jurnal', 'Nilai Akhir', 'Grade',
            ]);

            $no = 1;
            foreach ($siswaList as $siswa) {
                $absensi = $allAbsensi->get($siswa->user_id, collect());

                $hadir     = $absensi->where('status', 'hadir')->where('check_in', '<=', '07:30:00')->count();
                $terlambat = $absensi->where('status', 'hadir')->where('check_in', '>', '07:30:00')->count();
                $sakit     = $absensi->where('status', 'sakit')->count();
                $izin      = $absensi->where('status', 'izin')->count();
                $libur     = $absensi->where('status', 'libur')->count();
                $alpha     = $absensi->where('status', 'alpha')->count();
                $totalAbsen = $absensi->count();
                $hariAktif = $totalAbsen - $libur;
                $persentase = $hariAktif > 0 ? round((($hadir + $terlambat) / $hariAktif) * 100, 1) : 0;
                $totalPoin  = ($hadir * 100) + ($terlambat * 75) + (($sakit + $izin) * 50);
                $nilaiKehadiran = $hariAktif > 0 ? round($totalPoin / $hariAktif) : 0;

                $jurnalList  = $allJurnal->get($siswa->user_id, collect());
                $nilaiJurnal = round($jurnalList->avg('nilai') ?? 0);

                $penilaian  = $allNilai->get($siswa->user_id);
                $nilaiAkhir = $penilaian?->nilai_akhir ?? '-';
                $grade      = $penilaian?->grade ?? '-';

                fputcsv($file, [
                    $no++,
                    $siswa->user->name ?? '-',
                    $siswa->nis ?? '-',
                    $siswa->jurusan?->nama ?? '-',
                    $siswa->perusahaan?->nama ?? '-',
                    $hadir, $terlambat, $sakit, $izin, $libur, $alpha,
                    $totalAbsen, "{$persentase}%", $nilaiKehadiran,
                    $jurnalList->count(), $nilaiJurnal, $nilaiAkhir, $grade,
                ]);
            }

            fclose($file);
        }, 200, $headers);
    }
}