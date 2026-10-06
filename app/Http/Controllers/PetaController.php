<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Laporan;
use Illuminate\Http\Request;

class PetaController extends Controller
{
    /**
     * Halaman Peta Sebaran Laporan Lingkungan Hidup Kota Padang
     */
    public function index(Request $request)
    {
        $kategoris = Kategori::orderBy('nama_kategori', 'asc')->get();

        $query = Laporan::with(['kategori', 'fotoBuktiLaporan'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', 0);

        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $laporans = $query->orderBy('created_at', 'desc')->get();

        return view('pages.peta', compact('kategoris', 'laporans'));
    }

    /**
     * Endpoint Data JSON untuk Marker Leaflet.js
     */
    public function geojson(Request $request)
    {
        $query = Laporan::with(['kategori', 'fotoBuktiLaporan'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', 0);

        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $laporans = $query->get()->map(function ($item) {
            $foto = $item->fotoBuktiLaporan->first();
            return [
                'id' => $item->id,
                'kode_laporan' => $item->kode_laporan,
                'judul' => $item->judul,
                'deskripsi' => $item->deskripsi,
                'kategori' => $item->kategori ? $item->kategori->nama_kategori : 'Umum',
                'ikon' => $item->kategori ? $item->kategori->ikon : 'tag',
                'status' => $item->status,
                'prioritas' => $item->prioritas,
                'latitude' => (float)$item->latitude,
                'longitude' => (float)$item->longitude,
                'alamat' => $item->alamat,
                'kecamatan' => $item->kecamatan,
                'foto' => $foto ? $foto->url : null,
                'tanggal' => $item->created_at ? $item->created_at->format('d M Y') : '-',
                'url' => route('lapor.detail', ['kode' => $item->kode_laporan]),
            ];
        });

        return response()->json([
            'status' => 'success',
            'count' => $laporans->count(),
            'data' => $laporans,
        ]);
    }
}
