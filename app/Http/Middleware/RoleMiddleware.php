<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        if (!$request->user()->hasRole($roles)) {
            abort(403, 'Akses ditolak. Peran (role) Anda tidak diizinkan untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
