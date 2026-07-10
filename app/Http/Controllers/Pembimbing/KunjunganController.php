<?php

namespace App\Http\Controllers\Pembimbing;

use App\Http\Controllers\Controller;
use App\Models\Kunjungan;
use App\Models\Perusahaan;
use App\Models\SiswaProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KunjunganController extends Controller
{
    /**
     * Display a listing of kunjungan (dengan filter & pagination).
     */
    public function index(Request $request)
    {
        $query = Kunjungan::where('pembimbing_id', Auth::id())
            ->with(['perusahaan.siswaProfiles.user']);

        // Filter by perusahaan
        if ($request->filled('perusahaan_id')) {
            $query->where('perusahaan_id', $request->perusahaan_id);
        }

        // Filter by tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        // Filter by status (rencana/selesai)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $kunjungans = $query->orderBy('tanggal', 'desc')->paginate(10);
        
        // Data perusahaan binaan pembimbing untuk dropdown filter
        $perusahaanBinaan = Perusahaan::where('pembimbing_id', Auth::id())
            ->orderBy('nama')
            ->get();

        return view('pembimbing.kunjungan.index', compact('kunjungans', 'perusahaanBinaan'));
    }

    /**
     * Show the form for creating a new kunjungan.
     */
    public function create()
    {
        $perusahaanBinaan = Perusahaan::where('pembimbing_id', Auth::id())
            ->orderBy('nama')
            ->get();
            
        return view('pembimbing.kunjungan.create', compact('perusahaanBinaan'));
    }

    /**
     * Store a newly created kunjungan in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'perusahaan_id' => 'required|exists:perusahaan,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:rencana,selesai',
            'catatan_rencana' => 'required_if:status,rencana|nullable|string|max:1000',
            'catatan' => 'required_if:status,selesai|nullable|string|max:1000',
            'foto' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
        ], [
            'perusahaan_id.required' => 'Pilih perusahaan terlebih dahulu',
            'tanggal.required' => 'Tanggal kunjungan wajib diisi',
            'status.required' => 'Status kunjungan wajib diisi',
            'catatan_rencana.required_if' => 'Catatan rencana kunjungan wajib diisi jika status adalah Rencana',
            'catatan.required_if' => 'Catatan kunjungan wajib diisi jika status adalah Selesai',
            'foto.image' => 'File harus berupa gambar',
            'foto.max' => 'Ukuran foto maksimal 2MB',
        ]);

        // Validasi keamanan: Pastikan perusahaan diassign ke pembimbing ini
        $perusahaan = Perusahaan::where('id', $validated['perusahaan_id'])
            ->where('pembimbing_id', Auth::id())
            ->first();
            
        if (!$perusahaan) {
            abort(403, 'Anda hanya bisa mencatat kunjungan untuk perusahaan yang Anda bimbing.');
        }

        // Handle upload foto
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('kunjungan', 'public');
        }

        // Simpan kunjungan
        Kunjungan::create([
            'pembimbing_id' => Auth::id(),
            'siswa_user_id' => null, // target perusahaan, bukan siswa
            'perusahaan_id' => $validated['perusahaan_id'],
            'tanggal' => $validated['tanggal'],
            'catatan_rencana' => $validated['status'] === 'rencana' ? $validated['catatan_rencana'] : null,
            'catatan' => $validated['status'] === 'selesai' ? $validated['catatan'] : null,
            'foto' => $fotoPath,
            'status' => $validated['status'],
        ]);

        return redirect()->route('pembimbing.kunjungan')
            ->with('success', '✅ Kunjungan berhasil disimpan!');
    }

    /**
     * Show the form for editing the specified kunjungan.
     */
    public function edit(Kunjungan $kunjungan)
    {
        // Pastikan kunjungan ini milik pembimbing yang sedang login
        if ($kunjungan->pembimbing_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke data kunjungan ini.');
        }

        $perusahaanBinaan = Perusahaan::where('pembimbing_id', Auth::id())
            ->orderBy('nama')
            ->get();

        return view('pembimbing.kunjungan.edit', compact('kunjungan', 'perusahaanBinaan'));
    }

    /**
     * Update the specified kunjungan in storage.
     */
    public function update(Request $request, Kunjungan $kunjungan)
    {
        // Pastikan kunjungan ini milik pembimbing yang sedang login
        if ($kunjungan->pembimbing_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke data kunjungan ini.');
        }

        $validated = $request->validate([
            'perusahaan_id' => 'required|exists:perusahaan,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:rencana,selesai',
            'catatan_rencana' => 'required_if:status,rencana|nullable|string|max:1000',
            'catatan' => 'required_if:status,selesai|nullable|string|max:1000',
            'foto' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
        ], [
            'perusahaan_id.required' => 'Pilih perusahaan terlebih dahulu',
            'tanggal.required' => 'Tanggal kunjungan wajib diisi',
            'status.required' => 'Status kunjungan wajib diisi',
            'catatan_rencana.required_if' => 'Catatan rencana kunjungan wajib diisi jika status adalah Rencana',
            'catatan.required_if' => 'Catatan kunjungan wajib diisi jika status adalah Selesai',
            'foto.image' => 'File harus berupa gambar',
            'foto.max' => 'Ukuran foto maksimal 2MB',
        ]);

        // Validasi keamanan: Pastikan perusahaan diassign ke pembimbing ini
        $perusahaan = Perusahaan::where('id', $validated['perusahaan_id'])
            ->where('pembimbing_id', Auth::id())
            ->first();
            
        if (!$perusahaan) {
            abort(403, 'Anda hanya bisa mencatat kunjungan untuk perusahaan yang Anda bimbing.');
        }

        // Handle upload foto
        $fotoPath = $kunjungan->foto;
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($fotoPath) {
                Storage::disk('public')->delete($fotoPath);
            }
            $fotoPath = $request->file('foto')->store('kunjungan', 'public');
        }

        // Update kunjungan
        $kunjungan->update([
            'perusahaan_id' => $validated['perusahaan_id'],
            'tanggal' => $validated['tanggal'],
            'catatan_rencana' => $validated['status'] === 'rencana' ? $validated['catatan_rencana'] : null,
            'catatan' => $validated['status'] === 'selesai' ? $validated['catatan'] : null,
            'foto' => $fotoPath,
            'status' => $validated['status'],
        ]);

        return redirect()->route('pembimbing.kunjungan')
            ->with('success', '✅ Kunjungan berhasil diperbarui!');
    }

    /**
     * Remove the specified kunjungan from storage.
     */
    public function destroy(Kunjungan $kunjungan)
    {
        // Pastikan kunjungan ini milik pembimbing yang sedang login
        if ($kunjungan->pembimbing_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke data kunjungan ini.');
        }

        // Hapus foto jika ada
        if ($kunjungan->foto) {
            Storage::disk('public')->delete($kunjungan->foto);
        }

        $kunjungan->delete();

        return redirect()->route('pembimbing.kunjungan')
            ->with('success', '✅ Kunjungan berhasil dihapus!');
    }
}