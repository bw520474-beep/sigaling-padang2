<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Kategori;
use App\Models\Organization;
use App\Models\Region;
use App\Models\RoutingRule;
use App\Models\Subcategory;
use App\Services\AuditTrailService;
use Illuminate\Http\Request;

class RoutingRuleController extends Controller
{
    /**
     * Daftar Aturan Smart Routing
     */
    public function index(Request $request)
    {
        $query = RoutingRule::with(['category', 'subcategory', 'organization', 'department', 'region']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('organization_id')) {
            $query->where('organization_id', $request->organization_id);
        }

        $rules = $query->orderBy('category_id')->orderBy('subcategory_id')->paginate(15)->withQueryString();

        $categories = Kategori::with('subcategories')->orderBy('nama_kategori')->get();
        $subcategories = Subcategory::where('is_active', true)->orderBy('name')->get();
        $organizations = Organization::with('departments')->where('is_active', true)->orderBy('name')->get();
        $regions = Region::where('is_active', true)->orderBy('name')->get();

        return view('admin.routing.index', compact('rules', 'categories', 'subcategories', 'organizations', 'regions'));
    }

    /**
     * Simpan Aturan Routing Baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:kategori,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'region_id' => 'nullable|exists:regions,id',
            'organization_id' => 'required|exists:organizations,id',
            'department_id' => 'nullable|exists:departments,id',
            'priority' => 'required|in:Rendah,Sedang,Tinggi,Darurat',
            'sla_hours' => 'required|integer|min:1|max:720',
            'notes' => 'nullable|string',
        ]);

        $rule = RoutingRule::create([
            'category_id' => $validated['category_id'],
            'subcategory_id' => $validated['subcategory_id'] ?: null,
            'region_id' => $validated['region_id'] ?: null,
            'organization_id' => $validated['organization_id'],
            'department_id' => $validated['department_id'] ?: null,
            'priority' => $validated['priority'],
            'sla_hours' => $validated['sla_hours'],
            'notes' => $validated['notes'] ?? null,
            'is_active' => true,
        ]);

        AuditTrailService::log('create_routing_rule', 'routing_rule', $rule->id, null, $rule->toArray());

        return back()->with('success', 'Aturan Smart Routing baru berhasil dikonfigurasikan.');
    }

    /**
     * Update Aturan Routing
     */
    public function update(Request $request, $id)
    {
        $rule = RoutingRule::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'required|exists:kategori,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'region_id' => 'nullable|exists:regions,id',
            'organization_id' => 'required|exists:organizations,id',
            'department_id' => 'nullable|exists:departments,id',
            'priority' => 'required|in:Rendah,Sedang,Tinggi,Darurat',
            'sla_hours' => 'required|integer|min:1|max:720',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $old = $rule->toArray();

        $rule->update([
            'category_id' => $validated['category_id'],
            'subcategory_id' => $validated['subcategory_id'] ?: null,
            'region_id' => $validated['region_id'] ?: null,
            'organization_id' => $validated['organization_id'],
            'department_id' => $validated['department_id'] ?: null,
            'priority' => $validated['priority'],
            'sla_hours' => $validated['sla_hours'],
            'notes' => $validated['notes'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        AuditTrailService::log('update_routing_rule', 'routing_rule', $rule->id, $old, $rule->toArray());

        return back()->with('success', 'Aturan Smart Routing berhasil diperbarui.');
    }

    /**
     * Hapus Aturan Routing
     */
    public function destroy($id)
    {
        $rule = RoutingRule::findOrFail($id);
        $old = $rule->toArray();

        $rule->delete();

        AuditTrailService::log('delete_routing_rule', 'routing_rule', $id, $old, null);

        return back()->with('success', 'Aturan Smart Routing berhasil dihapus.');
    }
}
