<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminLaporanController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\InstansiController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\OpdController;
use App\Http\Controllers\Admin\PenggunaController;
use App\Http\Controllers\Admin\PetugasController;
use App\Http\Controllers\Admin\RoutingRuleController;
use App\Http\Controllers\Admin\SlaRuleController;
use App\Http\Controllers\Admin\StatistikController;
use App\Http\Controllers\Admin\TpsController as AdminTpsController;
use App\Http\Controllers\Admin\WilayahController;
use App\Http\Controllers\Auditor\AuditorDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Instansi\InstansiDashboardController;
use App\Http\Controllers\Instansi\InstansiLaporanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PetaController;
use App\Http\Controllers\Petugas\PetugasDashboardController;
use App\Http\Controllers\Petugas\PetugasTugasController;
use App\Http\Controllers\TpsController;
use App\Http\Controllers\TransparansiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SIGALING PADANG (Global Civic Platform)
|--------------------------------------------------------------------------
*/

// ===== 1. HALAMAN PUBLIK & MASYARAKAT =====
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/alur', [HomeController::class, 'alur'])->name('alur');
Route::get('/edukasi', [HomeController::class, 'edukasi'])->name('edukasi');
Route::get('/jadwal-sampah', [HomeController::class, 'jadwalSampah'])->name('jadwal-sampah');
Route::get('/faq', [HomeController::class, 'faq'])->name('faq');

// Transparansi Publik & Civic Monitor
Route::get('/transparansi', [TransparansiController::class, 'index'])->name('transparansi');

// Peta Aduan & Titik Lokasi TPS
Route::get('/peta', [PetaController::class, 'index'])->name('peta');
Route::get('/api/peta-geojson', [PetaController::class, 'geojson'])->name('peta.geojson');
Route::get('/tps', [TpsController::class, 'index'])->name('tps.index');
Route::get('/api/tps-geojson', [TpsController::class, 'geojson'])->name('tps.geojson');

// Pelaporan & Smart Classifier
Route::get('/buat-laporan', [LaporanController::class, 'create'])->name('lapor');
Route::post('/buat-laporan', [LaporanController::class, 'store'])->name('lapor.store');
Route::post('/api/suggest-routing', [LaporanController::class, 'suggest'])->name('api.suggest-routing');
Route::get('/tracking', [LaporanController::class, 'tracking'])->name('lapor.tracking');
Route::get('/riwayat', [LaporanController::class, 'riwayat'])->name('riwayat');
Route::get('/laporan/{kode}', [LaporanController::class, 'show'])->name('lapor.detail');

// ===== 2. AUTENTIKASI =====
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Profil Pengguna (Masyarakat / Umum)
Route::middleware('auth')->group(function () {
    Route::get('/profil', [AuthController::class, 'profil'])->name('profil');
    Route::post('/profil/update', [AuthController::class, 'updateProfil'])->name('profil.update');
    Route::post('/profil/password', [AuthController::class, 'updatePassword'])->name('profil.password');
});

// ===== 3. MODUL SUPERADMIN / ADMIN UTAMA =====
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,superadmin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Manajemen Laporan
    Route::get('/laporan', [AdminLaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/rekap', [AdminLaporanController::class, 'rekap'])->name('laporan.rekap');
    Route::get('/laporan/cetak', [AdminLaporanController::class, 'cetak'])->name('laporan.cetak');
    Route::get('/laporan/{id}', [AdminLaporanController::class, 'show'])->name('laporan.detail');
    Route::get('/laporan/{id}/view', [AdminLaporanController::class, 'show'])->name('laporan.show');
    Route::post('/laporan/{id}/status', [AdminLaporanController::class, 'updateStatus'])->name('laporan.update-status');
    Route::post('/laporan/{id}/teruskan', [AdminLaporanController::class, 'teruskan'])->name('laporan.teruskan');
    Route::post('/laporan/{id}/multi-assign', [AdminLaporanController::class, 'multiAssign'])->name('laporan.multi-assign');
    Route::delete('/laporan/{id}', [AdminLaporanController::class, 'destroy'])->name('laporan.destroy');

    // Master Data Inti & Civic Platform
    Route::resource('tps', AdminTpsController::class);
    Route::resource('kategori', KategoriController::class)->except(['create', 'show', 'edit']);
    Route::resource('opd', OpdController::class)->except(['create', 'show', 'edit']);
    Route::resource('pengguna', PenggunaController::class)->except(['create', 'show', 'edit']);
    Route::resource('petugas', PetugasController::class)->except(['create', 'show', 'edit']);

    // Civic Multi-Agency Platform Master Modules
    Route::resource('wilayah', WilayahController::class)->except(['create', 'show', 'edit']);
    Route::resource('instansi', InstansiController::class)->except(['create', 'show', 'edit']);
    Route::resource('routing', RoutingRuleController::class)->except(['create', 'show', 'edit']);
    Route::resource('sla', SlaRuleController::class)->except(['create', 'show', 'edit']);
    Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit.index');
    Route::get('/audit-log/view', [AuditLogController::class, 'index'])->name('audit-log');

    // Analitik & Statistik
    Route::get('/statistik', [StatistikController::class, 'index'])->name('statistik');
});

// ===== 4. MODUL INSTANSI & SUPERVISOR (Agency Portal) =====
Route::prefix('instansi')->name('instansi.')->middleware(['auth', 'role:admin_instansi,supervisor,superadmin,admin', 'org_access'])->group(function () {
    Route::get('/', [InstansiDashboardController::class, 'index'])->name('dashboard');
    Route::get('/laporan', [InstansiLaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/{id}', [InstansiLaporanController::class, 'show'])->name('laporan.detail');
    Route::get('/laporan/{id}/view', [InstansiLaporanController::class, 'show'])->name('laporan.show');
    Route::post('/laporan/{id}/assign-officer', [InstansiLaporanController::class, 'assignOfficer'])->name('laporan.assign-officer');
    Route::post('/laporan/{id}/add-collab', [InstansiLaporanController::class, 'addCollaborator'])->name('laporan.add-collab');
    Route::post('/laporan/{id}/status', [InstansiLaporanController::class, 'updateStatus'])->name('laporan.update-status');
});

// ===== 5. MODUL AUDITOR & PENGAWASAN INDEPENDEN =====
Route::prefix('auditor')->name('auditor.')->middleware(['auth', 'role:auditor,superadmin,admin'])->group(function () {
    Route::get('/', [AuditorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/laporan', [AuditorDashboardController::class, 'laporan'])->name('laporan');
    Route::get('/audit-log', [AuditorDashboardController::class, 'auditLog'])->name('audit-log');
});

// ===== 6. MODUL PETUGAS LAPANGAN =====
Route::prefix('petugas')->name('petugas.')->middleware(['auth', 'role:petugas'])->group(function () {
    Route::get('/', [PetugasDashboardController::class, 'index'])->name('dashboard');
    Route::get('/tugas', [PetugasTugasController::class, 'index'])->name('tugas.index');
    Route::get('/tugas/{id}', [PetugasTugasController::class, 'show'])->name('tugas.detail');
    Route::post('/tugas/{laporanOpdId}/ambil', [PetugasTugasController::class, 'ambilTugas'])->name('tugas.ambil');
    Route::post('/tugas/{id}/progress', [PetugasTugasController::class, 'updateProgress'])->name('tugas.progress');
});
