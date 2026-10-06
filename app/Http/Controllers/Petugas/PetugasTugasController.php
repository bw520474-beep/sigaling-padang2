<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\FotoLaporan;
use App\Models\Laporan;
use App\Models\LaporanOpd;
use App\Models\Penanganan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PetugasTugasController extends Controller
{
    /**
     * Daftar Seluruh Tugas Penanganan Saya & Tugas Tersedia
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $petugasId = $user->id;
        $opdId = $user->opd_id;

        // 1. Tugas Saya
        $query = Penanganan::with(['laporan.kategori', 'laporan.fotoBuktiLaporan'])
            ->where('petugas_id', $petugasId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tugasSaya = $query->orderBy('tanggal_penanganan', 'desc')->paginate(10)->withQueryString();

        // 2. Tugas Tersedia di Antrean OPD (Khusus OPD yang sesuai dengan Petugas)
        $tugasTersediaQuery = LaporanOpd::with(['laporan.kategori', 'opd'])
            ->whereNull('petugas_id')
            ->whereHas('laporan', function ($q) {
                $q->whereIn('status', ['Terverifikasi', 'Dalam Penanganan']);
            });

        if ($opdId) {
            $tugasTersediaQuery->where('opd_id', $opdId);
        }

        $tugasTersedia = $tugasTersediaQuery->orderBy('tanggal_diteruskan', 'desc')->get();

        return view('petugas.tugas.index', compact('tugasSaya', 'tugasTersedia'));
    }

    /**
     * Detail Tugas Lapangan
     */
    public function show($id)
    {
        $petugasId = Auth::id();

        $penanganan = Penanganan::with([
            'laporan.kategori',
            'laporan.fotoBuktiLaporan',
            'laporan.fotoBuktiPenanganan',
            'laporan.laporanOpd.opd'
        ])
        ->where('petugas_id', $petugasId)
        ->findOrFail($id);

        return view('petugas.tugas.detail', compact('penanganan'));
    }

    /**
     * Ambil Tugas Baru dari Antrean Disposisi OPD
     */
    public function ambilTugas(Request $request, $laporanOpdId)
    {
        $user = Auth::user();
        $petugasId = $user->id;
        $laporanOpd = LaporanOpd::with('opd')->findOrFail($laporanOpdId);

        // Validasi: Hanya petugas dari OPD yang sama yang berhak mengambil tugas
        if ($user->opd_id && $laporanOpd->opd_id != $user->opd_id) {
            $targetOpd = $laporanOpd->opd ? $laporanOpd->opd->nama_opd : 'OPD lain';
            $myOpd = $user->opd ? $user->opd->nama_opd : 'OPD Anda';
            return back()->with('error', "Akses Ditolak: Anda bertugas di {$myOpd}, tidak dapat mengambil disposisi tugas untuk {$targetOpd}.");
        }

        // Cek jika tugas sudah diambil orang lain
        if ($laporanOpd->petugas_id && $laporanOpd->petugas_id != $petugasId) {
            return back()->with('error', "Tugas penanganan ini telah diambil oleh petugas lapangan lain.");
        }

        DB::beginTransaction();

        try {
            // Assign petugas ke disposisi
            $laporanOpd->petugas_id = $petugasId;
            $laporanOpd->save();

            // Buat record penanganan awal
            $penanganan = Penanganan::create([
                'laporan_id' => $laporanOpd->laporan_id,
                'petugas_id' => $petugasId,
                'status' => 'Dalam Proses',
                'keterangan' => 'Tugas telah diambil oleh petugas lapangan dan sedang dalam proses penanganan.',
                'tanggal_penanganan' => now(),
            ]);

            // Update status laporan menjadi Dalam Penanganan
            $laporan = Laporan::find($laporanOpd->laporan_id);
            if ($laporan) {
                $laporan->status = 'Dalam Penanganan';
                $laporan->save();
            }

            DB::commit();

            return redirect()->route('petugas.tugas.detail', $penanganan->id)
                ->with('success', "Tugas penanganan laporan {$laporan->kode_laporan} berhasil Anda ambil.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengambil tugas: ' . $e->getMessage());
        }
    }

    /**
     * Update Status & Catatan Penanganan Lapangan
     */
    public function updateProgress(Request $request, $id)
    {
        $petugasId = Auth::id();
        $penanganan = Penanganan::where('petugas_id', $petugasId)->findOrFail($id);
        $laporan = $penanganan->laporan;

        $validated = $request->validate([
            'status' => 'required|in:Menunggu,Dalam Proses,Selesai',
            'keterangan' => 'required|string',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'status.required' => 'Pilih status penanganan saat ini.',
            'keterangan.required' => 'Berikan catatan atau keterangan progres penanganan.',
            'foto.image' => 'File bukti penanganan harus berupa gambar (JPG, PNG, WEBP).',
        ]);

        DB::beginTransaction();

        try {
            $penanganan->status = $validated['status'];
            $penanganan->keterangan = $validated['keterangan'];
            $penanganan->tanggal_penanganan = now();

            // Upload foto bukti penanganan selesai
            if ($request->hasFile('foto')) {
                $uploadPath = public_path('uploads/penanganan');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                $file = $request->file('foto');
                $filename = 'penanganan_' . $laporan->id . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($uploadPath, $filename);

                $penanganan->foto = $filename;

                // Simpan juga ke relasi foto_laporan jenis bukti_penanganan
                FotoLaporan::create([
                    'laporan_id' => $laporan->id,
                    'nama_file' => $filename,
                    'jenis' => 'bukti_penanganan',
                ]);
            }

            $penanganan->save();

            // Sinkronkan status Laporan utama
            if ($validated['status'] === 'Selesai') {
                $laporan->status = 'Selesai';
                $laporan->save();
            } elseif ($validated['status'] === 'Dalam Proses') {
                $laporan->status = 'Dalam Penanganan';
                $laporan->save();
            }

            DB::commit();

            return back()->with('success', 'Progres penanganan berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui progres: ' . $e->getMessage());
        }
    }
}
