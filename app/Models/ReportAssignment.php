<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportAssignment extends Model
{
    use HasFactory;

    protected $table = 'report_assignments';

    protected $fillable = [
        'report_id',
        'organization_id',
        'department_id',
        'supervisor_id',
        'officer_id',
        'status',
        'priority',
        'sla_due_at',
        'notes',
        'completion_notes',
        'completion_evidence',
        'completed_at',
    ];

    protected $casts = [
        'sla_due_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'report_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function officer()
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    /**
     * Hitung status SLA
     */
    public function getSlaStatusAttribute(): string
    {
        if ($this->status === 'resolved') {
            return 'resolved';
        }

        if (!$this->sla_due_at) {
            return 'on_track';
        }

        $now = now();
        if ($now->greaterThan($this->sla_due_at)) {
            return 'overdue';
        }

        $totalHours = 72; // default
        $remainingHours = $now->diffInHours($this->sla_due_at, false);

        if ($remainingHours <= 12) {
            return 'warning';
        }

        return 'on_track';
    }
}
