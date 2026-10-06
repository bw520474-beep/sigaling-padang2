<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $laporan_id
 * @property int $opd_id
 * @property int|null $petugas_id
 * @property string|null $catatan
 * @property \Illuminate\Support\Carbon|null $tanggal_diteruskan
 * @property-read \App\Models\Laporan $laporan
 * @property-read \App\Models\Opd $opd
 * @property-read \App\Models\User|null $petugas
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanOpd newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanOpd newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanOpd query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanOpd whereCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanOpd whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanOpd whereLaporanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanOpd whereOpdId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanOpd wherePetugasId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanOpd whereTanggalDiteruskan($value)
 * @mixin \Eloquent
 */
class LaporanOpd extends Model
{
    use HasFactory;

    protected $table = 'laporan_opd';

    public $timestamps = false;

    protected $fillable = [
        'laporan_id',
        'opd_id',
        'petugas_id',
        'catatan',
        'tanggal_diteruskan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_diteruskan' => 'datetime',
        ];
    }

    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'laporan_id');
    }

    public function opd()
    {
        return $this->belongsTo(Opd::class, 'opd_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
