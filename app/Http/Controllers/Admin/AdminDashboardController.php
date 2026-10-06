<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\Penanganan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalLaporan = Laporan::count();
        $menungguVerifikasi = Laporan::where('status', 'Menunggu Verifikasi')->count();
        $terverifikasi = Laporan::where('status', 'Terverifikasi')->count();
        $dalamPenanganan = Laporan::where('status', 'Dalam Penanganan')->count();
        $selesai = Laporan::where('status', 'Selesai')->count();
        $ditolak = Laporan::where('status', 'Ditolak')->count();

        // Rata-rata waktu SLA penyelesaian
        $avgHours = DB::table('penanganan')
            ->join('laporan', 'penanganan.laporan_id', '=', 'laporan.id')
            ->where('penanganan.status', 'Selesai')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, laporan.created_at, penanganan.tanggal_penanganan)) as avg_hours')
            ->value('avg_hours');

        $avgHours = round((float)$avgHours, 1);
        $slaDisplay = ($avgHours > 0) ? ($avgHours > 24 ? round($avgHours / 24, 1) . ' Hari' : $avgHours . ' Jam') : '1.5 Hari';

        // Laporan per kategori untuk Chart
        $kategoriStats = Kategori::withCount('laporans')->orderBy('laporans_count', 'desc')->get();
        $chartKategoriLabels = $kategoriStats->pluck('nama_kategori')->toArray();
        $chartKategoriData = $kategoriStats->pluck('laporans_count')->toArray();

        // Tren 7 hari terakhir
        $trendDates = collect();
        $trendCounts = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $label = now()->subDays($i)->format('d M');
            $count = Laporan::whereDate('created_at', $date)->count();

            $trendDates->push($label);
            $trendCounts->push($count);
        }

        // Laporan butuh verifikasi
        $laporanButuhVerifikasi = Laporan::with('kategori')
            ->where('status', 'Menunggu Verifikasi')
            ->orderBy('created_at', 'asc')
            ->take(5)
            ->get();

        // Laporan terbaru
        $laporanTerbaru = Laporan::with('kategori')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.index', compact(
            'totalLaporan',
            'menungguVerifikasi',
            'terverifikasi',
            'dalamPenanganan',
            'selesai',
            'ditolak',
            'slaDisplay',
            'chartKategoriLabels',
            'chartKategoriData',
            'trendDates',
            'trendCounts',
            'laporanButuhVerifikasi',
            'laporanTerbaru'
        ));
    }
}
