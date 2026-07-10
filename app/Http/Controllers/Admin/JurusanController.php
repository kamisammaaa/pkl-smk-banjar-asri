<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jurusan;

class JurusanController extends Controller
{
    public function index() { return view('admin.jurusan.index', ['jurusan' => Jurusan::latest()->get()]); }
    public function store(Request $request) {
        $request->validate(['nama' => 'required|unique:jurusan,nama']);
        Jurusan::create($request->only('nama'));
        return back()->with('success', 'Jurusan ditambahkan!');
    }
    public function destroy(Jurusan $jurusan) { $jurusan->delete(); return back()->with('success', 'Jurusan dihapus!'); }
}