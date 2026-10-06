<?php

namespace App\Http\Controllers\Instansi;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\ReportAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstansiDashboardController extends Controller
{
    /**
     * Dashboard Khusus Instansi / OPD
     */
    public function index()
    {
        $user = Auth::user();
        $org = Organization::where('opd_id', $user->opd_id)->first();

        if (!$org && $user->isSuperAdmin()) {
            $org = Organization::first();
        }

        $orgId = $org ? $org->id : null;

        $assignmentsQuery = ReportAssignment::with(['laporan.kategori', 'laporan.subcategory', 'officer', 'department'])
            ->where('organization_id', $orgId);

        $totalLaporan = (clone $assignmentsQuery)->count();
        $laporanMasuk = (clone $assignmentsQuery)->whereIn('status', ['received', 'verification'])->count();
        $laporanProses = (clone $assignmentsQuery)->whereIn('status', ['assigned', 'in_progress', 'investigation', 'action'])->count();
        $laporanSelesai = (clone $assignmentsQuery)->where('status', 'resolved')->count();

        // Hitung laporan terlambat (overdue)
        $laporanOverdue = (clone $assignmentsQuery)
            ->where('status', '!=', 'resolved')
            ->whereNotNull('sla_due_at')
            ->where('sla_due_at', '<', now())
            ->count();

        // Petugas aktif di instansi ini
        $petugasList = User::where('role', 'petugas')
            ->where('opd_id', $user->opd_id)
            ->get();

        // Laporan terbaru
        $recentAssignments = (clone $assignmentsQuery)
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        return view('instansi.dashboard', compact(
            'org',
            'totalLaporan',
            'laporanMasuk',
            'laporanProses',
            'laporanSelesai',
            'laporanOverdue',
            'petugasList',
            'recentAssignments'
        ));
    }
}
