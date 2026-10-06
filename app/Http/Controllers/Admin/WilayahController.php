<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Services\AuditTrailService;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    /**
     * Daftar Wilayah Administratif
     */
    public function index(Request $request)
    {
        $query = Region::with(['parent', 'children']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%");
            });
        }

        $regions = $query->orderBy('type')->orderBy('name')->paginate(15)->withQueryString();
        $parentCandidates = Region::whereIn('type', ['country', 'province', 'city'])->get();

        return view('admin.wilayah.index', compact('regions', 'parentCandidates'));
    }

    /**
     * Simpan Wilayah Baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:regions,code',
            'type' => 'required|in:country,province,city,district,village',
            'parent_id' => 'nullable|exists:regions,id',
        ], [
            'name.required' => 'Nama wilayah wajib diisi.',
            'code.required' => 'Kode wilayah wajib diisi.',
            'code.unique' => 'Kode wilayah sudah digunakan.',
        ]);

        $region = Region::create([
            'name' => $validated['name'],
            'code' => strtoupper($validated['code']),
            'type' => $validated['type'],
            'parent_id' => $validated['parent_id'] ?: null,
            'is_active' => true,
        ]);

        AuditTrailService::log('create_region', 'region', $region->id, null, $region->toArray());

        return back()->with('success', "Wilayah \"{$region->name}\" berhasil ditambahkan.");
    }

    /**
     * Update Wilayah
     */
    public function update(Request $request, $id)
    {
        $region = Region::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:regions,code,' . $id,
            'type' => 'required|in:country,province,city,district,village',
            'parent_id' => 'nullable|exists:regions,id',
            'is_active' => 'nullable|boolean',
        ]);

        $old = $region->toArray();

        $region->update([
            'name' => $validated['name'],
            'code' => strtoupper($validated['code']),
            'type' => $validated['type'],
            'parent_id' => $validated['parent_id'] ?: null,
            'is_active' => $request->has('is_active'),
        ]);

        AuditTrailService::log('update_region', 'region', $region->id, $old, $region->toArray());

        return back()->with('success', "Wilayah \"{$region->name}\" berhasil diperbarui.");
    }

    /**
     * Hapus Wilayah
     */
    public function destroy($id)
    {
        $region = Region::findOrFail($id);
        $name = $region->name;
        $old = $region->toArray();

        $region->delete();

        AuditTrailService::log('delete_region', 'region', $id, $old, null);

        return back()->with('success', "Wilayah \"{$name}\" berhasil dihapus.");
    }
}
