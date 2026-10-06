<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoutingRule extends Model
{
    use HasFactory;

    protected $table = 'routing_rules';

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'region_id',
        'organization_id',
        'department_id',
        'priority',
        'sla_hours',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sla_hours' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Kategori::class, 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
