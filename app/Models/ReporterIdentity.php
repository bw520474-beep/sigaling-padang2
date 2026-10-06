<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReporterIdentity extends Model
{
    use HasFactory;

    protected $table = 'reporter_identities';

    protected $fillable = [
        'report_id',
        'user_id',
        'reporter_name',
        'contact',
        'id_card_number',
        'protection_level',
        'address_detail',
    ];

    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'report_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Tampilkan nama berdasarkan level proteksi dan peran yang sedang melihat
     */
    public function getDisplayNameForUser(?User $viewer): string
    {
        if ($this->protection_level === 'anonymous') {
            return 'Warga Anonim (Identitas Terlindungi)';
        }

        if ($this->protection_level === 'confidential') {
            if ($viewer && ($viewer->isSuperAdmin() || $viewer->isSupervisor())) {
                return $this->reporter_name . ' [Rahasia]';
            }
            return 'Warga Terverifikasi (Identitas Rahasia)';
        }

        return $this->reporter_name;
    }

    /**
     * Tampilkan kontak berdasarkan level proteksi dan peran yang sedang melihat
     */
    public function getDisplayContactForUser(?User $viewer): string
    {
        if ($this->protection_level === 'anonymous') {
            return 'Tidak Dipublikasikan (Anonim)';
        }

        if ($this->protection_level === 'confidential') {
            if ($viewer && ($viewer->isSuperAdmin() || $viewer->isSupervisor())) {
                return $this->contact ?: '-';
            }
            return 'Dirahasiakan';
        }

        return $this->contact ?: '-';
    }
}
