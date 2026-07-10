<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SiswaProfile;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * Show registration form for students.
     */
    public function showRegistrationForm()
    {
        $jurusanList = Jurusan::orderBy('nama')->get();
        return view('auth.register', compact('jurusanList'));
    }

    /**
     * Handle student registration.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nis' => 'required|string|max:20|unique:siswa_profiles,nis',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'jurusan_id' => 'required|exists:jurusan,id',
            'kelas' => 'required|string|max:10',
        ], [
            'nis.unique' => 'NIS ini sudah terdaftar. Silakan login atau hubungi admin.',
            'nis.required' => 'NIS wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Validasi format NIS (opsional: sesuaikan dengan format sekolah)
        if (!preg_match('/^\d{4,20}$/', $request->nis)) {
            return redirect()->back()->withErrors(['nis' => 'Format NIS tidak valid.'])->withInput();
        }

        // Buat user baru dengan role 'siswa' dan status pending
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'siswa',
            'is_active' => false, // ⚠️ Default: non-aktif, menunggu approval admin
        ]);

        // Buat profile siswa
        SiswaProfile::create([
            'user_id' => $user->id,
            'nis' => $request->nis,
            'jurusan_id' => $request->jurusan_id,
            'kelas' => $request->kelas,
            // perusahaan_id & pembimbing_id diisi nanti oleh admin
        ]);

        return redirect()->route('login')->with('success', '✅ Registrasi berhasil! Akun Anda menunggu persetujuan admin.');
    }
}