<?php

namespace App\Http\Controllers;

use App\Models\Tps;
use Illuminate\Http\Request;

class TpsController extends Controller
{
    /**
     * Daftar 11 Kecamatan di Kota Padang
     */
    protected array $kecamatans = [
        'Padang Barat',
        'Padang Timur',
        'Padang Utara',
        'Padang Selatan',
        'Koto Tangah',
        'Kuranji',
        'Nanggalo',
        'Lubuk Begalung',
        'Lubuk Kilangan',
        'Pauh',
        'Bungus Teluk Kabung',
    ];

    /**
     * Halaman Publik Peta & Direktori TPS Kota Padang
     */
    public function index(Request $request)
    {
        $selectedKecamatan = $request->get('kecamatan');
        $search = $request->get('q');
        $statusFilter = $request->get('status');

        $query = Tps::query();

        if (!empty($selectedKecamatan) && in_array($selectedKecamatan, $this->kecamatans)) {
            $query->where('kecamatan', $selectedKecamatan);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_tps', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('kelurahan', 'like', "%{$search}%")
                  ->orWhere('kode_tps', 'like', "%{$search}%");
            });
        }

        if (!empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        $tpsList = $query->orderBy('kecamatan')->orderBy('nama_tps')->get();

        // Hitung statistik keseluruhan
        $totalTps = Tps::count();
        $totalAktif = Tps::where('status', 'Aktif')->count();
        $total3R = Tps::where('jenis_tps', 'like', '%3R%')->count();
        $totalKecamatan = count($this->kecamatans);

        // Hitung jumlah TPS per kecamatan untuk badge filter
        $kecamatanCounts = Tps::selectRaw('kecamatan, count(*) as count')
            ->groupBy('kecamatan')
            ->pluck('count', 'kecamatan')
            ->toArray();

        return view('pages.tps', [
            'tpsList' => $tpsList,
            'kecamatans' => $this->kecamatans,
            'selectedKecamatan' => $selectedKecamatan,
            'search' => $search,
            'statusFilter' => $statusFilter,
            'totalTps' => $totalTps,
            'totalAktif' => $totalAktif,
            'total3R' => $total3R,
            'totalKecamatan' => $totalKecamatan,
            'kecamatanCounts' => $kecamatanCounts,
        ]);
    }

    /**
     * Endpoint API GeoJSON untuk Leaflet Map
     */
    public function geojson(Request $request)
    {
        $query = Tps::query();

        if ($request->filled('kecamatan')) {
            $query->where('kecamatan', $request->kecamatan);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tpsItems = $query->get();

        $features = $tpsItems->map(function ($tps) {
            return [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [(float) $tps->longitude, (float) $tps->latitude],
                ],
                'properties' => [
                    'id' => $tps->id,
                    'kode_tps' => $tps->kode_tps,
                    'nama_tps' => $tps->nama_tps,
                    'kecamatan' => $tps->kecamatan,
                    'kelurahan' => $tps->kelurahan,
                    'alamat' => $tps->alamat,
                    'jenis_tps' => $tps->jenis_tps,
                    'kapasitas' => $tps->kapasitas,
                    'jadwal_angkut' => $tps->jadwal_angkut,
                    'status' => $tps->status,
                    'badge_class' => $tps->status_badge_class,
                    'dot_class' => $tps->status_dot_class,
                    'keterangan' => $tps->keterangan,
                    'gmaps_url' => $tps->google_maps_url,
                ],
            ];
        });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }
}
