<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use App\Models\Kunjungan;
use App\Models\Absensi;
use App\Models\Jurnal;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Helpers\AttendanceHelper;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Auto-generate alpha for past days before viewing dashboard stats
        AttendanceHelper::generateAlphaForSiswa($user->id);

        $profile = $user->siswaProfile;
        $bulanIni = now()->format('Y-m');

        // ========================================
        // 1. PENGUMUMAN DARI ADMIN
        // ========================================
        $pengumuman = \App\Models\Pengumuman::query()
            ->where('is_active', true)
            ->where('published_at', '<=', now())
            ->where(function($q) use ($user) {
                $q->where('target', 'semua')
                  ->orWhere('target', 'siswa')
                  ->orWhereNull('target');
            })
            ->latest()
            ->take(5)
            ->get();

        // ========================================
        // 2. JADWAL KUNJUNGAN PEMBIMBING
        // ========================================
        $perusahaanId = $profile?->perusahaan_id;

        // Kunjungan yang akan datang / rencana
        $kunjunganMendatang = Kunjungan::where(function($q) use ($user, $perusahaanId) {
                if ($perusahaanId) {
                    $q->where('perusahaan_id', $perusahaanId)
                      ->orWhere('siswa_user_id', $user->id);
                } else {
                    $q->where('siswa_user_id', $user->id);
                }
            })
            ->where('status', 'rencana')
            ->with('pembimbing', 'perusahaan')
            ->orderBy('tanggal', 'asc')
            ->first();

        // Riwayat kunjungan (5 terakhir)
        $kunjunganLampau = Kunjungan::where(function($q) use ($user, $perusahaanId) {
                if ($perusahaanId) {
                    $q->where('perusahaan_id', $perusahaanId)
                      ->orWhere('siswa_user_id', $user->id);
                } else {
                    $q->where('siswa_user_id', $user->id);
                }
            })
            ->where('status', 'selesai')
            ->with('pembimbing', 'perusahaan')
            ->orderBy('tanggal', 'desc')
            ->take(5)
            ->get();

        // Gabungkan untuk ditampilkan
        $semuaKunjungan = collect([$kunjunganMendatang])->filter()->merge($kunjunganLampau);

        // ========================================
        // 3. STATISTIK ABSENSI
        // ========================================
        $absensiBulanIni = Absensi::where('siswa_user_id', $user->id)
            ->where('tanggal', 'like', "{$bulanIni}%")
            ->get();

        // Asumsi jam masuk normal: 07:30
        $statsAbsensi = [
            'hadir' => $absensiBulanIni->where('status', 'hadir')
                ->where('is_verified', true)
                ->where('check_in', '<=', '07:30:00')
                ->count(),
            'terlambat' => $absensiBulanIni->where('status', 'hadir')
                ->where('is_verified', true)
                ->where('check_in', '>', '07:30:00')
                ->count(),
            'izin' => $absensiBulanIni->where('status', 'izin')->count(),
            'sakit' => $absensiBulanIni->where('status', 'sakit')->count(),
            'alpha' => $absensiBulanIni->where('status', 'alpha')->count(),
            'total' => $absensiBulanIni->count(),
        ];

        // ========================================
        // 4. STATISTIK JURNAL
        // ========================================
        $jurnalBulanIni = Jurnal::where('siswa_user_id', $user->id)
            ->where('tanggal', 'like', "{$bulanIni}%")
            ->get();

        $statsJurnal = [
            'disetujui' => $jurnalBulanIni->where('status', 'disetujui')->count(),
            'menunggu' => $jurnalBulanIni->where('status', 'menunggu')->count(),
            'revisi' => $jurnalBulanIni->where('status', 'revisi')->count(),
            'total' => $jurnalBulanIni->count(),
        ];

        // ========================================
        // 5. INFO SISWA (Untuk Display)
        // ========================================
        $infoSiswa = [
            'nama' => $user->name,
            'nis' => $profile?->nis ?? '-',
            'jurusan' => $profile?->jurusan?->nama ?? '-',
            'perusahaan' => $profile?->perusahaan?->nama ?? 'Belum diassign',
            'pembimbing' => $profile?->pembimbing?->name ?? '-',
        ];

        return view('siswa.dashboard', compact(
            'pengumuman',
            'semuaKunjungan',
            'kunjunganMendatang',
            'statsAbsensi',
            'statsJurnal',
            'infoSiswa'
        ));
    }
}