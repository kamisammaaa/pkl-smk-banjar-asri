<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kunjungan;
use App\Models\User;
use App\Models\Perusahaan;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    /**
     * Dashboard admin (stats overview & charts).
     */
    public function dashboard()
    {
        // 1. Basic Stats
        $stats = [
            'total_siswa' => User::where('role', 'siswa')->count(),
            'total_pembimbing' => User::where('role', 'pembimbing')->count(),
            'total_perusahaan' => Perusahaan::count(),
            'absensi_hari_ini' => \App\Models\Absensi::whereDate('tanggal', today())->count(),
            'kunjungan_bulan_ini' => Kunjungan::whereMonth('created_at', now()->month)->count(),
        ];
        
        // 2. Alert Data: Unassigned Students
        $siswaUnassignedPembimbing = User::where('role', 'siswa')
            ->where(function($q) {
                $q->whereDoesntHave('siswaProfile')
                  ->orWhereHas('siswaProfile', fn($sq) => $sq->whereNull('pembimbing_id'));
            })->count();
            
        $siswaUnassignedPerusahaan = User::where('role', 'siswa')
            ->where(function($q) {
                $q->whereDoesntHave('siswaProfile')
                  ->orWhereHas('siswaProfile', fn($sq) => $sq->whereNull('perusahaan_id'));
            })->count();
            
        // 3. Chart Data: Distribusi per Jurusan
        $jurusanDist = \App\Models\SiswaProfile::with('jurusan')
            ->selectRaw('jurusan_id, count(*) as total')
            ->whereNotNull('jurusan_id')
            ->groupBy('jurusan_id')
            ->get();
            
        $chartJurusan = [
            'labels' => $jurusanDist->pluck('jurusan.nama')->toArray(),
            'data' => $jurusanDist->pluck('total')->toArray(),
        ];

        return view('admin.dashboard', compact(
            'stats', 
            'siswaUnassignedPembimbing', 
            'siswaUnassignedPerusahaan',
            'chartJurusan'
        ));
    }

    /**
     * 🔥 FIX: Monitoring kunjungan dengan semua data yang dibutuhkan view.
     */
    public function kunjungan(Request $request)
    {
        $query = Kunjungan::with(['pembimbing', 'perusahaan.siswaProfiles.user']);

        // Filter by pembimbing
        if ($request->filled('pembimbing_id')) {
            $query->where('pembimbing_id', $request->pembimbing_id);
        }

        // Filter by perusahaan
        if ($request->filled('perusahaan_id')) {
            $query->where('perusahaan_id', $request->perusahaan_id);
        }

        // Filter by tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $kunjungans = $query->latest()->paginate(15);

        // 🔥 PASTIKAN SEMUA VARIABEL INI DIKIRIM KE VIEW:
        
        // 1. List pembimbing untuk dropdown filter
        $pembimbingList = User::where('role', 'pembimbing')
            ->orderBy('name')
            ->pluck('name', 'id');
        
        // 2. List perusahaan untuk dropdown filter
        $perusahaanList = Perusahaan::orderBy('nama')
            ->pluck('nama', 'id');
        
        // 3. Stats cards
        $perusahaanIdsWithKunjungan = Kunjungan::distinct('perusahaan_id')->pluck('perusahaan_id');
        $siswaDikunjungi = \App\Models\SiswaProfile::whereIn('perusahaan_id', $perusahaanIdsWithKunjungan)
            ->distinct('user_id')
            ->count('user_id');

        $stats = [
            'bulan_ini' => Kunjungan::whereMonth('created_at', now()->month)->count(),
            'siswa_dikunjungi' => $siswaDikunjungi,
            'perusahaan' => Kunjungan::distinct('perusahaan_id')->count('perusahaan_id'),
        ];

        return view('admin.monitoring.kunjungan', compact(
            'kunjungans',
            'pembimbingList',      // ← WAJIB ADA!
            'perusahaanList',      // ← WAJIB ADA!
            'stats'                // ← WAJIB ADA!
        ));
    }

    /**
     * Monitoring verifikasi absensi.
     */
    public function verifikasi(Request $request)
    {
        // Query absensi yang BELUM diverifikasi dan BUKAN alpha (alpha otomatis sudah verified)
        $query = \App\Models\Absensi::with('siswa.siswaProfile')
            ->where('is_verified', false)
            ->where('status', '!=', 'alpha');

        // Filter by siswa
        if ($request->filled('siswa_id')) {
            $query->where('siswa_user_id', $request->siswa_id);
        }

        // Filter by tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        // Absensi belum diverifikasi
        $absensiBelumVerified = $query->latest()->paginate(15);

        // Absensi sudah verified (untuk tab "Sudah Diverifikasi")
        $absensiVerifiedQuery = \App\Models\Absensi::with('siswa.siswaProfile')
            ->where('is_verified', true);

        if ($request->filled('siswa_id')) {
            $absensiVerifiedQuery->where('siswa_user_id', $request->siswa_id);
        }
        if ($request->filled('tanggal')) {
            $absensiVerifiedQuery->whereDate('tanggal', $request->tanggal);
        }
        $absensiVerified = $absensiVerifiedQuery->latest()->paginate(15, ['*'], 'verified_page');

        // List siswa untuk dropdown filter
        $siswaList = \App\Models\User::where('role', 'siswa')
            ->orderBy('name')
            ->pluck('name', 'id');

        // Stats
        $stats = [
            'belum_verified' => \App\Models\Absensi::where('is_verified', false)
                ->where('status', '!=', 'alpha')
                ->count(),
            'sudah_verified' => \App\Models\Absensi::where('is_verified', true)->count(),
            'hari_ini'       => \App\Models\Absensi::whereDate('tanggal', today())->count(),
        ];

        return view('admin.monitoring.verifikasi', compact(
            'absensiBelumVerified',
            'absensiVerified',
            'siswaList',
            'stats'
        ));
    }
}