<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        // 🔒 Cek: Jika user ada tapi belum aktif (pending approval)
        if ($user && !$user->is_active) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Akun Anda belum disetujui oleh administrator. Silakan tunggu atau hubungi admin.',
            ])->withInput();
        }

        return redirect()->intended(match($user->role) {
            'admin' => route('admin.dashboard'),
            'pembimbing' => route('pembimbing.dashboard'),
            'siswa' => route('siswa.dashboard'),
            default => route('home'),
        });
    }

    /**
     * 🔥 BARU: Destroy an authenticated session (Logout).
     * Method ini wajib ada agar tombol logout berfungsi.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}