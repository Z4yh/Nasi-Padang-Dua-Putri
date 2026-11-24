<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Menangani permintaan masuk dan memvalidasi peran (role) pengguna.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Pastikan pengguna sudah login
        if (! $request->user()) {
            if ($request->is('api/*') || $request->expectsJson()) {
                abort(401, 'Unauthenticated.');
            }
            return redirect()->route('login');
        }

        $userRole = $request->user()->role;

        // 2. Normalisasi alias role (misal: 'admin' mencakup 'penjual' & 'super_admin')
        $allowedRoles = [];
        foreach ($roles as $role) {
            if ($role === 'admin') {
                $allowedRoles[] = 'penjual';
                $allowedRoles[] = 'super_admin';
            } else {
                $allowedRoles[] = $role;
            }
        }

        // 3. Periksa apakah role pengguna cocok
        if (! in_array($userRole, $allowedRoles, true)) {
            abort(403, 'Tidak punya akses');
        }

        return $next($request);
    }
}
