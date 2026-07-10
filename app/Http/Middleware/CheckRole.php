<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Redirect ke login jika belum autentikasi
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Tolak akses jika akun tidak aktif
        if (!$user->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')
                ->with('error', 'Akun Anda belum diaktifkan. Hubungi administrator.');
        }

        // Tolak akses jika role tidak sesuai
        if ($user->role !== $role) {
            abort(403, 'Akses ditolak. Anda tidak memiliki hak untuk halaman ini.');
        }

        return $next($request);
    }
}