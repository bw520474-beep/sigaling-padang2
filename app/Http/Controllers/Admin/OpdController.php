<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Opd;
use Illuminate\Http\Request;

class OpdController extends Controller
{
    public function index()
    {
        $opds = Opd::withCount('laporanOpds')->orderBy('nama_opd', 'asc')->get();
        return view('admin.opd.index', compact('opds'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_opd' => 'required|string|max:255|unique:opd,nama_opd',
            'deskripsi' => 'nullable|string',
            'kontak' => 'nullable|string|max:255',
        ], [
            'nama_opd.required' => 'Nama OPD wajib diisi.',
            'nama_opd.unique' => 'Nama OPD tersebut sudah terdaftar.',
        ]);

        Opd::create($validated);

        return back()->with('success', 'Instansi OPD baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $opd = Opd::findOrFail($id);

        $validated = $request->validate([
            'nama_opd' => "required|string|max:255|unique:opd,nama_opd,{$id}",
            'deskripsi' => 'nullable|string',
            'kontak' => 'nullable|string|max:255',
        ], [
            'nama_opd.required' => 'Nama OPD wajib diisi.',
            'nama_opd.unique' => 'Nama OPD tersebut sudah terdaftar.',
        ]);

        $opd->update($validated);

        return back()->with('success', "Data OPD \"{$opd->nama_opd}\" berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $opd = Opd::findOrFail($id);

        if ($opd->laporanOpds()->count() > 0) {
            return back()->with('error', "OPD \"{$opd->nama_opd}\" tidak dapat dihapus karena memiliki riwayat disposisi laporan.");
        }

        $opd->delete();

        return back()->with('success', 'Data OPD berhasil dihapus.');
    }
}
