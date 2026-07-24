<?php

namespace App\Http\Controllers\Pembimbing;

use App\Http\Controllers\Controller;
use App\Models\PenilaianAkhir;
use App\Models\SiswaProfile;
use App\Models\User;
use App\Services\PenilaianService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NilaiController extends Controller
{
    public function __construct(private readonly PenilaianService $penilaianService)
    {
    }

    /**
     * Display list of students for grading
     */
    public function index()
    {
        $siswaBinaan = SiswaProfile::where('pembimbing_id', Auth::id())
            ->with(['user', 'perusahaan', 'jurusan'])
            ->paginate(15);

        return view('pembimbing.nilai.index', compact('siswaBinaan'));
    }

    /**
     * Show form to grade a student
     */
    public function create(User $siswa)
    {
        $profile = $siswa->siswaProfile;
        
        // Security: Check if this student belongs to this pembimbing
        if (!$profile || $profile->pembimbing_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        // 1. 📅 Ambil Periode PKL dari Perusahaan siswa
        $perusahaan = $profile->perusahaan;
        $periode    = $perusahaan?->periodePKL;

        // Fallback jika tidak ada periode: gunakan 3 bulan terakhir
        if ($periode) {
            $startDate = Carbon::parse($periode->tanggal_mulai);
            $endDate   = Carbon::parse($periode->tanggal_selesai);
        } else {
            $startDate = Carbon::now()->subMonths(3);
            $endDate   = Carbon::now();
        }
            
        // 2. 🗓️ Hitung Total Hari Kerja (Senin - Sabtu, Minggu Libur)
        $totalWorkingDays = 0;
        $currentDate = Carbon::parse($startDate);

        while ($currentDate <= $endDate) {
            // Carbon::SUNDAY = 0. Jika bukan Minggu, hitung sebagai hari kerja.
            if ($currentDate->dayOfWeek != Carbon::SUNDAY) {
                $totalWorkingDays++;
            }
            $currentDate->addDay();
        }

        // 3. ✅ Hitung Kehadiran Terverifikasi dalam Periode tersebut
        $absensis = $siswa->absensis()
            ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        // Gunakan is_late yang sudah dihitung berdasarkan jam_masuk perusahaan saat check-in
        $hadirVerified = $absensis->where('status', 'hadir')->where('is_verified', true);
        $hadirOnTime = $hadirVerified->where('is_late', false)->count();
        $hadirLate   = $hadirVerified->where('is_late', true)->count();

        $sakit    = $absensis->where('status', 'sakit')->where('is_verified', true)->count();
        $izin     = $absensis->where('status', 'izin')->where('is_verified', true)->count();
        $libur    = $absensis->where('status', 'libur')->count();
        $alpha    = $absensis->where('status', 'alpha')->count();
        $totalDays = $absensis->count();

        // Hari aktif = total - libur (konsisten dengan LaporanController)
        $hariAktif = max($totalDays - $libur, 1);

        // Formula: (hadir*100 + terlambat*70 + (izin+sakit)*100 + alpha*0) / hariAktif
        $nilaiAbsensi = $totalDays > 0
            ? round((($hadirOnTime * 100) + ($hadirLate * 70) + (($sakit + $izin) * 100)) / $hariAktif)
            : 0;
            
        // Cap nilai maksimal di 100
        $nilaiAbsensi = min(max($nilaiAbsensi, 0), 100);

        // 5. 📚 Hitung Nilai Jurnal
        // Jurnal wajib dikerjakan pada hari hadir + terlambat + alpha.
        // (Libur, sakit, dan izin tidak dihitung sebagai kewajiban jurnal.)
        $hariMasuk = $hadirOnTime + $hadirLate + $alpha;
        $jurnalDisetujui = $siswa->jurnals->where('status', 'disetujui');
        $nilaiJurnal = $this->penilaianService->calculateNilaiJurnal($jurnalDisetujui, max($hariMasuk, 1));

        // 6. 🎯 Ambil Data Penilaian Terakhir (jika ada)
        $penilaian = PenilaianAkhir::where('siswa_user_id', $siswa->id)
            ->where('pembimbing_id', Auth::id())->first();

        // Siapkan data ringkasan untuk dikirim ke view
        $absensiSummary = [
            'start_date'         => $startDate->format('d M Y'),
            'end_date'           => $endDate->format('d M Y'),
            'total_working_days' => $totalWorkingDays,
            'verified_days'      => $hadirOnTime + $hadirLate,
            'missing_days'       => $alpha,
            'percentage'         => $nilaiAbsensi
        ];

        return view('pembimbing.nilai.create', compact(
            'siswa', 
            'profile', 
            'nilaiAbsensi', 
            'nilaiJurnal', 
            'penilaian', 
            'absensiSummary'
        ));
    }

    /**
     * Save the final grade
     */
    public function store(Request $request, User $siswa)
    {
        $profile = $siswa->siswaProfile;
        if (!$profile || $profile->pembimbing_id !== Auth::id()) abort(403);

        $validated = $request->validate([
            'nilai_sikap' => 'required|integer|min:0|max:100',
            'catatan_akhir' => 'nullable|string|max:1000'
        ]);

        // Ambil nilai terhitung dari hidden inputs
        $nilaiAbsensi = (int) $request->input('nilai_absensi', 0);
        $nilaiJurnal = (int) $request->input('nilai_jurnal', 0);
        $nilaiSikap = (int) $validated['nilai_sikap'];
        
        // Rumus Final: 30% Absensi + 40% Jurnal + 30% Sikap
        $nilaiAkhir = $this->penilaianService->calculateNilaiAkhir($nilaiAbsensi, $nilaiJurnal, $nilaiSikap);

        PenilaianAkhir::updateOrCreate(
            ['siswa_user_id' => $siswa->id, 'pembimbing_id' => Auth::id()],
            [
                'nilai_absensi' => $nilaiAbsensi,
                'nilai_jurnal' => $nilaiJurnal,
                'nilai_sikap' => $nilaiSikap,
                'nilai_akhir' => $nilaiAkhir,
                'catatan_akhir' => $validated['catatan_akhir'],
                'submitted_at' => now()
            ]
        );

        return redirect()->route('pembimbing.nilai.index')
            ->with('success', "✅ Nilai akhir {$siswa->name}: {$nilaiAkhir}/100 tersimpan!");
    }
}
