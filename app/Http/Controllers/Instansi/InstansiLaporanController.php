<?php

namespace App\Http\Controllers\Instansi;

use App\Http\Controllers\Controller;
use App\Models\FotoLaporan;
use App\Models\Laporan;
use App\Models\LaporanOpd;
use App\Models\Organization;
use App\Models\Penanganan;
use App\Models\ReportAssignment;
use App\Models\User;
use App\Services\AuditTrailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstansiLaporanController extends Controller
{
    /**
     * Daftar Penugasan Laporan Instansi
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $org = Organization::where('opd_id', $user->opd_id)->first();
        $orgId = $org ? $org->id : null;

        $query = ReportAssignment::with(['laporan.kategori', 'laporan.subcategory', 'officer', 'department', 'laporan.fotoBuktiLaporan'])
            ->where('organization_id', $orgId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('officer_id')) {
            $query->where('officer_id', $request->officer_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('laporan', function ($q) use ($s) {
                $q->where('kode_laporan', 'like', "%{$s}%")
                  ->orWhere('judul', 'like', "%{$s}%")
                  ->orWhere('alamat', 'like', "%{$s}%");
            });
        }

        $assignments = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        $petugasList = User::where('role', 'petugas')
            ->where('opd_id', $user->opd_id)
            ->get();

        return view('instansi.laporan.index', compact('assignments', 'petugasList', 'org'));
    }

    /**
     * Detail Laporan & Tindakan Supervisor/Admin Instansi
     */
    public function show($id)
    {
        $user = Auth::user();
        $org = Organization::where('opd_id', $user->opd_id)->first();
        $orgId = $org ? $org->id : null;

        $assignment = ReportAssignment::with([
            'laporan.kategori',
            'laporan.subcategory',
            'laporan.fotoBuktiLaporan',
            'laporan.fotoBuktiPenanganan',
            'laporan.reporterIdentity',
            'laporan.assignments.organization',
            'laporan.assignments.officer',
            'officer',
            'department',
            'laporan.auditLogs' => fn($q) => $q->orderBy('created_at', 'desc')
        ])
        ->where('organization_id', $orgId)
        ->findOrFail($id);

        $petugasList = User::where('role', 'petugas')
            ->where('opd_id', $user->opd_id)
            ->get();

        $otherOrganizations = Organization::where('id', '!=', $orgId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('instansi.laporan.detail', compact('assignment', 'petugasList', 'otherOrganizations', 'org'));
    }

    /**
     * Verifikasi Laporan & Perbarui Status
     */
    public function verifikasi(Request $request, $id)
    {
        $user = Auth::user();
        $org = Organization::where('opd_id', $user->opd_id)->first();

        $assignment = ReportAssignment::where('organization_id', $org->id)->findOrFail($id);
        $laporan = $assignment->laporan;

        $validated = $request->validate([
            'status' => 'required|in:verification,assigned,rejected,need_more_info',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $assignment->status;
        $assignment->status = $validated['status'];
        $assignment->supervisor_id = $user->id;
        $assignment->notes = $validated['notes'] ?? $assignment->notes;
        $assignment->save();

        // Sinkronisasi status master laporan
        if ($validated['status'] === 'verification' || $validated['status'] === 'assigned') {
            $laporan->status = 'Terverifikasi';
            $laporan->save();
        } elseif ($validated['status'] === 'rejected') {
            $laporan->status = 'Ditolak';
            $laporan->save();
        }

        AuditTrailService::log(
            'verify_assignment',
            'report_assignment',
            $assignment->id,
            ['status' => $oldStatus],
            ['status' => $assignment->status, 'notes' => $assignment->notes]
        );

        return back()->with('success', "Status penugasan laporan berhasil diperbarui menjadi \"{$assignment->status}\".");
    }

    /**
     * Tugaskan Laporan kepada Petugas Lapangan
     */
    public function tugaskanPetugas(Request $request, $id)
    {
        $user = Auth::user();
        $org = Organization::where('opd_id', $user->opd_id)->first();

        $assignment = ReportAssignment::where('organization_id', $org->id)->findOrFail($id);
        $laporan = $assignment->laporan;

        $validated = $request->validate([
            'officer_id' => 'required|exists:users,id',
            'notes' => 'nullable|string',
        ], [
            'officer_id.required' => 'Pilih salah satu petugas lapangan yang bertugas.',
        ]);

        $officer = User::where('role', 'petugas')
            ->where('opd_id', $user->opd_id)
            ->findOrFail($validated['officer_id']);

        $assignment->officer_id = $officer->id;
        $assignment->supervisor_id = $user->id;
        $assignment->status = 'assigned';
        if ($validated['notes']) {
            $assignment->notes = $validated['notes'];
        }
        $assignment->save();

        // Buat atau sinkronkan record Penanganan untuk petugas
        Penanganan::firstOrCreate(
            ['laporan_id' => $laporan->id, 'petugas_id' => $officer->id],
            [
                'status' => 'Menunggu',
                'keterangan' => 'Ditugaskan oleh Supervisor: ' . ($validated['notes'] ?? 'Harap segera dilakukan tindakan di lokasi.'),
                'tanggal_penanganan' => now(),
            ]
        );

        // Update laporan status
        $laporan->status = 'Dalam Penanganan';
        $laporan->save();

        // Sync ke legacy LaporanOpd jika ada
        LaporanOpd::where('laporan_id', $laporan->id)
            ->where('opd_id', $user->opd_id)
            ->update(['petugas_id' => $officer->id]);

        AuditTrailService::log(
            'assign_officer',
            'report_assignment',
            $assignment->id,
            null,
            ['officer_name' => $officer->nama, 'notes' => $validated['notes'] ?? null]
        );

        return back()->with('success', "Laporan berhasil ditugaskan kepada petugas lapangan \"{$officer->nama}\".");
    }

    /**
     * Tambah Instansi Pendukung (Kolaborasi Lintas Instansi)
     */
    public function tambahInstansiPendukung(Request $request, $id)
    {
        $user = Auth::user();
        $org = Organization::where('opd_id', $user->opd_id)->first();

        $assignment = ReportAssignment::where('organization_id', $org->id)->findOrFail($id);
        $laporan = $assignment->laporan;

        $validated = $request->validate([
            'support_org_id' => 'required|exists:organizations,id',
            'support_notes' => 'required|string',
        ], [
            'support_org_id.required' => 'Pilih instansi mitra pendukung.',
            'support_notes.required' => 'Jelaskan alasan dan aspek yang memerlukan bantuan lintas instansi.',
        ]);

        $supportOrg = Organization::findOrFail($validated['support_org_id']);

        ReportAssignment::create([
            'report_id' => $laporan->id,
            'organization_id' => $supportOrg->id,
            'status' => 'received',
            'priority' => $assignment->priority ?: 'Sedang',
            'sla_due_at' => now()->addDays(3),
            'notes' => 'Kolaborasi lintas dinas diprakarsai oleh ' . $org->name . ': ' . $validated['support_notes'],
        ]);

        if ($supportOrg->opd_id) {
            LaporanOpd::create([
                'laporan_id' => $laporan->id,
                'opd_id' => $supportOrg->opd_id,
                'petugas_id' => null,
                'catatan' => 'Permintaan kolaborasi: ' . $validated['support_notes'],
                'tanggal_diteruskan' => now(),
            ]);
        }

        AuditTrailService::log(
            'request_cross_agency_support',
            'report',
            $laporan->id,
            null,
            ['from' => $org->name, 'to' => $supportOrg->name, 'notes' => $validated['support_notes']]
        );

        return back()->with('success', "Permintaan kolaborasi lintas instansi berhasil diteruskan ke {$supportOrg->name}.");
    }

    /**
     * Setujui Penyelesaian Laporan oleh Supervisor
     */
    public function setujuiSelesai(Request $request, $id)
    {
        $user = Auth::user();
        $org = Organization::where('opd_id', $user->opd_id)->first();

        $assignment = ReportAssignment::where('organization_id', $org->id)->findOrFail($id);
        $laporan = $assignment->laporan;

        $assignment->status = 'resolved';
        $assignment->completed_at = now();
        $assignment->completion_notes = $request->input('completion_notes', 'Pekerjaan lapangan telah diverifikasi tuntas oleh Supervisor.');
        $assignment->save();

        // Cek apakah seluruh assignment pada laporan ini sudah selesai
        $pendingAssignments = ReportAssignment::where('report_id', $laporan->id)
            ->where('status', '!=', 'resolved')
            ->count();

        if ($pendingAssignments === 0) {
            $laporan->status = 'Selesai';
            $laporan->save();
        }

        AuditTrailService::log(
            'approve_resolution',
            'report_assignment',
            $assignment->id,
            null,
            ['completed_at' => now()->toDateTimeString(), 'notes' => $assignment->completion_notes]
        );

        return back()->with('success', "Penanganan instansi dinyatakan selesai tuntas dan diverifikasi.");
    }
}
