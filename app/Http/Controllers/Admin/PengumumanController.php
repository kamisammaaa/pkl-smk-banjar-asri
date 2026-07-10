<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    public function index() { return view('admin.pengumuman.index', ['pengumuman' => Pengumuman::with('admin')->latest()->get()]); }
    
    public function create() { return view('admin.pengumuman.create'); }
    
    public function store(Request $request)
    {
        $validated = $request->validate(['judul' => 'required', 'isi' => 'required', 'target' => 'required|in:semua,siswa,pembimbing']);
        Pengumuman::create([
            'judul' => $validated['judul'], 'isi' => $validated['isi'], 'target' => $validated['target'],
            'admin_id' => auth()->id(), 'published_at' => now(), 'is_active' => true
        ]);
        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman diterbitkan!');
    }
    
    public function edit(Pengumuman $pengumuman) { return view('admin.pengumuman.edit', compact('pengumuman')); }
    
    public function update(Request $request, Pengumuman $pengumuman)
    {
        $validated = $request->validate(['judul' => 'required', 'isi' => 'required', 'target' => 'required|in:semua,siswa,pembimbing', 'is_active' => 'boolean']);
        $pengumuman->update($validated);
        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman diupdate!');
    }
    
    public function destroy(Pengumuman $pengumuman) { $pengumuman->delete(); return back()->with('success', 'Pengumuman dihapus!'); }
}