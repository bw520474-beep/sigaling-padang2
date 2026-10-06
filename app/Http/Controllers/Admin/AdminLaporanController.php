<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\LaporanOpd;
use App\Models\Opd;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminLaporanController extends Controller
{
    /**
     * Tabel Daftar Seluruh Laporan
     */
    public function index(Request $request)
    {
        $query = Laporan::with(['kategori', 'user', 'laporanOpd.opd']);

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter kategori
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        // Filter prioritas
        if ($request->filled('prioritas')) {
            $query->where('prioritas', $request->prioritas);
        }

        // Filter keyword pencarian
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('kode_laporan', 'like', "%{$s}%")
                    ->orWhere('judul', 'like', "%{$s}%")
                    ->orWhere('nama_pelapor', 'like', "%{$s}%")
                    ->orWhere('kecamatan', 'like', "%{$s}%")
                    ->orWhere('alamat', 'like', "%{$s}%");
            });
        }

        // Handle Export CSV
        if ($request->query('export') === 'csv') {
            return $this->exportCsv($query->orderBy('created_at', 'desc')->get());
        }

        $laporans = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();
        $kategoris = Kategori::orderBy('nama_kategori', 'asc')->get();

        return view('admin.laporan.index', compact('laporans', 'kategoris'));
    }

    /**
     * Halaman Filter & Rekap Pengaduan Masyarakat
     */
    public function rekap(Request $request)
    {
        $query = Laporan::with(['kategori', 'user', 'laporanOpd.opd', 'laporanOpd.petugas']);

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter kategori
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        // Filter kecamatan
        if ($request->filled('kecamatan')) {
            $query->where('kecamatan', $request->kecamatan);
        }

        // Filter OPD
        if ($request->filled('opd_id')) {
            $query->whereHas('laporanOpd', function ($q) use ($request) {
                $q->where('opd_id', $request->opd_id);
            });
        }

        // Filter rentang tanggal
        if ($request->filled('tgl_mulai')) {
            $query->whereDate('created_at', '>=', $request->tgl_mulai);
        }
        if ($request->filled('tgl_selesai')) {
            $query->whereDate('created_at', '<=', $request->tgl_selesai);
        }

        // Ringkasan metrik rekap
        $baseQuery = clone $query;
        $totalLaporan = (clone $baseQuery)->count();
        $totalMenunggu = (clone $baseQuery)->where('status', 'Menunggu Verifikasi')->count();
        $totalProses = (clone $baseQuery)->whereIn('status', ['Terverifikasi', 'Dalam Penanganan'])->count();
        $totalSelesai = (clone $baseQuery)->where('status', 'Selesai')->count();
        $totalDitolak = (clone $baseQuery)->where('status', 'Ditolak')->count();

        $laporans = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $kategoris = Kategori::orderBy('nama_kategori', 'asc')->get();
        $opds = Opd::orderBy('nama_opd', 'asc')->get();

        $kecamatanList = [
            'Bungus Teluk Kabung',
            'Koto Tangah',
            'Kuranji',
            'Lubuk Begalung',
            'Lubuk Kilangan',
            'Nanggalo',
            'Padang Barat',
            'Padang Selatan',
            'Padang Timur',
            'Padang Utara',
            'Pauh'
        ];

        return view('admin.laporan.rekap', compact(
            'laporans',
            'kategoris',
            'opds',
            'kecamatanList',
            'totalLaporan',
            'totalMenunggu',
            'totalProses',
            'totalSelesai',
            'totalDitolak'
        ));
    }

    /**
     * Tampilan Cetak / Print Dokumen Rekap Resmi (Kop Surat Pemko Padang)
     */
    public function cetak(Request $request)
    {
        $query = Laporan::with(['kategori', 'user', 'laporanOpd.opd', 'laporanOpd.petugas']);

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter kategori
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        // Filter kecamatan
        if ($request->filled('kecamatan')) {
            $query->where('kecamatan', $request->kecamatan);
        }

        // Filter OPD
        if ($request->filled('opd_id')) {
            $query->whereHas('laporanOpd', function ($q) use ($request) {
                $q->where('opd_id', $request->opd_id);
            });
        }

        // Filter rentang tanggal
        if ($request->filled('tgl_mulai')) {
            $query->whereDate('created_at', '>=', $request->tgl_mulai);
        }
        if ($request->filled('tgl_selesai')) {
            $query->whereDate('created_at', '<=', $request->tgl_selesai);
        }

        $laporans = $query->orderBy('created_at', 'asc')->get();

        // Rekap ringkas
        $totalLaporan = $laporans->count();
        $totalSelesai = $laporans->where('status', 'Selesai')->count();
        $totalProses = $laporans->whereIn('status', ['Terverifikasi', 'Dalam Penanganan'])->count();
        $totalMenunggu = $laporans->where('status', 'Menunggu Verifikasi')->count();
        $totalDitolak = $laporans->where('status', 'Ditolak')->count();

        $selectedOpd = $request->filled('opd_id') ? Opd::find($request->opd_id) : null;
        $selectedKategori = $request->filled('kategori_id') ? Kategori::find($request->kategori_id) : null;

        return view('admin.laporan.cetak', compact(
            'laporans',
            'totalLaporan',
            'totalSelesai',
            'totalProses',
            'totalMenunggu',
            'totalDitolak',
            'selectedOpd',
            'selectedKategori'
        ));
    }

    /**
     * Detail Laporan, Verifikasi & Disposisi
     */
    public function show($id)
    {
        $laporan = Laporan::with([
            'kategori',
            'subcategory',
            'user',
            'reporterIdentity',
            'fotoBuktiLaporan',
            'fotoBuktiPenanganan',
            'assignments.organization',
            'assignments.department',
            'assignments.officer',
            'assignments.supervisor',
            'laporanOpd.opd',
            'laporanOpd.petugas',
            'penanganans.petugas',
            'auditLogs' => fn($q) => $q->orderBy('created_at', 'desc')
        ])->findOrFail($id);

        $opds = Opd::orderBy('nama_opd', 'asc')->get();
        $organizations = \App\Models\Organization::with('departments')->where('is_active', true)->orderBy('name')->get();

        return view('admin.laporan.detail', compact('laporan', 'opds', 'organizations'));
    }

    /**
     * Update Status & Prioritas Laporan
     */
    public function updateStatus(Request $request, $id)
    {
        $laporan = Laporan::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:Menunggu Verifikasi,Terverifikasi,Dalam Penanganan,Selesai,Ditolak',
            'prioritas' => 'required|in:Rendah,Sedang,Tinggi,Darurat',
        ]);

        $oldStatus = $laporan->status;
        $laporan->status = $validated['status'];
        $laporan->prioritas = $validated['prioritas'];
        $laporan->save();

        \App\Services\AuditTrailService::log(
            'update_status',
            'report',
            $laporan->id,
            ['status' => $oldStatus],
            ['status' => $laporan->status, 'prioritas' => $laporan->prioritas]
        );

        return back()->with('success', "Status laporan {$laporan->kode_laporan} berhasil diperbarui menjadi \"{$laporan->status}\".");
    }

    /**
     * Teruskan Disposisi Laporan ke OPD (Legacy & Smart Sync)
     */
    public function teruskan(Request $request, $id)
    {
        $laporan = Laporan::findOrFail($id);

        $validated = $request->validate([
            'opd_id' => 'required|exists:opd,id',
            'catatan' => 'nullable|string',
        ], [
            'opd_id.required' => 'Pilih salah satu instansi OPD yang berwenang.',
        ]);

        LaporanOpd::create([
            'laporan_id' => $laporan->id,
            'opd_id' => $validated['opd_id'],
            'petugas_id' => null,
            'catatan' => $validated['catatan'] ?? null,
            'tanggal_diteruskan' => now(),
        ]);

        // Sync ke report_assignments
        $org = \App\Models\Organization::where('opd_id', $validated['opd_id'])->first();
        if ($org) {
            \App\Models\ReportAssignment::create([
                'report_id' => $laporan->id,
                'organization_id' => $org->id,
                'status' => 'received',
                'priority' => $laporan->prioritas ?: 'Sedang',
                'sla_due_at' => now()->addDays(3),
                'notes' => $validated['catatan'] ?? 'Disposisi manual oleh Administrator',
            ]);
        }

        // Otomatis ubah status menjadi Terverifikasi jika masih Menunggu
        if ($laporan->status === 'Menunggu Verifikasi') {
            $laporan->status = 'Terverifikasi';
            $laporan->save();
        }

        $opd = Opd::find($validated['opd_id']);

        \App\Services\AuditTrailService::log(
            'forward_report',
            'report',
            $laporan->id,
            null,
            ['opd' => $opd ? $opd->nama_opd : 'OPD']
        );

        return back()->with('success', "Laporan berhasil diteruskan ke disposisi {$opd->nama_opd}.");
    }

    /**
     * Multi-Agency Assignment (Menambahkan Penugasan Instansi Kolaboratif pada Satu Laporan)
     */
    public function multiAssign(Request $request, $id)
    {
        $laporan = Laporan::findOrFail($id);

        $validated = $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'department_id' => 'nullable|exists:departments,id',
            'notes' => 'required|string',
            'priority' => 'nullable|in:Rendah,Sedang,Tinggi,Darurat',
        ], [
            'organization_id.required' => 'Pilih instansi kolaboratif yang akan ditugaskan.',
            'notes.required' => 'Sertakan uraian tugas untuk instansi pendukung.',
        ]);

        $assignment = \App\Models\ReportAssignment::create([
            'report_id' => $laporan->id,
            'organization_id' => $validated['organization_id'],
            'department_id' => $validated['department_id'] ?: null,
            'status' => 'received',
            'priority' => $validated['priority'] ?: ($laporan->prioritas ?: 'Sedang'),
            'sla_due_at' => now()->addDays(3),
            'notes' => $validated['notes'],
        ]);

        $org = \App\Models\Organization::find($validated['organization_id']);

        // Bridge ke legacy jika ada opd_id
        if ($org && $org->opd_id) {
            LaporanOpd::create([
                'laporan_id' => $laporan->id,
                'opd_id' => $org->opd_id,
                'catatan' => $validated['notes'],
                'tanggal_diteruskan' => now(),
            ]);
        }

        \App\Services\AuditTrailService::log(
            'add_collaborative_agency',
            'report',
            $laporan->id,
            null,
            ['organization' => $org ? $org->name : 'Org', 'notes' => $validated['notes']]
        );

        return back()->with('success', "Penugasan instansi kolaboratif \"{$org->name}\" berhasil ditambahkan pada laporan ini.");
    }

    /**
     * Hapus Laporan
     */
    public function destroy($id)
    {
        $laporan = Laporan::findOrFail($id);
        $kode = $laporan->kode_laporan;

        // Delete photo files
        foreach ($laporan->fotoLaporan as $foto) {
            $filePath = public_path('uploads/laporan/' . $foto->nama_file);
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        $laporan->delete();

        return redirect()->route('admin.laporan.index')->with('success', "Laporan {$kode} berhasil dihapus.");
    }

    /**
     * Ekspor Laporan ke CSV
     */
    private function exportCsv($laporans): StreamedResponse
    {
        $fileName = 'Rekap_Laporan_SIGALING_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename={$fileName}",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($laporans) {
            $file = fopen('php://output', 'w');
            // BOM UTF-8 for Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'No',
                'Kode Laporan',
                'Tanggal Lapor',
                'Kategori',
                'Judul Pengaduan',
                'Deskripsi',
                'Kecamatan',
                'Kelurahan',
                'Alamat',
                'Nama Pelapor',
                'Kontak',
                'Status',
                'Prioritas',
            ]);

            $no = 1;
            foreach ($laporans as $r) {
                fputcsv($file, [
                    $no++,
                    $r->kode_laporan,
                    $r->created_at ? $r->created_at->format('Y-m-d H:i') : '-',
                    $r->kategori ? $r->kategori->nama_kategori : '-',
                    $r->judul,
                    $r->deskripsi,
                    $r->kecamatan,
                    $r->kelurahan,
                    $r->alamat,
                    $r->nama_pelapor,
                    $r->kontak_pelapor,
                    $r->status,
                    $r->prioritas,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
