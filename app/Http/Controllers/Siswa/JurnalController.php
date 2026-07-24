<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Jurnal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JurnalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jurnals = Jurnal::where('siswa_user_id', Auth::id())
            ->latest()
            ->paginate(10);

        // Cek apakah siswa sudah mengisi jurnal hari ini
        $sudahIsiHariIni = Jurnal::where('siswa_user_id', Auth::id())
            ->whereDate('tanggal', today())
            ->exists();

        $jurnalHariIni = $sudahIsiHariIni
            ? Jurnal::where('siswa_user_id', Auth::id())
                ->whereDate('tanggal', today())
                ->first()
            : null;

        // Ambil absensi hari ini untuk keterangan status jurnal
        $absensiHariIni = Absensi::where('siswa_user_id', Auth::id())
            ->whereDate('tanggal', today())
            ->first();
            
        // Ambil jurnal terakhir (untuk fitur auto-fill kegiatan)
        $jurnalTerakhir = Jurnal::where('siswa_user_id', Auth::id())
            ->whereDate('tanggal', '<', today()) // yang bukan hari ini
            ->latest('tanggal')
            ->first();
            
        return view('siswa.jurnal.index', compact('jurnals', 'sudahIsiHariIni', 'jurnalHariIni', 'absensiHariIni', 'jurnalTerakhir'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $error = $this->checkPeriodePKL();
        if ($error) {
            return back()->with('error', $error);
        }

        // Cek absensi hari ini — jurnal hanya bisa diisi jika status hadir atau terlambat
        $absensiHariIni = \App\Models\Absensi::where('siswa_user_id', Auth::id())
            ->whereDate('tanggal', today())
            ->first();

        if (!$absensiHariIni) {
            return back()->with('error', '⚠️ Anda harus mengisi absensi terlebih dahulu sebelum mengisi jurnal.');
        }

        if (!in_array($absensiHariIni->status, ['hadir', 'terlambat'])) {
            $labelStatus = match($absensiHariIni->status) {
                'sakit'  => 'sakit',
                'izin'   => 'izin',
                'libur'  => 'libur',
                'alpha'  => 'alpha',
                default  => $absensiHariIni->status,
            };
            return back()->with('error', "⚠️ Jurnal tidak dapat diisi karena status kehadiran Anda hari ini adalah \"$labelStatus\". Jurnal hanya dapat diisi saat hadir atau terlambat.");
        }

        // Cek apakah siswa sudah mengisi jurnal hari ini
        $sudahIsiHariIni = Jurnal::where('siswa_user_id', Auth::id())
            ->whereDate('tanggal', today())
            ->exists();

        if ($sudahIsiHariIni) {
            return back()->with('error', '⚠️ Anda sudah mengisi jurnal untuk hari ini. Jurnal hanya dapat diisi satu kali per hari.');
        }

        // Validasi input
        $validated = $request->validate([
            'kegiatan' => 'required|string|max:1000',
            'kendala' => 'nullable|string|max:1000',
            'foto' => 'nullable|image|max:20480|mimes:jpg,jpeg,png',
        ], [
            'kegiatan.required' => 'Kegiatan wajib diisi',
            'kegiatan.max' => 'Kegiatan terlalu panjang (maks 1000 karakter)',
            'foto.image' => 'File harus berupa gambar',
            'foto.max' => 'Ukuran foto maksimal 20MB (akan dikompres otomatis)',
            'foto.mimes' => 'Format foto harus jpg, jpeg, atau png',
        ]);

        // Upload foto jika ada
        $path = null;
        if ($request->hasFile('foto')) {
            $path = \App\Helpers\UploadHelper::uploadAndCompress($request->file('foto'), 'jurnal', 'public');
        }

        // Simpan jurnal dengan status default 'menunggu'
        Jurnal::create([
            'siswa_user_id' => Auth::id(),
            'tanggal' => now()->toDateString(),
            'kegiatan' => $validated['kegiatan'],
            'kendala' => $validated['kendala'] ?? null,
            'foto' => $path,
            'status' => 'menunggu', // Default: menunggu persetujuan pembimbing
        ]);

        return back()->with('success', '✅ Jurnal harian berhasil disimpan! Menunggu persetujuan pembimbing.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jurnal $jurnal)
    {
        // Hanya pemilik jurnal yang bisa edit
        if ($jurnal->siswa_user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $error = $this->checkPeriodePKL();
        if ($error) {
            return redirect()->route('siswa.jurnal.index')->with('error', $error);
        }

        // Hanya jurnal dengan status 'menunggu' atau 'revisi' yang bisa diedit
        if (!in_array($jurnal->status, ['menunggu', 'revisi'])) {
            return back()->with('info', 'Jurnal yang sudah disetujui tidak dapat diedit.');
        }

        return view('siswa.jurnal.edit', compact('jurnal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jurnal $jurnal)
    {
        // Validasi keamanan
        if ($jurnal->siswa_user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $error = $this->checkPeriodePKL();
        if ($error) {
            return back()->with('error', $error);
        }

        if (!in_array($jurnal->status, ['menunggu', 'revisi'])) {
            return back()->with('info', 'Jurnal yang sudah disetujui tidak dapat diedit.');
        }

        $validated = $request->validate([
            'kegiatan' => 'required|string|max:1000',
            'kendala' => 'nullable|string|max:1000',
            'foto' => 'nullable|image|max:20480|mimes:jpg,jpeg,png',
        ], [
            'foto.image' => 'File harus berupa gambar',
            'foto.max' => 'Ukuran foto maksimal 20MB (akan dikompres otomatis)',
            'foto.mimes' => 'Format foto harus jpg, jpeg, atau png',
        ]);

        // Handle foto baru
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($jurnal->foto) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($jurnal->foto);
            }
            $validated['foto'] = \App\Helpers\UploadHelper::uploadAndCompress($request->file('foto'), 'jurnal', 'public');
        }

        $jurnal->update([
            'kegiatan' => $validated['kegiatan'],
            'kendala' => $validated['kendala'] ?? null,
            'foto' => $validated['foto'] ?? $jurnal->foto,
            // Jika sebelumnya 'revisi', ubah ke 'menunggu' lagi setelah edit
            'status' => $jurnal->status === 'revisi' ? 'menunggu' : $jurnal->status,
        ]);

        return back()->with('success', '✅ Jurnal berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jurnal $jurnal)
    {
        // Hanya pemilik yang bisa hapus
        if ($jurnal->siswa_user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        // Hanya jurnal 'menunggu' yang bisa dihapus
        if ($jurnal->status !== 'menunggu') {
            return back()->with('info', 'Jurnal yang sudah diproses tidak dapat dihapus.');
        }

        // Hapus file foto jika ada
        if ($jurnal->foto) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($jurnal->foto);
        }

        $jurnal->delete();

        return back()->with('success', '🗑️ Jurnal berhasil dihapus!');
    }

    /**
     * Cek Periode PKL Siswa yang sedang berjalan
     */
    private function checkPeriodePKL()
    {
        $profile = Auth::user()->siswaProfile;
        if (!$profile || !$profile->perusahaan_id) {
            return '⚠️ Anda belum ditempatkan di industri/perusahaan mitra.';
        }

        $perusahaan = $profile->perusahaan;
        if (!$perusahaan || !$perusahaan->periode_pkl_id) {
            return '⚠️ Industri Anda belum dikaitkan dengan Periode PKL.';
        }

        $periode = $perusahaan->periodePKL;
        $today = now();
        if (!$periode || !$periode->is_active || $today->lt($periode->tanggal_mulai) || $today->gt($periode->tanggal_selesai)) {
            $dateRange = $periode ? "({$periode->tanggal_mulai->format('d-m-Y')} s/d {$periode->tanggal_selesai->format('d-m-Y')})" : "";
            return "⚠️ Anda hanya bisa mengisi/mengubah jurnal selama periode PKL Anda berjalan {$dateRange}.";
        }

        return null;
    }
}