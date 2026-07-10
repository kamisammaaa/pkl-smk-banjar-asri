<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PerusahaanData;
use Illuminate\Http\Request;

class PerusahaanDataController extends Controller
{
    /**
     * Display listing of perusahaan data with approval status.
     */
    public function index()
    {
        $data = PerusahaanData::with('siswa')
            ->latest()
            ->paginate(15);
            
        return view('admin.perusahaan-data.index', compact('data'));
    }

    /**
     * 🔥 BARU: Approve data perusahaan siswa.
     */
    public function approve(PerusahaanData $perusahaanData)
    {
        $perusahaanData->update(['is_verified' => true]);
        
        return back()->with('success', "✅ Data perusahaan untuk {$perusahaanData->siswa->name} telah disetujui!");
    }

    /**
     * 🔥 BARU: Reject data perusahaan siswa.
     */
    public function reject(PerusahaanData $perusahaanData)
    {
        $perusahaanData->update(['is_verified' => false]);
        
        return back()->with('success', "❌ Data perusahaan untuk {$perusahaanData->siswa->name} ditandai belum valid.");
    }

    /**
     * Print view for admin.
     */
    public function print()
    {
        // Hanya tampilkan data yang sudah approved untuk dicetak
        $data = PerusahaanData::with('siswa')
            ->where('is_verified', true)
            ->latest()
            ->get();
            
        return view('admin.perusahaan-data.print', compact('data'));
    }
}