<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SlaRule;
use App\Services\AuditTrailService;
use Illuminate\Http\Request;

class SlaRuleController extends Controller
{
    /**
     * Tampilan Konfigurasi SLA
     */
    public function index()
    {
        $slaRules = SlaRule::orderBy('response_time_hours')->get();

        return view('admin.sla.index', compact('slaRules'));
    }

    /**
     * Update Target SLA
     */
    public function update(Request $request, $id)
    {
        $rule = SlaRule::findOrFail($id);

        $validated = $request->validate([
            'response_time_hours' => 'required|integer|min:1|max:168',
            'resolution_time_hours' => 'required|integer|min:1|max:720',
            'warning_threshold_percent' => 'required|integer|min:50|max:95',
        ]);

        $old = $rule->toArray();

        $rule->update($validated);

        AuditTrailService::log('update_sla_rule', 'sla_rule', $rule->id, $old, $rule->toArray());

        return back()->with('success', "Aturan SLA prioritas \"{$rule->priority}\" berhasil diperbarui.");
    }
}
