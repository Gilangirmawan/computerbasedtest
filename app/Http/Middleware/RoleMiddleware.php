<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $roleName = Role::find(Auth::user()->role_id)->name;
        if (!Auth::check() || !in_array($roleName, $roles)){
            return back('');
        }
        return $next($request);
    }

    // public function handle(Request $request, Closure $next, ...$roles): Response
    // {
    //     if (!Auth::check()) {
    //         return redirect()->route('login');
    //     }

    //     $user = Auth::user();

    //     // Normalisasi daftar role dari parameter (dukung "guru,admin" dan spasi)
    //     $allowedNames = collect($roles)
    //         ->flatMap(fn ($r) => explode(',', $r))
    //         ->map(fn ($r) => strtolower(trim($r)))
    //         ->filter()
    //         ->values()
    //         ->all();

    //     // Dukung juga angka (mis. role:2)
    //     $allowedIds = collect($roles)
    //         ->flatMap(fn ($r) => explode(',', $r))
    //         ->map(fn ($r) => trim($r))
    //         ->filter(fn ($r) => ctype_digit($r))
    //         ->map(fn ($r) => (int) $r)
    //         ->values()
    //         ->all();

    //     $role = Role::find($user->role_id);
    //     $userRoleName = strtolower(trim(optional($role)->name ?? ''));

    //     if ($userRoleName === '') {
    //         abort(403, 'Role tidak ditemukan.');
    //     }

    //     if (!in_array($userRoleName, $allowedNames, true)
    //         && !in_array((int) $user->role_id, $allowedIds, true)) {
    //         abort(403, 'Anda tidak punya akses ke halaman ini.');
    //     }

    //     return $next($request);
    // }
}
