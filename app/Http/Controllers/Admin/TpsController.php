<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tps;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TpsController extends Controller
{
    /**
     * 11 Kecamatan di Kota Padang
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
     * Daftar Titik TPS dengan UI gaya Nike / Puma / Apple
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
                  ->orWhere('kode_tps', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('kelurahan', 'like', "%{$search}%");
            });
        }

        if (!empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        $tpsList = $query->latest()->paginate(12)->withQueryString();

        // Metrik Eksekutif
        $totalTps = Tps::count();
        $totalAktif = Tps::where('status', 'Aktif')->count();
        $totalPenuh = Tps::whereIn('status', ['Hampir Penuh', 'Penuh'])->count();
        $total3R = Tps::where('jenis_tps', 'like', '%3R%')->count();

        return view('admin.tps.index', [
            'tpsList' => $tpsList,
            'kecamatans' => $this->kecamatans,
            'selectedKecamatan' => $selectedKecamatan,
            'search' => $search,
            'statusFilter' => $statusFilter,
            'totalTps' => $totalTps,
            'totalAktif' => $totalAktif,
            'totalPenuh' => $totalPenuh,
            'total3R' => $total3R,
        ]);
    }

    /**
     * Halaman Input TPS Baru
     */
    public function create()
    {
        return view('admin.tps.create', [
            'kecamatans' => $this->kecamatans,
        ]);
    }

    /**
     * Simpan Data TPS Baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_tps' => 'required|string|max:150',
            'kecamatan' => 'required|string|in:' . implode(',', $this->kecamatans),
            'kelurahan' => 'nullable|string|max:80',
            'alamat' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'jenis_tps' => 'required|string|max:50',
            'kapasitas' => 'nullable|string|max:80',
            'jadwal_angkut' => 'nullable|string|max:120',
            'status' => 'required|in:Aktif,Hampir Penuh,Penuh,Perbaikan',
            'keterangan' => 'nullable|string',
        ]);

        // Auto-generate kode_tps jika kosong
        if (empty($request->kode_tps)) {
            $prefixMap = [
                'Padang Barat' => 'PB',
                'Padang Timur' => 'PT',
                'Padang Utara' => 'PU',
                'Padang Selatan' => 'PS',
                'Koto Tangah' => 'KT',
                'Kuranji' => 'KR',
                'Nanggalo' => 'NG',
                'Lubuk Begalung' => 'LB',
                'Lubuk Kilangan' => 'LK',
                'Pauh' => 'PH',
                'Bungus Teluk Kabung' => 'BT',
            ];
            $prefix = $prefixMap[$validated['kecamatan']] ?? 'PDG';
            $nextNum = Tps::where('kecamatan', $validated['kecamatan'])->count() + 1;
            $validated['kode_tps'] = 'TPS-' . $prefix . '-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
        } else {
            $validated['kode_tps'] = $request->kode_tps;
        }

        Tps::create($validated);

        return redirect()->route('admin.tps.index')->with('success', 'Titik TPS baru berhasil ditambahkan.');
    }

    /**
     * Halaman Edit Data TPS
     */
    public function edit($id)
    {
        $tps = Tps::findOrFail($id);

        return view('admin.tps.edit', [
            'tps' => $tps,
            'kecamatans' => $this->kecamatans,
        ]);
    }

    /**
     * Update Data TPS
     */
    public function update(Request $request, $id)
    {
        $tps = Tps::findOrFail($id);

        $validated = $request->validate([
            'kode_tps' => 'nullable|string|max:30|unique:tps,kode_tps,' . $id,
            'nama_tps' => 'required|string|max:150',
            'kecamatan' => 'required|string|in:' . implode(',', $this->kecamatans),
            'kelurahan' => 'nullable|string|max:80',
            'alamat' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'jenis_tps' => 'required|string|max:50',
            'kapasitas' => 'nullable|string|max:80',
            'jadwal_angkut' => 'nullable|string|max:120',
            'status' => 'required|in:Aktif,Hampir Penuh,Penuh,Perbaikan',
            'keterangan' => 'nullable|string',
        ]);

        $tps->update($validated);

        return redirect()->route('admin.tps.index')->with('success', 'Data titik TPS berhasil diperbarui.');
    }

    /**
     * Hapus Data TPS
     */
    public function destroy($id)
    {
        $tps = Tps::findOrFail($id);
        $tps->delete();

        return redirect()->route('admin.tps.index')->with('success', 'Titik TPS berhasil dihapus.');
    }
}
