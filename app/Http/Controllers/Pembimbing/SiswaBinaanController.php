<?php

namespace App\Http\Controllers\Pembimbing;

use App\Http\Controllers\Controller;
use App\Models\SiswaProfile;
use Illuminate\Http\Request;

class SiswaBinaanController extends Controller
{
    public function index(Request $request)
    {
        $query = SiswaProfile::where('pembimbing_id', auth()->id())
            ->with(['user', 'perusahaan', 'jurusan']);

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->orWhere('nis', 'like', "%{$search}%");
        }

        // Filter perusahaan
        if ($request->filled('perusahaan_id')) {
            $query->where('perusahaan_id', $request->perusahaan_id);
        }

        // Filter jurusan
        if ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
        }

        $siswaBinaan = $query->paginate(10);
        
        // Data untuk filter dropdown
        $perusahaanList = \App\Models\Perusahaan::where('pembimbing_id', auth()->id())->orderBy('nama')->pluck('nama', 'id');
        $jurusanList = \App\Models\Jurusan::distinct()->pluck('nama', 'id');

        return view('pembimbing.siswa-binaan', compact('siswaBinaan', 'perusahaanList', 'jurusanList'));
    }
}