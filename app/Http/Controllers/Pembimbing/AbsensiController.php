<?php

namespace App\Http\Controllers\Pembimbing;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\SiswaProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        // Generate alpha hanya untuk siswa binaan saat ini
        // (tidak memanggil generateAlphaForAllActiveSiswa agar tidak over-generate)
        $siswaBinaan = SiswaProfile::where('pembimbing_id', Auth::id())
            ->with('user')
            ->get();

        foreach ($siswaBinaan as $s) {
            \App\Helpers\AttendanceHelper::generateAlphaForSiswa($s->user_id);
        }

        $query = Absensi::whereHas('siswa.siswaProfile', fn($q) => $q->where('pembimbing_id', Auth::id()))
            ->with(['siswa.siswaProfile.perusahaan', 'siswa.siswaProfile.jurusan', 'verifier']);

        // Filter Siswa
        if ($request->filled('siswa_id')) {
            $query->where('siswa_user_id', $request->siswa_id);
        }

        // Filter Status
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                // Semua yang belum diverifikasi dan bukan alpha
                $query->where('is_verified', false)->where('status', '!=', 'alpha');
            } elseif ($request->status === 'verified') {
                $query->where('is_verified', true);
            } else {
                // Filter by status name (hadir, sakit, izin, libur, alpha)
                $query->where('status', $request->status);
            }
        }

        // Filter Tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $absensis = $query->latest('tanggal')->paginate(15);

        // Hitung badge pending untuk info
        $pendingCount = Absensi::whereHas('siswa.siswaProfile', fn($q) => $q->where('pembimbing_id', Auth::id()))
            ->where('is_verified', false)
            ->where('status', '!=', 'alpha')
            ->count();

        return view('pembimbing.absensi.index', compact('absensis', 'siswaBinaan', 'pendingCount'));
    }

    /**
     * Approve / verifikasi absensi siswa.
     */
    public function verify(Absensi $absensi)
    {
        // Validasi keamanan: hanya pembimbing yang menangani siswa ini
        $profile = $absensi->siswa->siswaProfile ?? null;
        if (!$profile || $profile->pembimbing_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        // Alpha otomatis tidak perlu diverifikasi manual
        if ($absensi->status === 'alpha') {
            return back()->with('error', '⚠️ Absensi Alpha otomatis tidak perlu diverifikasi.');
        }

        $absensi->update([
            'is_verified' => true,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        return back()->with('success', '✅ Absensi ' . $absensi->siswa->name . ' (' . $absensi->getStatusLabel() . ') berhasil diverifikasi.');
    }

    /**
     * Tolak / reject absensi siswa (mengembalikan ke pending dengan keterangan).
     */
    public function reject(Request $request, Absensi $absensi)
    {
        $profile = $absensi->siswa->siswaProfile ?? null;
        if (!$profile || $profile->pembimbing_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        if ($absensi->status === 'alpha') {
            return back()->with('error', '⚠️ Absensi Alpha tidak bisa ditolak.');
        }

        // Reset ke unverified (pending)
        $absensi->update([
            'is_verified' => false,
            'verified_by' => null,
            'verified_at' => null,
            'keterangan_status' => $request->keterangan ?? 'Ditolak oleh pembimbing',
        ]);

        return back()->with('success', '❌ Absensi ' . $absensi->siswa->name . ' berhasil ditolak/dikembalikan ke pending.');
    }

    /**
     * Ekspor Data Absensi Siswa Binaan ke Format CSV
     */
    public function export(Request $request): StreamedResponse
    {
        $pembimbingId = Auth::id();
        $fileName     = 'rekap_absensi_binaan_' . now()->format('Y-m-d') . '.csv';

        $query = Absensi::whereHas('siswa.siswaProfile', fn($q) => $q->where('pembimbing_id', $pembimbingId))
            ->with(['siswa.siswaProfile.perusahaan', 'siswa.siswaProfile.jurusan']);

        if ($request->filled('siswa_id')) {
            $query->where('siswa_user_id', $request->siswa_id);
        }
        if ($request->filled('status')) {
            if (in_array($request->status, ['pending', 'verified'])) {
                $query->where('is_verified', $request->status === 'verified');
            } else {
                $query->where('status', $request->status);
            }
        }
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $absensis = $query->orderBy('tanggal', 'desc')->get();

        $headers = [
            'Content-type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $columns = ['No', 'Tanggal', 'Nama Siswa', 'Jurusan', 'Perusahaan/DUDI', 'Jam Masuk', 'Status', 'Keterangan', 'Verifikasi'];

        return response()->stream(function () use ($absensis, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, $columns);

            foreach ($absensis as $index => $a) {
                $profile = $a->siswa->siswaProfile ?? null;
                $row     = [
                    $index + 1,
                    $a->tanggal ? \Carbon\Carbon::parse($a->tanggal)->format('d/m/Y') : '-',
                    $a->siswa->name ?? '-',
                    $profile && $profile->jurusan ? $profile->jurusan->nama : '-',
                    $profile && $profile->perusahaan ? $profile->perusahaan->nama : '-',
                    $a->check_in ? \Carbon\Carbon::parse($a->check_in)->format('H:i') : '-',
                    ucfirst($a->status ?? 'Alpha'),
                    $a->alasan ?? '-',
                    $a->is_verified ? 'Terverifikasi' : 'Pending',
                ];
                fputcsv($file, $row);
            }

            fclose($file);
        }, 200, $headers);
    }
}