<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
public function index(Request $request)
    {
        // Start query: Exclude admin role
        $query = User::where('role', '!=', 'admin');

        // 🔍 Filter: Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // 🔍 Filter: By role (siswa/pembimbing)
        if ($request->filled('role') && in_array($request->role, ['siswa', 'pembimbing'])) {
            $query->where('role', $request->role);
        }

        // Execute query with pagination
        $users = $query->latest()->paginate(15);

        // Append query params to pagination links
        $users->appends($request->only('search', 'role'));

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:pembimbing,siswa'
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => true
        ]);

        return redirect()->route('admin.users.index')->with('success', '✅ User berhasil ditambahkan!');
    }

    public function edit(User $user)
    {
        if ($user->role === 'admin') abort(403, 'Tidak dapat mengedit admin.');
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->role === 'admin') abort(403, 'Tidak dapat mengedit admin.');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:6',
            'role' => 'required|in:pembimbing,siswa',
            'is_active' => 'boolean'
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active')
        ];

        if ($validated['password']) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);
        return redirect()->route('admin.users.index')->with('success', '✅ User berhasil diupdate!');
    }

    public function destroy(User $user)
    {
        if ($user->role === 'admin' || $user->id === auth()->id()) {
            abort(403, 'Tidak dapat menghapus akun ini.');
        }
        $user->delete();
        return back()->with('success', '🗑️ User berhasil dihapus!');
    }

    // ================= IMPORT FEATURES =================
    public function importForm()
    {
        return view('admin.users.import');
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:5120' // Max 5MB
        ]);

        $path = $request->file('file')->getRealPath();
        $handle = fopen($path, 'r');
        
        // Skip header row
        fgetcsv($handle);

        $imported = 0;
        $skipped = 0;
        $errors = [];

        while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
            if (count($row) < 4) continue; // Skip baris kosong/rusak

            [$name, $email, $password, $role] = array_map('trim', $row);

            // Validasi sederhana per baris
            if (!in_array(strtolower($role), ['siswa', 'pembimbing'])) {
                $errors[] = "Role '$role' tidak valid pada data $email";
                continue;
            }

            if (User::where('email', $email)->exists()) {
                $skipped++;
                continue;
            }

            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => strtolower($role),
                'is_active' => true
            ]);
            $imported++;
        }
        fclose($handle);

        $msg = "✅ Berhasil import: {$imported} user. | ⏭️ Dilewati (email duplikat): {$skipped}";
        if (!empty($errors)) {
            $msg .= "<br>⚠️ Error: " . implode(', ', array_unique($errors));
        }

        return back()->with('success', $msg);
    }

        /**
     * Display pending student registrations for admin approval.
     */
    public function registrasi()
    {
        $pendingSiswa = User::where('role', 'siswa')
            ->where('is_active', false)
            ->with('siswaProfile.jurusan')
            ->latest()
            ->paginate(15);
            
        return view('admin.registrasi.index', compact('pendingSiswa'));
    }

    /**
     * Approve a pending student registration.
     */
    public function approveRegistrasi(User $user)
    {
        if ($user->role !== 'siswa') abort(403);
        
        $user->update(['is_active' => true]);
        
        return back()->with('success', "✅ Akun {$user->name} telah diaktifkan!");
    }

    /**
     * Reject a pending student registration.
     */
    public function rejectRegistrasi(User $user)
    {
        if ($user->role !== 'siswa') abort(403);
        
        $userName = $user->name;
        $user->delete();
        
        return back()->with('success', "❌ Registrasi {$userName} ditolak.");
    }
}