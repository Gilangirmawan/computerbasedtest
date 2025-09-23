<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->is_superadmin) {
            abort(403, 'AKSES DITOLAK. HANYA UNTUK SUPER ADMIN.');
        }
        return $next($request);
    }
}