<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodePKL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeriodePKLController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() 
    { 
        $periode = PeriodePKL::latest()->get();
        return view('admin.periode-pkl.index', compact('periode')); 
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255', 
            'tanggal_mulai' => 'required|date', 
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai'
        ]);
        
        PeriodePKL::create($validated);
        
        return redirect()->route('admin.periode-pkl.index')
            ->with('success', 'Periode PKL "'.$validated['nama'].'" berhasil ditambahkan!');
    }
    
    /**
     * Activate the specified period (only one active at a time).
     */
    public function activate(PeriodePKL $periodePkl)
    {
        if ($periodePkl->is_active) {
            // Jika sudah aktif, nonaktifkan saja
            $periodePkl->update(['is_active' => false]);
            $status = 'dinonaktifkan';
        } else {
            // Aktifkan periode ini tanpa menonaktifkan periode lain
            // sehingga lebih dari satu periode bisa aktif bersamaan
            $periodePkl->update(['is_active' => true]);
            $status = 'diaktifkan';
        }

        return redirect()->route('admin.periode-pkl.index')
            ->with('success', "✅ Periode \"{$periodePkl->nama}\" berhasil {$status}!");
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PeriodePKL $periodePkl) 
    { 
        if ($periodePkl->is_active) {
            return back()->withErrors(['error' => 'Tidak bisa hapus periode yang sedang aktif.']);
        }
        
        $periodePkl->delete(); 
        return redirect()->route('admin.periode-pkl.index')
            ->with('success', 'Periode PKL "'.$periodePkl->nama.'" berhasil dihapus!'); 
    }
}