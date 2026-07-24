<?php

namespace App\Http\Controllers\Pembimbing;

use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use App\Models\SiswaProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JurnalController extends Controller
{
    public function getApprovalValidationRules(string $status): array
    {
        $rules = [
            'status' => 'required|in:disetujui,revisi',
            'nilai' => 'nullable|integer|min:0|max:100',
            'catatan_revisi' => 'nullable|required_if:status,revisi|string|max:500',
        ];

        if ($status === 'disetujui') {
            $rules['nilai'] = 'required|integer|min:0|max:100';
        }

        return $rules;
    }

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
        } else {
            $query->where(function ($q) {
                $q->where('status', '!=', 'disetujui')
                  ->orWhereNull('nilai');
            });
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

        $validated = $request->validate($this->getApprovalValidationRules($request->input('status')));

        $this->saveReview($jurnal, $validated);

        $msg = $validated['status'] === 'disetujui'
            ? '✅ Jurnal disetujui'
            : '🔄 Jurnal dikembalikan untuk revisi';
        if (!empty($validated['nilai'])) {
            $msg .= " (Nilai: {$validated['nilai']}/100)";
        }

        return back()->with('success', $msg);
    }

    public function bulkApprove(Request $request)
    {
        $request->validate([
            'jurnal_ids' => 'required|string',
            'nilai' => 'required|integer|min:0|max:100'
        ]);

        $ids = explode(',', $request->jurnal_ids);
        if (empty($ids)) {
            return back()->with('error', 'Tidak ada jurnal yang dipilih.');
        }

        $pembimbingId = Auth::id();
        $count = 0;
        $nilai = $request->nilai;

        foreach ($ids as $id) {
            $jurnal = Jurnal::with('siswa.siswaProfile')->find($id);
            if ($jurnal) {
                $profile = $jurnal->siswa->siswaProfile ?? null;
                // Pastikan milik pembimbing ini dan belum disetujui (atau disetujui tapi mau diupdate nilainya massal)
                if ($profile && $profile->pembimbing_id === $pembimbingId) {
                    $jurnal->update([
                        'status' => 'disetujui',
                        'nilai' => $nilai,
                        'catatan_revisi' => null, // Hapus catatan revisi jika ada
                    ]);
                    $count++;
                }
            }
        }

        return back()->with('success', "✅ Berhasil menyetujui {$count} jurnal dengan nilai {$nilai}.");
    }

    public function edit(Jurnal $jurnal)
    {
        $profile = $jurnal->siswa->siswaProfile;
        if (!$profile || $profile->pembimbing_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        return back()->with('edit_jurnal_id', $jurnal->id);
    }

    public function update(Request $request, Jurnal $jurnal)
    {
        $profile = $jurnal->siswa->siswaProfile;
        if (!$profile || $profile->pembimbing_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate($this->getApprovalValidationRules($request->input('status')));

        $this->saveReview($jurnal, $validated);

        return back()->with('success', '✅ Review jurnal berhasil diperbarui');
    }

    private function saveReview(Jurnal $jurnal, array $validated): void
    {
        $jurnal->update([
            'status' => $validated['status'],
            'nilai' => $validated['nilai'] ?? null,
            'catatan_revisi' => $validated['catatan_revisi'] ?? null,
        ]);
    }
}