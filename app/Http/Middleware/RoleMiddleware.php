<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        // Super Admin gets access to everything
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Guests can view all pages/features in a read-only state
        if ($user->role === 'guest' && ($request->isMethod('GET') || $request->isMethod('HEAD'))) {
            return $next($request);
        }

        if ($user->role === 'guest') {
            abort(403, 'Akses ditolak. Sebagai Guest, Anda tidak memiliki izin untuk melakukan tindakan/perubahan data pada halaman ini.');
        }

        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
    }
}
