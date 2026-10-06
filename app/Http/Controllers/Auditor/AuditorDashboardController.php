<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Laporan;
use App\Models\ReportAssignment;
use Illuminate\Http\Request;

class AuditorDashboardController extends Controller
{
    /**
     * Dashboard Pengawas / Auditor
     */
    public function index()
    {
        $totalLaporan = Laporan::count();
        $totalSelesai = Laporan::where('status', 'Selesai')->count();
        $totalOverdue = Laporan::where('status', '!=', 'Selesai')
            ->whereNotNull('sla_due_at')
            ->where('sla_due_at', '<', now())
            ->count();

        $slaComplianceRate = $totalLaporan > 0 ? round((($totalLaporan - $totalOverdue) / $totalLaporan) * 100, 1) : 100;

        $recentLogs = AuditLog::orderBy('created_at', 'desc')->limit(10)->get();

        $overdueAssignments = ReportAssignment::with(['laporan', 'organization', 'officer'])
            ->where('status', '!=', 'resolved')
            ->whereNotNull('sla_due_at')
            ->where('sla_due_at', '<', now())
            ->orderBy('sla_due_at', 'asc')
            ->limit(10)
            ->get();

        return view('auditor.dashboard', compact(
            'totalLaporan',
            'totalSelesai',
            'totalOverdue',
            'slaComplianceRate',
            'recentLogs',
            'overdueAssignments'
        ));
    }

    /**
     * Seluruh Laporan (Read-Only)
     */
    public function laporan(Request $request)
    {
        $query = Laporan::with(['kategori', 'subcategory', 'assignments.organization', 'reporterIdentity']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('kode_laporan', 'like', "%{$s}%")
                  ->orWhere('judul', 'like', "%{$s}%")
                  ->orWhere('alamat', 'like', "%{$s}%");
            });
        }

        $laporans = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('auditor.laporan', compact('laporans'));
    }

    /**
     * Audit Log Trail
     */
    public function auditLog(Request $request)
    {
        return $this->auditLogs($request);
    }

    public function auditLogs(Request $request)
    {
        $query = AuditLog::with('actor');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('actor_name', 'like', "%{$s}%")
                  ->orWhere('action', 'like', "%{$s}%")
                  ->orWhere('entity_id', 'like', "%{$s}%");
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('auditor.audit-log', compact('logs'));
    }
}
