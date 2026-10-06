<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OrganizationAccessMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Superadmin dan Auditor memiliki akses lintas instansi (oversight / pengawasan)
        if ($user->isSuperAdmin() || $user->isAuditor()) {
            return $next($request);
        }

        // Untuk Admin Instansi, Supervisor, dan Petugas, mereka harus terikat pada OPD/Instansi
        if (!$user->opd_id) {
            abort(403, 'Akun Anda belum diasosiasikan dengan instansi / OPD manapun. Silakan hubungi Administrator.');
        }

        return $next($request);
    }
}
