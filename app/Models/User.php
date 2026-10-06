<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    public $timestamps = false;

    protected $fillable = [
        'nama',
        'email',
        'password',
        'no_hp',
        'role',
        'opd_id',
        'department_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return in_array($this->role, ['superadmin', 'admin']);
    }

    public function isAdmin(): bool
    {
        return $this->isSuperAdmin();
    }

    public function isAdminInstansi(): bool
    {
        return $this->role === 'admin_instansi';
    }

    public function isSupervisor(): bool
    {
        return $this->role === 'supervisor';
    }

    public function isPetugas(): bool
    {
        return $this->role === 'petugas';
    }

    public function isAuditor(): bool
    {
        return $this->role === 'auditor';
    }

    public function isMasyarakat(): bool
    {
        return $this->role === 'masyarakat' || empty($this->role);
    }

    public function laporans()
    {
        return $this->hasMany(Laporan::class, 'user_id');
    }

    public function penanganans()
    {
        return $this->hasMany(Penanganan::class, 'petugas_id');
    }

    public function laporanOpds()
    {
        return $this->hasMany(LaporanOpd::class, 'petugas_id');
    }

    public function opd()
    {
        return $this->belongsTo(Opd::class, 'opd_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'opd_id', 'opd_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function assignmentsAsSupervisor()
    {
        return $this->hasMany(ReportAssignment::class, 'supervisor_id');
    }

    public function assignmentsAsOfficer()
    {
        return $this->hasMany(ReportAssignment::class, 'officer_id');
    }
}
