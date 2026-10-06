<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tps extends Model
{
    use HasFactory;

    protected $table = 'tps';

    protected $fillable = [
        'kode_tps',
        'nama_tps',
        'kecamatan',
        'kelurahan',
        'alamat',
        'latitude',
        'longitude',
        'jenis_tps',
        'kapasitas',
        'jadwal_angkut',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    /**
     * Scope query untuk filter kecamatan
     */
    public function scopeKecamatan($query, $kecamatan)
    {
        if (!empty($kecamatan)) {
            return $query->where('kecamatan', $kecamatan);
        }
        return $query;
    }

    /**
     * Scope query untuk TPS yang beroperasi (aktif / hampir penuh / penuh)
     */
    public function scopeAktif($query)
    {
        return $query->where('status', '!=', 'Perbaikan');
    }

    /**
     * Badge CSS class untuk status operasional
     */
    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            'Aktif' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
            'Hampir Penuh' => 'bg-amber-50 text-amber-700 border-amber-200/80',
            'Penuh' => 'bg-rose-50 text-rose-700 border-rose-200/80',
            'Perbaikan' => 'bg-slate-100 text-slate-700 border-slate-200',
            default => 'bg-slate-50 text-slate-600 border-slate-200',
        };
    }

    /**
     * Warna dot indikator bulat
     */
    public function getStatusDotClassAttribute()
    {
        return match ($this->status) {
            'Aktif' => 'bg-emerald-500',
            'Hampir Penuh' => 'bg-amber-500',
            'Penuh' => 'bg-rose-500',
            'Perbaikan' => 'bg-slate-400',
            default => 'bg-slate-400',
        };
    }

    /**
     * URL navigasi langsung ke Google Maps
     */
    public function getGoogleMapsUrlAttribute()
    {
        return "https://www.google.com/maps/dir/?api=1&destination={$this->latitude},{$this->longitude}";
    }
}
