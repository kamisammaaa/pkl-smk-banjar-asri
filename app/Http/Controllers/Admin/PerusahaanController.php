<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Perusahaan;
use App\Models\User;

class PerusahaanController extends Controller
{
    public function index() {
        $perusahaan = Perusahaan::with(['pembimbing', 'periodePKL'])->latest()->get();
        $periode = \App\Models\PeriodePKL::orderBy('tanggal_mulai', 'desc')->get();
        return view('admin.perusahaan.index', compact('perusahaan', 'periode'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'nama'             => 'required|string|max:255',
            'alamat'           => 'required|string|max:500',
            'kontak'           => 'nullable|string|max:100',
            'pembimbing_id'    => 'required|exists:users,id',
            'periode_pkl_id'   => 'nullable|exists:periode_pkls,id',
            'jam_masuk'        => 'required|date_format:H:i',
            'toleransi_menit'  => 'required|integer|min:0|max:120',
        ]);

        // Pastikan format jam disimpan sebagai HH:MM:SS
        $validated['jam_masuk'] = $validated['jam_masuk'] . ':00';

        Perusahaan::create($validated);
        return back()->with('success', 'Perusahaan berhasil ditambahkan!');
    }

    public function edit(Perusahaan $perusahaan) {
        $pembimbing = User::where('role', 'pembimbing')->orderBy('name')->get();
        $periode = \App\Models\PeriodePKL::orderBy('tanggal_mulai', 'desc')->get();
        return view('admin.perusahaan.edit', compact('perusahaan', 'pembimbing', 'periode'));
    }

    public function update(Request $request, Perusahaan $perusahaan) {
        $validated = $request->validate([
            'nama'             => 'required|string|max:255',
            'alamat'           => 'required|string|max:500',
            'kontak'           => 'nullable|string|max:100',
            'pembimbing_id'    => 'required|exists:users,id',
            'periode_pkl_id'   => 'nullable|exists:periode_pkls,id',
            'jam_masuk'        => 'required|date_format:H:i',
            'toleransi_menit'  => 'required|integer|min:0|max:120',
        ]);

        // Pastikan format jam disimpan sebagai HH:MM:SS
        $validated['jam_masuk'] = $validated['jam_masuk'] . ':00';

        $perusahaan->update($validated);
        return redirect()->route('admin.perusahaan.index')->with('success', 'Perusahaan berhasil diubah!');
    }

    public function destroy(Perusahaan $perusahaan) {
        $perusahaan->delete();
        return back()->with('success', 'Perusahaan berhasil dihapus!');
    }
}