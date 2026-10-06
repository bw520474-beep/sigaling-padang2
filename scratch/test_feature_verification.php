<?php

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require_once dirname(__DIR__) . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\LaporanOpd;
use App\Models\Penanganan;
use Illuminate\Support\Facades\Auth;

echo "=== 1. VERIFIKASI AKUN PETUGAS PER OPD ===\n";
$petugasUsers = User::where('role', 'petugas')->with('opd')->get();
foreach ($petugasUsers as $u) {
    echo "ID: {$u->id} | {$u->email} | Nama: {$u->name} | OPD: " . ($u->opd ? $u->opd->nama_opd : 'Tanpa OPD') . "\n";
}

echo "\n=== 2. VERIFIKASI TUGAS TERSEDIA PER PETUGAS ===\n";
// Simulasikan login petugas DLH (ID 3, opd_id = 1)
$dlhUser = User::find(3);
Auth::login($dlhUser);
$dlhTugas = LaporanOpd::with(['laporan', 'opd'])
    ->whereNull('petugas_id')
    ->when($dlhUser->opd_id, function ($q) use ($dlhUser) {
        $q->where('opd_id', $dlhUser->opd_id);
    })->get();
echo "Tugas tersedia untuk Petugas DLH (OPD 1): " . $dlhTugas->count() . " tugas\n";
foreach ($dlhTugas as $dt) {
    echo "  - Lap #{$dt->laporan_id} | OPD ID: {$dt->opd_id} ({$dt->opd->nama_opd})\n";
}

// Simulasikan login petugas PUPR (ID 11, opd_id = 2)
$puprUser = User::find(11);
Auth::login($puprUser);
$puprTugas = LaporanOpd::with(['laporan', 'opd'])
    ->whereNull('petugas_id')
    ->when($puprUser->opd_id, function ($q) use ($puprUser) {
        $q->where('opd_id', $puprUser->opd_id);
    })->get();
echo "Tugas tersedia untuk Petugas PUPR (OPD 2): " . $puprTugas->count() . " tugas\n";
foreach ($puprTugas as $pt) {
    echo "  - Lap #{$pt->laporan_id} | OPD ID: {$pt->opd_id} ({$pt->opd->nama_opd})\n";
}

echo "\n=== 3. VERIFIKASI DATA STATISTIK CONTROLLER ===\n";
$controller = new App\Http\Controllers\Admin\StatistikController();
$response = $controller->index();
$viewData = $response->getData();
echo "Status Counts: " . count($viewData['statusCounts']) . "\n";
echo "Kecamatan Stats: " . count($viewData['kecamatanStats']) . "\n";
echo "Kategori Stats: " . count($viewData['kategoriStats']) . "\n";
echo "OPD Stats: " . count($viewData['opdStats']) . "\n";
echo "TimeSeries Harian Labels: " . count($viewData['timeSeriesData']['harian']['labels']) . " hari\n";
echo "TimeSeries Mingguan Labels: " . count($viewData['timeSeriesData']['mingguan']['labels']) . " minggu\n";
echo "TimeSeries Bulanan Labels: " . count($viewData['timeSeriesData']['bulanan']['labels']) . " bulan\n";
echo "TimeSeries Tahunan Labels: " . count($viewData['timeSeriesData']['tahunan']['labels']) . " tahun\n";

echo "\n=== 4. VERIFIKASI ADMIN LAPORAN REKAP & CETAK ===\n";
$laporanCtrl = new App\Http\Controllers\Admin\AdminLaporanController();
$req = new Illuminate\Http\Request();
$rekapView = $laporanCtrl->rekap($req);
echo "Rekap View Total Laporan: " . $rekapView->getData()['totalLaporan'] . "\n";
$cetakView = $laporanCtrl->cetak($req);
echo "Cetak View Total Laporan: " . $cetakView->getData()['totalLaporan'] . "\n";

echo "\nSEMUA TEST VERIFIKASI BERJALAN SUKSES!\n";
