<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockGuestWrite
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === 'guest') {
            // Block all modifications (POST, PUT, DELETE, PATCH)
            // Allow GET, HEAD, OPTIONS
            if (!$request->isMethod('GET') && !$request->isMethod('HEAD') && !$request->isMethod('OPTIONS')) {
                // Allow logout and applyAdmin routes
                $allowedRouteNames = ['logout', 'users.applyAdmin'];
                $currentRouteName = $request->route()?->getName();
                
                if (!in_array($currentRouteName, $allowedRouteNames)) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'error' => 'Sebagai Guest, Anda tidak diperbolehkan melakukan tindakan ini (hanya untuk keperluan demo).'
                        ], 403);
                    }
                    
                    return redirect()->back()->with('guest_blocked', true);
                }
            }
        }

        return $next($request);
    }
}
