<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $table = 'organizations';

    protected $fillable = [
        'opd_id',
        'region_id',
        'code',
        'name',
        'type',
        'description',
        'phone',
        'email',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function legacyOpd()
    {
        return $this->belongsTo(Opd::class, 'opd_id');
    }

    public function departments()
    {
        return $this->hasMany(Department::class, 'organization_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'opd_id', 'opd_id');
    }

    public function assignments()
    {
        return $this->hasMany(ReportAssignment::class, 'organization_id');
    }

    public function routingRules()
    {
        return $this->hasMany(RoutingRule::class, 'organization_id');
    }
}
