<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\User;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AbsensiController extends Controller
{
    /**
     * Tampilkan daftar absensi siswa dengan pencarian & filter
     */
    public function index(Request $request)
    {
        $query = Absensi::with(['siswa.siswaProfile.jurusan', 'siswa.siswaProfile.pembimbing', 'verifier']);

        // 🔍 Filter: Pencarian nama siswa atau NIS
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('siswa', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('siswaProfile', function($sq) use ($search) {
                      $sq->where('nis', 'like', "%{$search}%");
                  });
            });
        }

        // 🔍 Filter: Tanggal Mulai & Selesai
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        // 🔍 Filter: Status Absensi
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 🔍 Filter: Jurusan Siswa
        if ($request->filled('jurusan_id')) {
            $query->whereHas('siswa.siswaProfile', function($q) use ($request) {
                $q->where('jurusan_id', $request->jurusan_id);
            });
        }

        // Paginate data absensi
        $absensi = $query->latest('tanggal')->paginate(15);
        $absensi->appends($request->all());

        // Ambil data untuk opsi filter
        $jurusanList = Jurusan::orderBy('nama')->get();
        $statusOptions = Absensi::getStatusOptions();

        return view('admin.absensi.index', compact('absensi', 'jurusanList', 'statusOptions'));
    }

    /**
     * Hapus satu data absensi beserta file fisiknya dari storage
     */
    public function destroy(Absensi $absensi)
    {
        // Hapus file foto jika ada
        if ($absensi->foto && Storage::disk('public')->exists($absensi->foto)) {
            Storage::disk('public')->delete($absensi->foto);
        }

        // Hapus file bukti jika ada
        if ($absensi->bukti_file && Storage::disk('public')->exists($absensi->bukti_file)) {
            Storage::disk('public')->delete($absensi->bukti_file);
        }

        $siswaName = $absensi->siswa->name ?? 'Siswa';
        $tanggal = $absensi->tanggal->format('d-m-Y');
        
        $absensi->delete();

        return back()->with('success', "✅ Data absensi <strong>{$siswaName}</strong> tanggal <strong>{$tanggal}</strong> berhasil dihapus.");
    }

    /**
     * Hapus masal data absensi yang dipilih
     */
    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', "❌ Pilih data absensi yang ingin dihapus terlebih dahulu.");
        }

        $absensis = Absensi::whereIn('id', $ids)->get();
        $count = 0;

        foreach ($absensis as $absensi) {
            // Hapus file foto jika ada
            if ($absensi->foto && Storage::disk('public')->exists($absensi->foto)) {
                Storage::disk('public')->delete($absensi->foto);
            }

            // Hapus file bukti jika ada
            if ($absensi->bukti_file && Storage::disk('public')->exists($absensi->bukti_file)) {
                Storage::disk('public')->delete($absensi->bukti_file);
            }

            $absensi->delete();
            $count++;
        }

        return back()->with('success', "✅ Berhasil menghapus <strong>{$count}</strong> data absensi.");
    }
}
