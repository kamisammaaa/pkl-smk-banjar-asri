<?php

namespace App\Http\Controllers\Pembimbing;

use App\Http\Controllers\Controller;
use App\Models\SiswaProfile;
use App\Models\Absensi;
use App\Models\Jurnal;
use App\Models\EOTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonitoringController extends Controller
{
    /**
     * Dashboard pembimbing - monitoring siswa binaan
     */
    public function index()
    {
        $siswaBinaan = SiswaProfile::where('pembimbing_id', Auth::id())
            ->with([
                'user', 
                'perusahaan', 
                'jurusan', 
                'absensis' => fn($q) => $q->where('tanggal', '>=', now()->subMonth()),
                'jurnals' => fn($q) => $q->where('tanggal', '>=', now()->subMonth())->latest(),
                'eotags' => fn($q) => $q->latest()
            ])
            ->get();
            
        return view('pembimbing.dashboard', compact('siswaBinaan'));
    }

    /**
     * Verifikasi absensi siswa
     */
    public function verifyAbsensi(Absensi $absensi)
    {
        $profile = SiswaProfile::where('user_id', $absensi->siswa_user_id)->first();
        if (!$profile || $profile->pembimbing_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $absensi->update([
            'is_verified' => true,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);
        EOTag::where('token', $absensi->eotag_token)->update(['status' => 'used']);

        return back()->with('success', '✅ Absensi berhasil diverifikasi.');
    }

    /**
     * Approve jurnal + optional nilai harian
     */
    public function approveJurnal(Request $request, Jurnal $jurnal)
    {
        // Validasi keamanan
        $profile = $jurnal->siswa->siswaProfile;
        if (!$profile || $profile->pembimbing_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'status' => 'required|in:disetujui,revisi',
            'nilai' => 'nullable|integer|min:0|max:100',
            'catatan_revisi' => 'required_if:status,revisi|string|max:500'
        ], [
            'status.required' => 'Status wajib dipilih',
            'status.in' => 'Status tidak valid',
            'nilai.integer' => 'Nilai harus angka',
            'nilai.min' => 'Nilai minimal 0',
            'nilai.max' => 'Nilai maksimal 100',
        ]);

        $jurnal->update([
            'status' => $validated['status'],
            'nilai' => $validated['nilai'] ?? null,
            'catatan_revisi' => $validated['catatan_revisi'] ?? null,
        ]);

        $msg = $validated['status'] === 'disetujui' 
            ? '✅ Jurnal disetujui' 
            : '🔄 Jurnal dikembalikan untuk revisi';
            
        if ($validated['nilai']) {
            $msg .= " (Nilai: {$validated['nilai']}/100)";
        }

        return back()->with('success', $msg);
    }
}