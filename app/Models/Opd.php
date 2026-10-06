<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $nama_opd
 * @property string|null $deskripsi
 * @property string|null $kontak
 * @property string|null $created_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LaporanOpd> $laporanOpds
 * @property-read int|null $laporan_opds_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opd newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opd newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opd query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opd whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opd whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opd whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opd whereKontak($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Opd whereNamaOpd($value)
 * @mixin \Eloquent
 */
class Opd extends Model
{
    use HasFactory;

    protected $table = 'opd';

    public $timestamps = false;

    protected $fillable = [
        'nama_opd',
        'deskripsi',
        'kontak',
    ];

    public function laporanOpds()
    {
        return $this->hasMany(LaporanOpd::class, 'opd_id');
    }
}
