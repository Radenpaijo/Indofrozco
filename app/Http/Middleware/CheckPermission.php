<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $hasAccess = false;
        foreach ($permissions as $permission) {
            if ($request->user()->hasPermission($permission)) {
                $hasAccess = true;
                break;
            }
        }

        if (!$hasAccess) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses fitur ini.');
        }

        return $next($request);
    }
}
