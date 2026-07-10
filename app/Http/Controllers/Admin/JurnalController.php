<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use App\Models\User;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JurnalController extends Controller
{
    /**
     * Tampilkan daftar jurnal harian siswa dengan pencarian & filter
     */
    public function index(Request $request)
    {
        $query = Jurnal::with(['siswa.siswaProfile.jurusan', 'siswa.siswaProfile.pembimbing']);

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

        // 🔍 Filter: Status Jurnal (menunggu, disetujui, revisi)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 🔍 Filter: Jurusan Siswa
        if ($request->filled('jurusan_id')) {
            $query->whereHas('siswa.siswaProfile', function($q) use ($request) {
                $q->where('jurusan_id', $request->jurusan_id);
            });
        }

        // Paginate data jurnal
        $jurnals = $query->latest('tanggal')->paginate(15);
        $jurnals->appends($request->all());

        // Ambil data untuk opsi filter
        $jurusanList = Jurusan::orderBy('nama')->get();
        $statusOptions = [
            'menunggu' => '⏳ Menunggu',
            'disetujui' => '✅ Disetujui',
            'revisi' => '🔄 Revisi',
        ];

        return view('admin.jurnal.index', compact('jurnals', 'jurusanList', 'statusOptions'));
    }

    /**
     * Hapus satu jurnal beserta file fotonya dari storage
     */
    public function destroy(Jurnal $jurnal)
    {
        // Hapus file foto jika ada
        if ($jurnal->foto && Storage::disk('public')->exists($jurnal->foto)) {
            Storage::disk('public')->delete($jurnal->foto);
        }

        $siswaName = $jurnal->siswa->name ?? 'Siswa';
        $tanggal = $jurnal->tanggal->format('d-m-Y');

        $jurnal->delete();

        return back()->with('success', "✅ Jurnal harian <strong>{$siswaName}</strong> tanggal <strong>{$tanggal}</strong> berhasil dihapus.");
    }

    /**
     * Hapus masal data jurnal yang dipilih
     */
    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', "❌ Pilih jurnal yang ingin dihapus terlebih dahulu.");
        }

        $jurnals = Jurnal::whereIn('id', $ids)->get();
        $count = 0;

        foreach ($jurnals as $jurnal) {
            // Hapus file foto jika ada
            if ($jurnal->foto && Storage::disk('public')->exists($jurnal->foto)) {
                Storage::disk('public')->delete($jurnal->foto);
            }

            $jurnal->delete();
            $count++;
        }

        return back()->with('success', "✅ Berhasil menghapus <strong>{$count}</strong> jurnal.");
    }
}
