<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $laporan_id
 * @property string $nama_file
 * @property string|null $jenis
 * @property string|null $created_at
 * @property-read string $url
 * @property-read \App\Models\Laporan $laporan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FotoLaporan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FotoLaporan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FotoLaporan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FotoLaporan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FotoLaporan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FotoLaporan whereJenis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FotoLaporan whereLaporanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FotoLaporan whereNamaFile($value)
 * @mixin \Eloquent
 */
class FotoLaporan extends Model
{
    use HasFactory;

    protected $table = 'foto_laporan';

    public $timestamps = false;

    protected $fillable = [
        'laporan_id',
        'nama_file',
        'jenis',
    ];

    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'laporan_id');
    }

    public function getUrlAttribute(): string
    {
        if (str_starts_with($this->nama_file, 'http')) {
            return $this->nama_file;
        }

        // Check if file exists in uploads/
        if (file_exists(public_path('uploads/laporan/' . $this->nama_file))) {
            return asset('uploads/laporan/' . $this->nama_file);
        }

        if (file_exists(public_path('uploads/penanganan/' . $this->nama_file))) {
            return asset('uploads/penanganan/' . $this->nama_file);
        }

        if (file_exists(public_path('uploads/' . $this->nama_file))) {
            return asset('uploads/' . $this->nama_file);
        }

        return asset('storage/laporan/' . $this->nama_file);
    }
}
