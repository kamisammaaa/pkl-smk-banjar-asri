<?php

namespace App\Http\Controllers\Siswa;
use App\Models\EOTag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class EOTAGController extends Controller
{
    public function index()
    {
        $tags = EOTag::where('siswa_user_id', auth()->id())->latest()->paginate(10);
        return view('siswa.eotag.index', compact('tags'));
    }

    public function checkin(Request $request)
    {
        $token = strtoupper(bin2hex(random_bytes(4)));
        
        EOTag::create([
            'siswa_user_id' => auth()->id(),
            'token' => $token,
            'check_in_at' => now(),
            'ip_address' => $request->ip(),
            'status' => 'valid',
        ]);

        return back()->with('success', "E-OTAG berhasil! Token: {$token} (Simpan untuk verifikasi industri)");
    }
}