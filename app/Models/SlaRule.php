<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlaRule extends Model
{
    use HasFactory;

    protected $table = 'sla_rules';

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'priority',
        'response_time_hours',
        'resolution_time_hours',
        'warning_threshold_percent',
    ];

    public function category()
    {
        return $this->belongsTo(Kategori::class, 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id');
    }
}
