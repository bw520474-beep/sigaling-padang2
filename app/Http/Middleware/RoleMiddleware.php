<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan masuk ke akun Anda terlebih dahulu.');
        }

        $user = Auth::user();
        $userRole = $user->role;

        // Normalisasi alias admin / superadmin
        $allowed = false;

        foreach ($roles as $role) {
            if ($userRole === $role) {
                $allowed = true;
                break;
            }
            if (($role === 'admin' || $role === 'superadmin') && in_array($userRole, ['admin', 'superadmin'])) {
                $allowed = true;
                break;
            }
            // Superadmin dapat mengakses modul instansi dan supervisor untuk monitoring
            if (in_array($role, ['admin_instansi', 'supervisor']) && in_array($userRole, ['admin', 'superadmin'])) {
                $allowed = true;
                break;
            }
        }

        if (!$allowed) {
            if (in_array($userRole, ['superadmin', 'admin'])) {
                return redirect()->route('admin.dashboard');
            } elseif (in_array($userRole, ['admin_instansi', 'supervisor'])) {
                return redirect()->route('instansi.dashboard');
            } elseif ($userRole === 'petugas') {
                return redirect()->route('petugas.dashboard');
            } elseif ($userRole === 'auditor') {
                return redirect()->route('auditor.dashboard');
            } else {
                return redirect()->route('home')->with('error', 'Anda tidak memiliki hak akses ke halaman tersebut.');
            }
        }

        return $next($request);
    }
}
