<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $kode_laporan
 * @property int|null $user_id
 * @property string $nama_pelapor
 * @property string|null $kontak_pelapor
 * @property int $kategori_id
 * @property string $judul
 * @property string $deskripsi
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $alamat
 * @property string|null $kecamatan
 * @property string|null $kelurahan
 * @property string|null $status
 * @property string|null $prioritas
 * @property \Illuminate\Support\Carbon|null $tanggal_laporan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LaporanOpd> $allLaporanOpd
 * @property-read int|null $all_laporan_opd_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FotoLaporan> $fotoBuktiLaporan
 * @property-read int|null $foto_bukti_laporan_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FotoLaporan> $fotoBuktiPenanganan
 * @property-read int|null $foto_bukti_penanganan_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FotoLaporan> $fotoLaporan
 * @property-read int|null $foto_laporan_count
 * @property-read int $status_step
 * @property-read \App\Models\Kategori $kategori
 * @property-read \App\Models\LaporanOpd|null $laporanOpd
 * @property-read \App\Models\Penanganan|null $latestPenanganan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Penanganan> $penanganans
 * @property-read int|null $penanganans_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereJudul($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereKategoriId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereKecamatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereKelurahan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereKodeLaporan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereKontakPelapor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereNamaPelapor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan wherePrioritas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereTanggalLaporan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Laporan whereUserId($value)
 * @mixin \Eloquent
 */
class Laporan extends Model
{
    use HasFactory;

    protected $table = 'laporan';

    public $timestamps = false;

    protected $fillable = [
        'kode_laporan',
        'user_id',
        'nama_pelapor',
        'kontak_pelapor',
        'kategori_id',
        'subcategory_id',
        'judul',
        'deskripsi',
        'latitude',
        'longitude',
        'alamat',
        'kecamatan',
        'kelurahan',
        'status',
        'prioritas',
        'protection_level',
        'sla_due_at',
        'sla_status',
        'tanggal_laporan',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'sla_due_at' => 'datetime',
            'tanggal_laporan' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id');
    }

    public function reporterIdentity()
    {
        return $this->hasOne(ReporterIdentity::class, 'report_id');
    }

    public function assignments()
    {
        return $this->hasMany(ReportAssignment::class, 'report_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'entity_id')->where('entity_type', 'report');
    }

    public function fotoLaporan()
    {
        return $this->hasMany(FotoLaporan::class, 'laporan_id');
    }

    public function fotoBuktiLaporan()
    {
        return $this->hasMany(FotoLaporan::class, 'laporan_id')->where('jenis', 'bukti_laporan');
    }

    public function fotoBuktiPenanganan()
    {
        return $this->hasMany(FotoLaporan::class, 'laporan_id')->where('jenis', 'bukti_penanganan');
    }

    public function laporanOpd()
    {
        return $this->hasOne(LaporanOpd::class, 'laporan_id')->latestOfMany('tanggal_diteruskan');
    }

    public function allLaporanOpd()
    {
        return $this->hasMany(LaporanOpd::class, 'laporan_id');
    }

    public function penanganans()
    {
        return $this->hasMany(Penanganan::class, 'laporan_id')->orderBy('tanggal_penanganan', 'asc');
    }

    public function latestPenanganan()
    {
        return $this->hasOne(Penanganan::class, 'laporan_id')->latestOfMany('tanggal_penanganan');
    }

    public function getStatusStepAttribute(): int
    {
        return match ($this->status) {
            'Menunggu Verifikasi' => 1,
            'Terverifikasi' => 2,
            'Dalam Penanganan' => 3,
            'Selesai' => 4,
            'Ditolak' => -1,
            default => 0,
        };
    }

    /**
     * Nama pelapor tersaring berdasarkan proteksi identitas
     */
    public function getMaskedReporterNameAttribute(): string
    {
        $level = $this->protection_level ?: ($this->reporterIdentity ? $this->reporterIdentity->protection_level : 'public');

        if ($level === 'anonymous') {
            return 'Warga Anonim (Terlindungi)';
        }

        if ($level === 'confidential') {
            $user = auth()->user();
            if ($user && ($user->isSuperAdmin() || $user->isSupervisor())) {
                return $this->nama_pelapor . ' [Rahasia]';
            }
            return 'Warga Terverifikasi (Identitas Rahasia)';
        }

        return $this->nama_pelapor ?: 'Masyarakat';
    }

    /**
     * Kontak tersaring berdasarkan proteksi identitas
     */
    public function getMaskedContactAttribute(): string
    {
        $level = $this->protection_level ?: ($this->reporterIdentity ? $this->reporterIdentity->protection_level : 'public');

        if ($level === 'anonymous') {
            return 'Dirahasiakan (Anonim)';
        }

        if ($level === 'confidential') {
            $user = auth()->user();
            if ($user && ($user->isSuperAdmin() || $user->isSupervisor())) {
                return $this->kontak_pelapor ?: '-';
            }
            return 'Dirahasiakan';
        }

        return $this->kontak_pelapor ?: '-';
    }

    /**
     * Cek status kepatuhan SLA secara real-time
     */
    public function getEffectiveSlaStatusAttribute(): string
    {
        if ($this->status === 'Selesai') {
            return 'resolved';
        }

        if (!$this->sla_due_at) {
            return 'on_track';
        }

        $now = now();
        if ($now->greaterThan($this->sla_due_at)) {
            return 'overdue';
        }

        $hoursRemaining = $now->diffInHours($this->sla_due_at, false);
        if ($hoursRemaining <= 12) {
            return 'warning';
        }

        return 'on_track';
    }
}
