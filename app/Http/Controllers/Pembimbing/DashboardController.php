<?php

namespace App\Http\Controllers\Pembimbing;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Jurnal;
use App\Models\Kunjungan;
use App\Models\SiswaProfile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Auto-generate alpha for all active students to ensure stats are accurate
        \App\Helpers\AttendanceHelper::generateAlphaForAllActiveSiswa();

        $pembimbingId = Auth::id();
        
        // 1. ✅ Hitung total siswa binaan (gunakan query builder yang aman)
        $totalSiswa = SiswaProfile::where('pembimbing_id', $pembimbingId)->count();
        
        // 2. ✅ Hitung jurnal menunggu persetujuan
        $jurnalMenunggu = Jurnal::whereHas('siswa', function($q) use ($pembimbingId) {
                $q->whereHas('siswaProfile', function($sq) use ($pembimbingId) {
                    $sq->where('pembimbing_id', $pembimbingId);
                });
            })
            ->where('status', 'menunggu')
            ->count();
        
        // 3. ✅ Hitung absensi belum diverifikasi
        $absensiBelumVerif = Absensi::whereHas('siswa', function($q) use ($pembimbingId) {
                $q->whereHas('siswaProfile', function($sq) use ($pembimbingId) {
                    $sq->where('pembimbing_id', $pembimbingId);
                });
            })
            ->where('is_verified', false)
            ->count();
        
        // 4. ✅ Hitung kunjungan hari ini
        $kunjunganHariIni = Kunjungan::where('pembimbing_id', $pembimbingId)
            ->whereDate('tanggal', Carbon::today())
            ->count();
        
        // 5. ✅ Hitung kehadiran siswa hari ini (yang sudah verified)
        $kehadiranHariIni = Absensi::whereHas('siswa', function($q) use ($pembimbingId) {
                $q->whereHas('siswaProfile', function($sq) use ($pembimbingId) {
                    $sq->where('pembimbing_id', $pembimbingId);
                });
            })
            ->whereDate('tanggal', Carbon::today())
            ->where('is_verified', true)
            ->distinct('siswa_user_id')
            ->count('siswa_user_id');
        
        // 6. ✅ Ambil 3 absensi terbaru yang belum diverifikasi (Eloquent Collection)
        $absensiTerbaru = Absensi::whereHas('siswa', function($q) use ($pembimbingId) {
                $q->whereHas('siswaProfile', function($sq) use ($pembimbingId) {
                    $sq->where('pembimbing_id', $pembimbingId);
                });
            })
            ->where('is_verified', false)
            ->with('siswa') // Eager load relasi siswa
            ->latest()
            ->limit(3)
            ->get(); // ✅ Pastikan ->get() mengembalikan Collection, bukan array
        
        // 7. ✅ Ambil 3 jurnal terbaru yang menunggu (Eloquent Collection)
        $jurnalTerbaru = Jurnal::whereHas('siswa', function($q) use ($pembimbingId) {
                $q->whereHas('siswaProfile', function($sq) use ($pembimbingId) {
                    $sq->where('pembimbing_id', $pembimbingId);
                });
            })
            ->where('status', 'menunggu')
            ->with('siswa') // Eager load relasi siswa
            ->latest()
            ->limit(3)
            ->get(); // ✅ Pastikan ->get() mengembalikan Collection
        
        $aktivitasTerbaru = collect();

        foreach ($absensiTerbaru as $absensi) {
            $aktivitasTerbaru->push([
                'type' => 'absensi',
                'data' => $absensi,
                'created_at' => $absensi->created_at
            ]);
        }

        foreach ($jurnalTerbaru as $jurnal) {
            $aktivitasTerbaru->push([
                'type' => 'jurnal',
                'data' => $jurnal,
                'created_at' => $jurnal->created_at
            ]);
        }

        $aktivitasTerbaru = $aktivitasTerbaru->sortByDesc('created_at')->take(5);

        $totalPerusahaan = \App\Models\Perusahaan::where('pembimbing_id', $pembimbingId)->count();

        // 8. 🔥 Early Warning System: Siswa dengan Alpha >= 3
        $siswaBermasalahAlpha = collect(SiswaProfile::where('pembimbing_id', $pembimbingId)
            ->whereHas('user.absensis', function($sq) {
                $sq->where('status', 'alpha');
            }, '>=', 3)
            ->with(['user', 'user.absensis' => function($q) {
                $q->where('status', 'alpha');
            }])
            ->get()
            ->map(function($profile) {
                return [
                    'nama' => $profile->user->name ?? 'Unknown',
                    'masalah' => 'Alpha ' . $profile->user->absensis->count() . ' kali'
                ];
            })->all());

        // 9. 🔥 Early Warning System: Siswa Tanpa Jurnal sama sekali
        $siswaTanpaJurnal = collect(SiswaProfile::where('pembimbing_id', $pembimbingId)
            ->whereDoesntHave('user.jurnals')
            ->with('user')
            ->get()
            ->map(function($profile) {
                return [
                    'nama' => $profile->user->name ?? 'Unknown',
                    'masalah' => 'Belum pernah mengisi jurnal'
                ];
            })->all());
            
        // 11. 🔥 Early Warning System: Siswa Belum Absen Hari Ini
        $siswaBelumAbsen = collect();
        if (Carbon::today()->dayOfWeek !== Carbon::SUNDAY) {
            $siswaBelumAbsen = collect(SiswaProfile::where('pembimbing_id', $pembimbingId)
                ->whereDoesntHave('user.absensis', function($sq) {
                    $sq->whereDate('tanggal', Carbon::today());
                })
                ->whereHas('perusahaan.periodePKL', function($sq) {
                    $sq->where('is_active', true)
                       ->whereDate('tanggal_mulai', '<=', Carbon::today())
                       ->whereDate('tanggal_selesai', '>=', Carbon::today());
                })
                ->with('user')
                ->get()
                ->map(function($profile) {
                    return [
                        'nama' => $profile->user->name ?? 'Unknown',
                        'masalah' => 'Belum mengisi absensi hari ini'
                    ];
                })->all());
        }
            
        $earlyWarnings = $siswaBermasalahAlpha->merge($siswaTanpaJurnal)->merge($siswaBelumAbsen);

        // 10. 📊 Data Grafik: Rekap Kehadiran Seluruh Siswa Binaan
        $chartDataRaw = DB::table('absensis')
            ->join('users', 'absensis.siswa_user_id', '=', 'users.id')
            ->join('siswa_profiles', 'users.id', '=', 'siswa_profiles.user_id')
            ->where('siswa_profiles.pembimbing_id', $pembimbingId)
            ->select('absensis.status', DB::raw('count(*) as total'))
            ->groupBy('absensis.status')
            ->get()
            ->pluck('total', 'status')
            ->toArray();

        $chartAbsensi = [
            'hadir' => $chartDataRaw['hadir'] ?? 0,
            'sakit' => $chartDataRaw['sakit'] ?? 0,
            'izin'  => $chartDataRaw['izin'] ?? 0,
            'alpha' => $chartDataRaw['alpha'] ?? 0,
        ];

        return view('pembimbing.dashboard', [
            'totalSiswa' => $totalSiswa,
            'totalPerusahaan' => $totalPerusahaan,
            'jurnalMenunggu' => $jurnalMenunggu,
            'absensiPerluApprove' => $absensiBelumVerif,
            'kunjunganMendatang' => $kunjunganHariIni,
            'siswaAbsenHariIni' => $kehadiranHariIni,
            'aktivitasTerbaru' => $aktivitasTerbaru,
            'earlyWarnings' => $earlyWarnings,
            'chartAbsensi' => $chartAbsensi
        ]);
    }
}