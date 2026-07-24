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
        // 0. PROGRESS PKL & SMART REMINDERS
        // ========================================
        $progress = [
            'persentase' => 0,
            'hari_berjalan' => 0,
            'total_hari' => 0,
            'isActive' => false
        ];
        
        $periode = $profile?->perusahaan?->periodePKL;
        if ($periode && $periode->is_active) {
            $start = Carbon::parse($periode->tanggal_mulai)->startOfDay();
            $end = Carbon::parse($periode->tanggal_selesai)->startOfDay();
            $today = now()->startOfDay();
            
            $totalHari = (int) $start->diffInDays($end) + 1;
            $progress['total_hari'] = $totalHari;
            $progress['isActive'] = true;
            
            if ($today->lt($start)) {
                $progress['persentase'] = 0;
                $progress['hari_berjalan'] = 0;
            } elseif ($today->gt($end)) {
                $progress['persentase'] = 100;
                $progress['hari_berjalan'] = $totalHari;
            } else {
                $hariBerjalan = (int) $start->diffInDays($today) + 1;
                $progress['hari_berjalan'] = $hariBerjalan;
                $progress['persentase'] = min(100, round(($hariBerjalan / $totalHari) * 100));
            }
        }
        
        // Smart Reminders
        $belumAbsen = false;
        $belumJurnal = false;
        
        $absensiHariIni = Absensi::where('siswa_user_id', $user->id)
            ->whereDate('tanggal', today())
            ->first();
            
        if (!$absensiHariIni) {
            $belumAbsen = true;
        } else {
            // Check if they need to fill journal (hadir/terlambat)
            if (in_array($absensiHariIni->status, ['hadir', 'terlambat'])) {
                $sudahJurnal = Jurnal::where('siswa_user_id', $user->id)
                    ->whereDate('tanggal', today())
                    ->exists();
                if (!$sudahJurnal) {
                    $belumJurnal = true;
                }
            }
        }

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

        // Gunakan is_late yang sudah dihitung berdasarkan jam_masuk perusahaan saat check-in
        $hadirVerified = $absensiBulanIni->where('status', 'hadir')->where('is_verified', true);
        $statsAbsensi = [
            'hadir'     => $hadirVerified->where('is_late', false)->count(),
            'terlambat' => $hadirVerified->where('is_late', true)->count(),
            'izin'      => $absensiBulanIni->where('status', 'izin')->count(),
            'sakit'     => $absensiBulanIni->where('status', 'sakit')->count(),
            'libur'     => $absensiBulanIni->where('status', 'libur')->count(),
            'alpha'     => $absensiBulanIni->where('status', 'alpha')->count(),
            'total'     => $absensiBulanIni->count(),
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
            'infoSiswa',
            'progress',
            'belumAbsen',
            'belumJurnal'
        ));
    }
}