<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;

    protected $table = 'subcategories';

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'icon',
        'default_priority',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Kategori::class, 'category_id');
    }

    public function routingRules()
    {
        return $this->hasMany(RoutingRule::class, 'subcategory_id');
    }

    public function laporans()
    {
        return $this->hasMany(Laporan::class, 'subcategory_id');
    }
}
