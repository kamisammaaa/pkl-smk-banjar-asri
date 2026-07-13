<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandlePostTooLarge
{
    /**
     * Handle an incoming request.
     *
     * When the uploaded file exceeds PHP's post_max_size, PHP empties
     * both $_POST and $_FILES entirely. We detect this by checking if
     * the Content-Length header exceeds post_max_size.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $maxSize = $this->parseSize(ini_get('post_max_size'));

        if ($maxSize > 0 && $request->server('CONTENT_LENGTH') > $maxSize) {
            $maxMb = round($maxSize / 1024 / 1024, 0);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "❌ Ukuran file terlalu besar! Maksimal upload adalah {$maxMb}MB per request.",
                ], 413);
            }

            return back()
                ->withInput()
                ->withErrors([
                    'foto' => "❌ Ukuran file terlalu besar! Server hanya mengizinkan upload maksimal {$maxMb}MB. Silakan kompres foto Anda terlebih dahulu.",
                ]);
        }

        return $next($request);
    }

    /**
     * Convert PHP ini size string (e.g., "20M", "512K") to bytes.
     */
    private function parseSize(string $size): int
    {
        $unit = strtoupper(substr($size, -1));
        $value = (int) $size;

        return match ($unit) {
            'G' => $value * 1024 * 1024 * 1024,
            'M' => $value * 1024 * 1024,
            'K' => $value * 1024,
            default => $value,
        };
    }
}
