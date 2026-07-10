<?php

namespace App\Http\Controllers\Pembimbing;

use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use App\Models\SiswaProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JurnalController extends Controller
{
    public function index(Request $request)
    {
        $query = Jurnal::whereHas('siswa.siswaProfile', fn($q) => $q->where('pembimbing_id', Auth::id()))
            ->with('siswa.siswaProfile.perusahaan');

        // Filter
        if ($request->filled('siswa_id')) {
            $query->where('siswa_user_id', $request->siswa_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $jurnals = $query->latest()->paginate(15);
        
        $siswaBinaan = SiswaProfile::where('pembimbing_id', Auth::id())
            ->with('user')
            ->get();

        return view('pembimbing.jurnal.index', compact('jurnals', 'siswaBinaan'));
    }

    public function approve(Request $request, Jurnal $jurnal)
    {
        // Validasi keamanan
        $profile = $jurnal->siswa->siswaProfile;
        if (!$profile || $profile->pembimbing_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'status' => 'required|in:disetujui,revisi',
            'nilai' => 'nullable|integer|min:0|max:100',
            'catatan_revisi' => 'nullable|required_if:status,revisi|string|max:500'
        ]);

        $jurnal->update([
            'status' => $validated['status'],
            'nilai' => $validated['nilai'] ?? null,
            'catatan_revisi' => $validated['catatan_revisi'] ?? null,
        ]);

        $msg = $validated['status'] === 'disetujui' 
            ? '✅ Jurnal disetujui' 
            : '🔄 Jurnal dikembalikan untuk revisi';
        if ($validated['nilai']) $msg .= " (Nilai: {$validated['nilai']}/100)";

        return back()->with('success', $msg);
    }
}