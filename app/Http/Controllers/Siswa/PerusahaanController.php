<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\PerusahaanData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerusahaanController extends Controller
{
    public function index()
    {
        $data = PerusahaanData::where('siswa_user_id', Auth::id())->first();
        return view('siswa.perusahaan', compact('data'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'alamat_pembimbing' => 'required|string',
            'nama_pembimbing' => 'required|string|max:255',
            'ttl_pembimbing' => 'required|string|max:100',
            'no_telp' => 'required|string|max:20',
        ]);

        PerusahaanData::updateOrCreate(
            ['siswa_user_id' => Auth::id()],
            $validated
        );

        return back()->with('success', '✅ Data perusahaan berhasil disimpan!');
    }
}
