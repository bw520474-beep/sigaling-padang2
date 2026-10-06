<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\Opd;

class StatistikController extends Controller
{
    public function index()
    {
        // 1. Rekap Per Status
        $statusCounts = Laporan::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // 2. Rekap Per Kecamatan
        $kecamatanStats = Laporan::selectRaw('kecamatan, count(*) as total')
            ->whereNotNull('kecamatan')
            ->where('kecamatan', '!=', '')
            ->groupBy('kecamatan')
            ->orderBy('total', 'desc')
            ->get();

        $chartKecamatanLabels = $kecamatanStats->pluck('kecamatan')->toArray();
        $chartKecamatanData = $kecamatanStats->pluck('total')->toArray();

        // 3. Rekap Per Kategori
        $kategoriStats = Kategori::withCount('laporans')
            ->orderBy('laporans_count', 'desc')
            ->get();

        $chartKategoriLabels = $kategoriStats->pluck('nama_kategori')->toArray();
        $chartKategoriData = $kategoriStats->pluck('laporans_count')->toArray();

        // 4. Rekap Disposisi OPD
        $opdStats = Opd::withCount('laporanOpds')
            ->orderBy('laporan_opds_count', 'desc')
            ->get();

        // 5. Tren Waktu Multi-Periode (Harian, Mingguan, Bulanan, Tahunan)
        // A. Harian (14 hari terakhir)
        $harianLabels = [];
        $harianData = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $harianLabels[] = $date->translatedFormat('d M');
            $harianData[] = Laporan::whereDate('created_at', $dateStr)->count();
        }

        // B. Mingguan (8 minggu terakhir)
        $mingguanLabels = [];
        $mingguanData = [];
        for ($i = 7; $i >= 0; $i--) {
            $start = now()->subWeeks($i)->startOfWeek();
            $end = (clone $start)->endOfWeek();
            $mingguanLabels[] = 'Mg ' . $start->format('d/m') . ' - ' . $end->format('d/m');
            $mingguanData[] = Laporan::whereBetween('created_at', [$start->toDateTimeString(), $end->toDateTimeString()])->count();
        }

        // C. Bulanan (12 bulan terakhir)
        $bulananLabels = [];
        $bulananData = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $bulananLabels[] = $month->translatedFormat('M Y');
            $bulananData[] = Laporan::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        }

        // D. Tahunan (5 tahun terakhir)
        $tahunanLabels = [];
        $tahunanData = [];
        $curYear = (int) now()->format('Y');
        for ($i = 4; $i >= 0; $i--) {
            $yr = $curYear - $i;
            $tahunanLabels[] = (string) $yr;
            $tahunanData[] = Laporan::whereYear('created_at', $yr)->count();
        }

        $timeSeriesData = [
            'harian' => [
                'labels' => $harianLabels,
                'data' => $harianData,
            ],
            'mingguan' => [
                'labels' => $mingguanLabels,
                'data' => $mingguanData,
            ],
            'bulanan' => [
                'labels' => $bulananLabels,
                'data' => $bulananData,
            ],
            'tahunan' => [
                'labels' => $tahunanLabels,
                'data' => $tahunanData,
            ],
        ];

        return view('admin.statistik', compact(
            'statusCounts',
            'kecamatanStats',
            'chartKecamatanLabels',
            'chartKecamatanData',
            'kategoriStats',
            'chartKategoriLabels',
            'chartKategoriData',
            'opdStats',
            'timeSeriesData'
        ));
    }
}
