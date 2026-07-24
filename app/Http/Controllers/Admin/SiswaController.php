<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SiswaProfile;
use App\Models\Jurusan;
use App\Models\Perusahaan;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    /**
     * Display list of all students
     */
    
    /**
     * Display list of all students with search & filter
     */
    public function index(Request $request)
    {
        // Start query: Only students, exclude admin/pembimbing
        $query = User::where('role', 'siswa');

        // 🔍 Filter: Search by name, email, or NIS
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  // Search juga di tabel siswa_profiles (NIS)
                  ->orWhereHas('siswaProfile', function($sq) use ($search) {
                      $sq->where('nis', 'like', "%{$search}%");
                  });
            });
        }

        // 🔍 Filter: By Jurusan
        if ($request->filled('jurusan_id') && is_numeric($request->jurusan_id)) {
            $query->whereHas('siswaProfile', function($q) use ($request) {
                $q->where('jurusan_id', $request->jurusan_id);
            });
        }

        // 🔍 Filter: By Pembimbing (assigned/unassigned)
        if ($request->filled('pembimbing_status')) {
            if ($request->pembimbing_status === 'assigned') {
                $query->whereHas('siswaProfile', function($q) {
                    $q->whereNotNull('pembimbing_id');
                });
            } elseif ($request->pembimbing_status === 'unassigned') {
                // Siswa tanpa profil atau profil belum punya pembimbing
                $query->where(function($q) {
                    $q->whereDoesntHave('siswaProfile')
                      ->orWhereHas('siswaProfile', function($sq) {
                          $sq->whereNull('pembimbing_id');
                      });
                });
            }
        }

        // 🔍 Filter: By Perusahaan (assigned/unassigned)
        if ($request->filled('perusahaan_status')) {
            if ($request->perusahaan_status === 'assigned') {
                $query->whereHas('siswaProfile', function($q) {
                    $q->whereNotNull('perusahaan_id');
                });
            } elseif ($request->perusahaan_status === 'unassigned') {
                // Siswa tanpa profil atau profil belum punya perusahaan
                $query->where(function($q) {
                    $q->whereDoesntHave('siswaProfile')
                      ->orWhereHas('siswaProfile', function($sq) {
                          $sq->whereNull('perusahaan_id');
                      });
                });
            }
        }

        // 🔍 Filter: By Perusahaan ID
        if ($request->filled('perusahaan_id') && is_numeric($request->perusahaan_id)) {
            $query->whereHas('siswaProfile', function($q) use ($request) {
                $q->where('perusahaan_id', $request->perusahaan_id);
            });
        }

        // Execute query with eager loading & pagination
        $siswa = $query->with(['siswaProfile.jurusan', 'siswaProfile.perusahaan', 'siswaProfile.pembimbing'])
            ->latest()
            ->paginate(15);

        // Preserve query params in pagination links
        $siswa->appends($request->only('search', 'jurusan_id', 'pembimbing_status', 'perusahaan_status', 'perusahaan_id'));

        // Load data for filter dropdowns
        $jurusanList = \App\Models\Jurusan::orderBy('nama')->get();
        $perusahaanList = \App\Models\Perusahaan::orderBy('nama')->get();
        $pembimbingList = \App\Models\User::where('role', 'pembimbing')->where('is_active', 1)->orderBy('name')->get();

        return view('admin.siswa.index', compact('siswa', 'jurusanList', 'perusahaanList', 'pembimbingList'));
    }
    /**
     * Show form to edit/assign student
     */
    public function edit(User $user)
    {
        // Only allow editing students
        if ($user->role !== 'siswa') {
            abort(403, 'User bukan siswa');
        }

        // ✅ FIX: Hapus where('is_active') untuk tabel jurusan & perusahaan
        // karena kolom tersebut mungkin belum ada di migration database Anda.
        
        $jurusan = Jurusan::orderBy('nama')->get();
        $perusahaan = Perusahaan::orderBy('nama')->get();
        
        // ✅ User table (pembimbing) TETAP pakai filter is_active
        // karena tabel users memang memiliki kolom ini.
        $pembimbing = User::where('role', 'pembimbing')
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();
        
        $profile = $user->siswaProfile;

        return view('admin.siswa.edit', compact(
            'user',
            'jurusan',
            'perusahaan',
            'pembimbing',
            'profile'
        ));
    }

    /**
     * Update student assignment
     */
    public function update(Request $request, User $user)
    {
        // Only allow updating students
        if ($user->role !== 'siswa') {
            abort(403, 'User bukan siswa');
        }

        // Build validation rules
        $rules = [
            'nis' => 'required|string|max:20',
            'jurusan_id' => 'required|exists:jurusan,id',
            'perusahaan_id' => 'nullable|exists:perusahaan,id',
            'pembimbing_id' => 'nullable|exists:users,id'
        ];

        // Add unique rule for NIS (exclude current student's profile if exists)
        if ($user->siswaProfile && $user->siswaProfile->id) {
            $rules['nis'] .= '|unique:siswa_profiles,nis,' . $user->siswaProfile->id . ',id';
        } else {
            $rules['nis'] .= '|unique:siswa_profiles,nis';
        }

        // Validate request
        $validated = $request->validate($rules, [
            'nis.required' => 'NIS wajib diisi',
            'nis.unique' => 'NIS sudah digunakan oleh siswa lain',
            'jurusan_id.required' => 'Jurusan wajib dipilih',
            'jurusan_id.exists' => 'Jurusan tidak valid',
            'perusahaan_id.exists' => 'Perusahaan tidak valid',
            'pembimbing_id.exists' => 'Pembimbing tidak valid',
        ]);

        // Convert empty strings to null for nullable fields
        // This prevents storing "" instead of NULL in database
        $perusahaanId = empty($validated['perusahaan_id']) ? null : $validated['perusahaan_id'];
        $pembimbingId = empty($validated['pembimbing_id']) ? null : $validated['pembimbing_id'];

        // Update or create student profile
        SiswaProfile::updateOrCreate(
            [
                'user_id' => $user->id
            ],
            [
                'nis' => $validated['nis'],
                'jurusan_id' => $validated['jurusan_id'],
                'perusahaan_id' => $perusahaanId,
                'pembimbing_id' => $pembimbingId,
                'updated_at' => now(),
            ]
        );

        return redirect()
            ->route('admin.siswa.index')
            ->with('success', "✅ Data siswa {$user->name} berhasil diupdate!");
    }

    /**
     * Assign massal siswa ke pembimbing/perusahaan
     */
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'siswa_ids' => 'required|array|min:1',
            'siswa_ids.*' => 'exists:users,id',
            'pembimbing_id' => 'nullable|exists:users,id',
            'perusahaan_id' => 'nullable|exists:perusahaan,id',
        ]);

        $count = 0;
        
        if (empty($request->pembimbing_id) && empty($request->perusahaan_id)) {
            return back()->with('error', '❌ Pilih minimal Pembimbing atau Perusahaan untuk di-assign.');
        }

        foreach ($request->siswa_ids as $userId) {
            // Cek apakah ini benar-benar siswa
            $user = User::where('id', $userId)->where('role', 'siswa')->first();
            if (!$user) continue;

            $profile = SiswaProfile::firstOrNew(['user_id' => $user->id]);
            
            if (!empty($request->pembimbing_id)) {
                $profile->pembimbing_id = $request->pembimbing_id;
            }
            if (!empty($request->perusahaan_id)) {
                $profile->perusahaan_id = $request->perusahaan_id;
            }
            
            // Dummy jurusan_id if doesn't exist to satisfy DB constraint if needed
            if (!$profile->exists && !$profile->jurusan_id) {
                // If they don't have a profile yet, they must at least have a valid jurusan
                // But normally users are imported with a Jurusan. 
                // Let's just try to save it, if it fails due to null jurusan, they must edit it individually.
                $profile->jurusan_id = $profile->jurusan_id ?? Jurusan::first()->id;
            }
            
            $profile->save();
            $count++;
        }

        return redirect()
            ->route('admin.siswa.index')
            ->with('success', "✅ Berhasil menetapkan (assign) {$count} siswa secara massal.");
    }
}