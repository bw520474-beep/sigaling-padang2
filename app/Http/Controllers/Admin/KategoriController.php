<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::withCount('laporans')->orderBy('nama_kategori', 'asc')->get();
        return view('admin.kategori.index', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori,nama_kategori',
            'ikon' => 'nullable|string|max:50',
            'deskripsi' => 'nullable|string',
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique' => 'Kategori tersebut sudah terdaftar.',
        ]);

        Kategori::create([
            'nama_kategori' => $validated['nama_kategori'],
            'ikon' => $validated['ikon'] ?: 'tag',
            'deskripsi' => $validated['deskripsi'] ?? null,
        ]);

        return back()->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        $validated = $request->validate([
            'nama_kategori' => "required|string|max:100|unique:kategori,nama_kategori,{$id}",
            'ikon' => 'nullable|string|max:50',
            'deskripsi' => 'nullable|string',
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique' => 'Kategori tersebut sudah terdaftar.',
        ]);

        $kategori->update([
            'nama_kategori' => $validated['nama_kategori'],
            'ikon' => $validated['ikon'] ?: 'tag',
            'deskripsi' => $validated['deskripsi'] ?? null,
        ]);

        return back()->with('success', "Kategori \"{$kategori->nama_kategori}\" berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);

        if ($kategori->laporans()->count() > 0) {
            return back()->with('error', "Kategori \"{$kategori->nama_kategori}\" tidak dapat dihapus karena masih digunakan pada {$kategori->laporans()->count()} laporan.");
        }

        $kategori->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
