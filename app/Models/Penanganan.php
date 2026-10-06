<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $laporan_id
 * @property int $petugas_id
 * @property string|null $status
 * @property string|null $keterangan
 * @property string|null $foto
 * @property \Illuminate\Support\Carbon|null $tanggal_penanganan
 * @property-read string|null $foto_url
 * @property-read \App\Models\Laporan $laporan
 * @property-read \App\Models\User $petugas
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penanganan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penanganan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penanganan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penanganan whereFoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penanganan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penanganan whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penanganan whereLaporanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penanganan wherePetugasId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penanganan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Penanganan whereTanggalPenanganan($value)
 * @mixin \Eloquent
 */
class Penanganan extends Model
{
    use HasFactory;

    protected $table = 'penanganan';

    public $timestamps = false;

    protected $fillable = [
        'laporan_id',
        'petugas_id',
        'status',
        'keterangan',
        'foto',
        'tanggal_penanganan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_penanganan' => 'datetime',
        ];
    }

    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'laporan_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function getFotoUrlAttribute(): ?string
    {
        if (empty($this->foto)) {
            return null;
        }

        if (str_starts_with($this->foto, 'http')) {
            return $this->foto;
        }

        if (file_exists(public_path('uploads/penanganan/' . $this->foto))) {
            return asset('uploads/penanganan/' . $this->foto);
        }

        if (file_exists(public_path('uploads/' . $this->foto))) {
            return asset('uploads/' . $this->foto);
        }

        return asset('storage/penanganan/' . $this->foto);
    }
}
