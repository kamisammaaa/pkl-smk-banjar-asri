<?php

namespace App\Http\Controllers\Pembimbing;

use App\Http\Controllers\Controller;
use App\Models\PenilaianAkhir;
use App\Models\User;
use App\Models\Absensi;
use App\Models\Jurnal;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    /**
     * Form penilaian akhir untuk satu siswa
     */
    public function create(User $siswa)
    {
        // Validasi: hanya pembimbing yang boleh nilai siswanya
        $profile = $siswa->siswaProfile;
        if (!$profile || $profile->pembimbing_id !== auth()->id()) {
            abort(403, 'Anda hanya boleh menilai siswa binaan Anda.');
        }

        // Hitung nilai absensi otomatis
        $absensi = Absensi::where('siswa_user_id', $siswa->id)
            ->where('is_verified', true)
            ->get();
            
        $hadir = $absensi->where('status', 'hadir')->where('check_in', '<=', '07:30:00')->count();
        $terlambat = $absensi->where('status', 'hadir')->where('check_in', '>', '07:30:00')->count();
        $izin = $absensi->where('status', 'izin')->count();
        $sakit = $absensi->where('status', 'sakit')->count();
        $alpha = $absensi->where('status', 'alpha')->count();
        $total = $absensi->count();
        
        // Formula: (hadir*100 + terlambat*70 + (izin+sakit)*100 + alpha*0) / total
        $nilaiAbsensi = $total > 0 
            ? round((($hadir * 100) + ($terlambat * 70) + (($izin + $sakit) * 100)) / $total) 
            : 100;

        // Hitung rata-rata nilai jurnal yang disetujui
        $jurnalDisetujui = Jurnal::where('siswa_user_id', $siswa->id)
            ->where('status', 'disetujui')
            ->get();
            
        $rataJurnal = $jurnalDisetujui->avg('nilai') ?? 80; // Default 80 jika belum ada nilai

        // Cek apakah sudah pernah dinilai
        $penilaian = PenilaianAkhir::where('siswa_user_id', $siswa->id)
            ->where('pembimbing_id', auth()->id())
            ->first();

        return view('pembimbing.penilaian.create', compact(
            'siswa', 'profile', 'nilaiAbsensi', 'rataJurnal', 
            'jurnalDisetujui', 'hadir', 'terlambat', 'izin', 'sakit', 'alpha', 'total',
            'penilaian'
        ));
    }

    /**
     * Simpan/update penilaian akhir
     */
    public function store(Request $request, User $siswa)
    {
        $profile = $siswa->siswaProfile;
        if (!$profile || $profile->pembimbing_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'nilai_sikap' => 'required|integer|min:0|max:100',
            'catatan_akhir' => 'nullable|string|max:1000'
        ]);

        // Ambil nilai komponen
        $nilaiAbsensi = (int) $request->input('nilai_absensi', 80);
        $nilaiJurnal = (int) $request->input('nilai_jurnal', 80);
        $nilaiSikap = (int) $validated['nilai_sikap'];
        
        // Hitung nilai akhir: 30% + 40% + 30%
        $nilaiAkhir = round((0.3 * $nilaiAbsensi) + (0.4 * $nilaiJurnal) + (0.3 * $nilaiSikap));

        PenilaianAkhir::updateOrCreate(
            ['siswa_user_id' => $siswa->id, 'pembimbing_id' => auth()->id()],
            [
                'nilai_absensi' => $nilaiAbsensi,
                'nilai_jurnal' => $nilaiJurnal,
                'nilai_sikap' => $nilaiSikap,
                'nilai_akhir' => $nilaiAkhir,
                'catatan_akhir' => $validated['catatan_akhir'],
                'submitted_at' => now()
            ]
        );

        return redirect()->route('pembimbing.dashboard')
            ->with('success', "✅ Nilai akhir untuk {$siswa->name}: {$nilaiAkhir}/100 (Grade: " . $this->getGrade($nilaiAkhir) . ")");
    }
    
    private function getGrade(int $nilai): string
    {
        if ($nilai >= 90) return 'A';
        if ($nilai >= 80) return 'B';
        if ($nilai >= 70) return 'C';
        if ($nilai >= 60) return 'D';
        return 'E';
    }
}