<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Opd;
use App\Models\Organization;
use App\Models\Region;
use App\Services\AuditTrailService;
use Illuminate\Http\Request;

class InstansiController extends Controller
{
    /**
     * Daftar Instansi & Unit/Bidang
     */
    public function index(Request $request)
    {
        $query = Organization::with(['region', 'departments', 'legacyOpd']);

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

        $organizations = $query->orderBy('name')->paginate(12)->withQueryString();
        $regions = Region::whereIn('type', ['city', 'province'])->get();

        return view('admin.instansi.index', compact('organizations', 'regions'));
    }

    /**
     * Simpan Instansi Baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:organizations,code',
            'type' => 'required|string|max:50',
            'region_id' => 'nullable|exists:regions,id',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        // Buat record OPD legacy secara sinkron agar modul lama tetap berjalan
        $opd = Opd::create([
            'nama_opd' => $validated['name'],
            'deskripsi' => $validated['description'] ?? null,
            'kontak' => $validated['phone'] ?? null,
        ]);

        $org = Organization::create([
            'opd_id' => $opd->id,
            'region_id' => $validated['region_id'] ?: null,
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'is_active' => true,
        ]);

        AuditTrailService::log('create_organization', 'organization', $org->id, null, $org->toArray());

        return back()->with('success', "Instansi \"{$org->name}\" berhasil didaftarkan ke platform.");
    }

    /**
     * Update Instansi
     */
    public function update(Request $request, $id)
    {
        $org = Organization::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:organizations,code,' . $id,
            'type' => 'required|string|max:50',
            'region_id' => 'nullable|exists:regions,id',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $old = $org->toArray();

        $org->update([
            'name' => $validated['name'],
            'code' => strtoupper($validated['code']),
            'type' => $validated['type'],
            'region_id' => $validated['region_id'] ?: null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        // Update OPD legacy
        if ($org->legacyOpd) {
            $org->legacyOpd->update([
                'nama_opd' => $org->name,
                'deskripsi' => $org->description,
                'kontak' => $org->phone,
            ]);
        }

        AuditTrailService::log('update_organization', 'organization', $org->id, $old, $org->toArray());

        return back()->with('success', "Informasi instansi \"{$org->name}\" berhasil diperbarui.");
    }

    /**
     * Tambah Unit/Bidang ke Instansi
     */
    public function storeDepartment(Request $request, $organizationId)
    {
        $org = Organization::findOrFail($organizationId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $dept = Department::create([
            'organization_id' => $org->id,
            'name' => $validated['name'],
            'code' => $validated['code'] ? strtoupper($validated['code']) : null,
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        AuditTrailService::log('create_department', 'department', $dept->id, null, $dept->toArray());

        return back()->with('success', "Bidang \"{$dept->name}\" berhasil ditambahkan ke {$org->name}.");
    }
}
