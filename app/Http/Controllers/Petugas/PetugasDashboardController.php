<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\LaporanOpd;
use App\Models\Penanganan;
use Illuminate\Support\Facades\Auth;

class PetugasDashboardController extends Controller
{
    public function index()
    {
        $petugasId = Auth::id();

        // 1. Statistik Tugas Petugas
        $totalTugas = Penanganan::where('petugas_id', $petugasId)->count();
        $menunggu = Penanganan::where('petugas_id', $petugasId)->where('status', 'Menunggu')->count();
        $proses = Penanganan::where('petugas_id', $petugasId)->where('status', 'Dalam Proses')->count();
        $selesai = Penanganan::where('petugas_id', $petugasId)->where('status', 'Selesai')->count();

        // 2. Tugas Aktif yang Sedang Dikerjakan
        $tugasAktif = Penanganan::with(['laporan.kategori', 'laporan.fotoBuktiLaporan'])
            ->where('petugas_id', $petugasId)
            ->whereIn('status', ['Menunggu', 'Dalam Proses'])
            ->orderBy('tanggal_penanganan', 'desc')
            ->take(5)
            ->get();

        // 3. Tugas Baru Tersedia (Laporan di Disposisi OPD yang belum di-assign petugas dan SESUAI OPD PETUGAS)
        $petugasUser = Auth::user();
        $tugasTersedia = LaporanOpd::with(['laporan.kategori', 'opd'])
            ->whereNull('petugas_id')
            ->when($petugasUser->opd_id, function ($q) use ($petugasUser) {
                $q->where('opd_id', $petugasUser->opd_id);
            })
            ->whereHas('laporan', function ($q) {
                $q->whereIn('status', ['Terverifikasi', 'Dalam Penanganan']);
            })
            ->orderBy('tanggal_diteruskan', 'desc')
            ->take(5)
            ->get();

        return view('petugas.index', compact(
            'totalTugas',
            'menunggu',
            'proses',
            'selesai',
            'tugasAktif',
            'tugasTersedia'
        ));
    }
}
