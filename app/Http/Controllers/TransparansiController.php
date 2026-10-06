<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\Organization;
use App\Models\Region;
use Illuminate\Http\Request;

class TransparansiController extends Controller
{
    /**
     * Halaman Transparansi Publik Terbuka & Civic Monitor SIGALING
     */
    public function index()
    {
        $totalLaporan = Laporan::count();
        $totalSelesai = Laporan::where('status', 'Selesai')->count();
        $totalProses = Laporan::whereIn('status', ['Terverifikasi', 'Dalam Penanganan'])->count();
        $totalOverdue = Laporan::where('status', '!=', 'Selesai')
            ->whereNotNull('sla_due_at')
            ->where('sla_due_at', '<', now())
            ->count();

        $slaRate = $totalLaporan > 0 ? round((($totalLaporan - $totalOverdue) / $totalLaporan) * 100, 1) : 100;

        // Distribusi per Kategori
        $kategoriStats = Kategori::withCount('laporans')
            ->orderBy('laporans_count', 'desc')
            ->get();

        // Distribusi per Instansi
        $orgStats = Organization::withCount('assignments')
            ->where('is_active', true)
            ->orderBy('assignments_count', 'desc')
            ->limit(6)
            ->get();

        // Data titik agregat publik (Anonim & Aman)
        $publicPoints = Laporan::with(['kategori', 'subcategory'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->limit(100)
            ->get()
            ->map(function ($lap) {
                return [
                    'id' => $lap->id,
                    'kode' => $lap->kode_laporan,
                    'judul' => $lap->judul,
                    'kategori' => $lap->kategori->nama_kategori ?? 'Umum',
                    'subkategori' => $lap->subcategory->name ?? '',
                    'status' => $lap->status,
                    'lat' => (float) $lap->latitude,
                    'lng' => (float) $lap->longitude,
                    'kecamatan' => $lap->kecamatan ?? 'Kota Padang',
                    'tanggal' => $lap->created_at ? $lap->created_at->translatedFormat('d M Y') : '-',
                ];
            });

        return view('pages.transparansi', compact(
            'totalLaporan',
            'totalSelesai',
            'totalProses',
            'totalOverdue',
            'slaRate',
            'kategoriStats',
            'orgStats',
            'publicPoints'
        ));
    }
}
