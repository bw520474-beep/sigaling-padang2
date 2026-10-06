<?php

namespace App\Http\Controllers;

use App\Models\FotoLaporan;
use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\LaporanOpd;
use App\Models\Region;
use App\Models\ReportAssignment;
use App\Models\ReporterIdentity;
use App\Models\Subcategory;
use App\Services\AuditTrailService;
use App\Services\RoutingEngineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LaporanController extends Controller
{
    protected RoutingEngineService $routingEngine;

    public function __construct(RoutingEngineService $routingEngine)
    {
        $this->routingEngine = $routingEngine;
    }

    /**
     * Formulir Buat Laporan Pengaduan Civic Platform
     */
    public function create(Request $request)
    {
        $kategoriList = Kategori::with(['subcategories' => function ($q) {
            $q->where('is_active', true)->orderBy('name', 'asc');
        }])->orderBy('nama_kategori', 'asc')->get();

        $districts = Region::where('type', 'district')
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        $selectedKategori = $request->query('kategori');
        $selectedSubkategori = $request->query('subkategori');
        $user = Auth::user();

        return view('masyarakat.buat-laporan', compact(
            'kategoriList',
            'districts',
            'selectedKategori',
            'selectedSubkategori',
            'user'
        ));
    }

    /**
     * API Suggestion Klasifikasi Cerdas & Routing Otomatis
     */
    public function suggest(Request $request)
    {
        $text = $request->input('text', '');
        if (strlen($text) < 3) {
            return response()->json(['matched' => false]);
        }

        $result = $this->routingEngine->suggestClassification($text);
        return response()->json($result);
    }

    /**
     * Simpan Laporan Baru ke Database
     */
    public function store(Request $request)
    {
        $protectionLevel = $request->input('protection_level', 'public');

        $rules = [
            'kategori_id' => 'required|exists:kategori,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'alamat' => 'required|string',
            'kecamatan' => 'nullable|string|max:100',
            'kelurahan' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'protection_level' => 'required|in:public,confidential,anonymous',
            'kontak_pelapor' => 'nullable|string|max:50',
            'id_card_number' => 'nullable|string|max:50',
            'foto.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ];

        // Nama pelapor wajib jika public atau confidential
        if ($protectionLevel !== 'anonymous') {
            $rules['nama_pelapor'] = 'required|string|max:255';
        } else {
            $rules['nama_pelapor'] = 'nullable|string|max:255';
        }

        $messages = [
            'nama_pelapor.required' => 'Nama pelapor wajib diisi (atau pilih mode Anonim jika ingin menyembunyikan identitas).',
            'kategori_id.required' => 'Pilih salah satu kategori permasalahan.',
            'judul.required' => 'Judul permasalahan wajib diisi.',
            'deskripsi.required' => 'Deskripsi permasalahan wajib dijelaskan.',
            'alamat.required' => 'Alamat lokasi permasalahan wajib diisi.',
            'foto.*.image' => 'File bukti harus berupa gambar (JPG, PNG, WEBP).',
            'foto.*.max' => 'Ukuran setiap gambar maksimal 5 MB.',
        ];

        $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            // Generate Kode Laporan Standar SIGALING: SIG-YYYY-XXXXX
            $year = date('Y');
            $randomSuffix = strtoupper(Str::random(5));
            $kodeLaporan = "SIG-{$year}-{$randomSuffix}";

            while (Laporan::where('kode_laporan', $kodeLaporan)->exists()) {
                $randomSuffix = strtoupper(Str::random(5));
                $kodeLaporan = "SIG-{$year}-{$randomSuffix}";
            }

            // Identifikasi Wilayah (Region) jika ada kecamatan yang cocok
            $regionId = null;
            if ($request->filled('kecamatan')) {
                $region = Region::where('type', 'district')
                    ->where('name', 'like', '%' . $request->kecamatan . '%')
                    ->first();
                if ($region) {
                    $regionId = $region->id;
                }
            }

            // Eksekusi Smart Routing Engine (Tanpa Hardcode)
            $routing = $this->routingEngine->resolveRouting(
                (int) $request->kategori_id,
                $request->filled('subcategory_id') ? (int) $request->subcategory_id : null,
                $regionId
            );

            $slaHours = $routing['sla_hours'] ?: 72;
            $slaDueAt = now()->addHours($slaHours);
            $priority = $routing['priority'] ?: 'Sedang';

            // Nama & Kontak Pelapor
            $namaPelapor = $request->nama_pelapor;
            if (empty($namaPelapor) && $protectionLevel === 'anonymous') {
                $namaPelapor = 'Warga Anonim';
            } elseif (empty($namaPelapor)) {
                $namaPelapor = Auth::check() ? Auth::user()->nama : 'Masyarakat';
            }

            $kontakPelapor = $request->kontak_pelapor ?: (Auth::check() ? Auth::user()->no_hp : null);

            // 1. Simpan Data Inti Laporan (Public/General Case)
            $laporan = Laporan::create([
                'kode_laporan' => $kodeLaporan,
                'user_id' => Auth::id() ?: null,
                'nama_pelapor' => $namaPelapor,
                'kontak_pelapor' => $kontakPelapor,
                'kategori_id' => $request->kategori_id,
                'subcategory_id' => $request->subcategory_id ?: null,
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'latitude' => $request->latitude ?: -0.9471,
                'longitude' => $request->longitude ?: 100.4172,
                'alamat' => $request->alamat,
                'kecamatan' => $request->kecamatan,
                'kelurahan' => $request->kelurahan,
                'status' => 'Menunggu Verifikasi',
                'prioritas' => $priority,
                'protection_level' => $protectionLevel,
                'sla_due_at' => $slaDueAt,
                'sla_status' => 'on_track',
                'tanggal_laporan' => now(),
            ]);

            // 2. Simpan Identitas Pelapor Secara Terpisah & Terproteksi
            ReporterIdentity::create([
                'report_id' => $laporan->id,
                'user_id' => Auth::id() ?: null,
                'reporter_name' => $namaPelapor,
                'contact' => $kontakPelapor,
                'id_card_number' => $request->id_card_number,
                'protection_level' => $protectionLevel,
                'address_detail' => $request->alamat,
            ]);

            // 3. Buat Penugasan Instansi Awal Melalui Routing Engine
            if ($routing['organization']) {
                ReportAssignment::create([
                    'report_id' => $laporan->id,
                    'organization_id' => $routing['organization']->id,
                    'department_id' => $routing['department'] ? $routing['department']->id : null,
                    'supervisor_id' => null,
                    'officer_id' => null,
                    'status' => 'received',
                    'priority' => $priority,
                    'sla_due_at' => $slaDueAt,
                    'notes' => 'Disposisi cerdas otomatis oleh SIGALING Smart Routing Engine ke instansi berwenang.',
                ]);

                // Bridge ke tabel legacy 'laporan_opd' untuk kompatibilitas penuh
                if ($routing['organization']->opd_id) {
                    LaporanOpd::create([
                        'laporan_id' => $laporan->id,
                        'opd_id' => $routing['organization']->opd_id,
                        'petugas_id' => null,
                        'catatan' => 'Disposisi otomatis dari Smart Routing SIGALING',
                        'tanggal_diteruskan' => now(),
                    ]);
                }
            }

            // 4. Handle Upload Bukti Laporan
            if ($request->hasFile('foto')) {
                $uploadPath = public_path('uploads/laporan');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                foreach ($request->file('foto') as $file) {
                    if ($file->isValid()) {
                        $extension = $file->getClientOriginalExtension();
                        $filename = 'laporan_' . $laporan->id . '_' . uniqid() . '.' . $extension;
                        $file->move($uploadPath, $filename);

                        FotoLaporan::create([
                            'laporan_id' => $laporan->id,
                            'nama_file' => $filename,
                            'jenis' => 'bukti_laporan',
                        ]);
                    }
                }
            }

            // 5. Catat ke Audit Trail
            AuditTrailService::log(
                'create_report',
                'report',
                $laporan->id,
                null,
                [
                    'kode_laporan' => $kodeLaporan,
                    'status' => 'Menunggu Verifikasi',
                    'protection_level' => $protectionLevel,
                    'assigned_org' => $routing['organization'] ? $routing['organization']->name : null,
                    'sla_hours' => $slaHours,
                ]
            );

            DB::commit();

            return redirect()->route('lapor.tracking', ['kode' => $kodeLaporan])
                ->with('success', "Laporan publik Anda berhasil diajukan dengan Kode Registrasi: {$kodeLaporan}. Sistem secara otomatis meneruskannya ke instansi yang berwenang.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kendala saat mengirim laporan: ' . $e->getMessage());
        }
    }

    /**
     * Pelacakan Status Pengaduan (Tracking)
     */
    public function tracking(Request $request)
    {
        $kode = trim($request->query('kode', ''));
        $laporan = null;
        $errorMessage = '';

        if (!empty($kode)) {
            $laporan = Laporan::with([
                'kategori',
                'subcategory',
                'fotoBuktiLaporan',
                'fotoBuktiPenanganan',
                'reporterIdentity',
                'assignments.organization',
                'assignments.department',
                'assignments.supervisor',
                'assignments.officer',
                'laporanOpd.opd',
                'penanganans.petugas'
            ])->where('kode_laporan', $kode)->first();

            if (!$laporan) {
                $errorMessage = "Kode registrasi laporan \"{$kode}\" tidak ditemukan. Periksa kembali huruf dan angka pada bukti pengajuan Anda.";
            }
        }

        return view('masyarakat.tracking', compact('laporan', 'kode', 'errorMessage'));
    }

    /**
     * Halaman Riwayat Laporan Masyarakat
     */
    public function riwayat(Request $request)
    {
        $query = Laporan::with([
            'kategori',
            'subcategory',
            'fotoBuktiLaporan',
            'assignments.organization'
        ]);

        // Filter User jika sedang login dan mode "Laporan Saya"
        if ($request->query('mode') === 'saya' && Auth::check()) {
            $query->where('user_id', Auth::id());
        }

        // Filter Kategori
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter Pencarian
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('kode_laporan', 'like', "%{$s}%")
                  ->orWhere('judul', 'like', "%{$s}%")
                  ->orWhere('kecamatan', 'like', "%{$s}%")
                  ->orWhere('alamat', 'like', "%{$s}%");
            });
        }

        $laporans = $query->orderBy('created_at', 'desc')->paginate(9)->withQueryString();
        $kategoris = Kategori::orderBy('nama_kategori', 'asc')->get();

        return view('pages.riwayat', compact('laporans', 'kategoris'));
    }

    /**
     * Detail Publik Laporan
     */
    public function show($kode)
    {
        $laporan = Laporan::with([
            'kategori',
            'subcategory',
            'fotoBuktiLaporan',
            'fotoBuktiPenanganan',
            'reporterIdentity',
            'assignments.organization',
            'assignments.department',
            'assignments.supervisor',
            'assignments.officer',
            'penanganans.petugas',
            'auditLogs' => fn($q) => $q->orderBy('created_at', 'desc')
        ])->where('kode_laporan', $kode)->firstOrFail();

        return view('masyarakat.detail-laporan', compact('laporan'));
    }
}
