<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\Organization;
use App\Models\Region;
use App\Models\ReportAssignment;
use App\Models\ReporterIdentity;
use App\Models\Subcategory;
use App\Models\User;
use App\Services\AuditTrailService;
use App\Services\RoutingEngineService;
use Illuminate\Http\Request;

echo "=== START E2E CIVIC PLATFORM VERIFICATION ===\n\n";

$passed = 0;
$failed = 0;

function assertCondition($name, $condition) {
    global $passed, $failed;
    if ($condition) {
        echo "[PASS] $name\n";
        $passed++;
    } else {
        echo "[FAIL] $name\n";
        $failed++;
    }
}

// 1. Check Master Data counts
$orgCount = Organization::count();
$deptCount = Department::count();
$subcatCount = Subcategory::count();
$regCount = Region::count();

assertCondition("Organizations seeded ($orgCount >= 9)", $orgCount >= 9);
assertCondition("Departments seeded ($deptCount >= 10)", $deptCount >= 10);
assertCondition("Subcategories seeded ($subcatCount >= 20)", $subcatCount >= 20);
assertCondition("Regions seeded ($regCount >= 14)", $regCount >= 14);

// 2. Test Smart Classifier Routing Service
$engine = app(RoutingEngineService::class);

$suggestion1 = $engine->suggestClassification("Ada lubang besar di jalan Khatib Sulaiman membahayakan motor");
assertCondition("Classifier suggests PUPR for road issue", 
    isset($suggestion1['routing']['organization']) && str_starts_with($suggestion1['routing']['organization']->code, 'PUPR'));

$suggestion2 = $engine->suggestClassification("Limbah cair industri mencemari sungai Batang Arau bau sekali");
assertCondition("Classifier suggests DLH for waste/pollution issue", 
    isset($suggestion2['routing']['organization']) && str_starts_with($suggestion2['routing']['organization']->code, 'DLH'));

$suggestion3 = $engine->suggestClassification("Lampu jalan PJU padam total di simpang haru gelap gulita");
assertCondition("Classifier suggests DISHUB for traffic lights/PJU issue", 
    isset($suggestion3['routing']['organization']) && str_starts_with($suggestion3['routing']['organization']->code, 'DISHUB'));

// 3. Test Citizen Report Submission with Confidential Protection Level
$subcatJalan = Subcategory::where('slug', 'jalan-rusak-berlubang')->first();
$regKotoTangah = Region::where('code', 'PADANG-KOTO-TANGAH')->first();

$laporanController = app(App\Http\Controllers\LaporanController::class);

// Create dummy request
$citizenUser = User::where('role', 'masyarakat')->first() ?: User::first();

$req = Request::create('/buat-laporan', 'POST', [
    'judul' => 'Uji Coba Laporan Civic Platform E2E',
    'kategori_id' => $subcatJalan->category_id,
    'subcategory_id' => $subcatJalan->id,
    'region_id' => $regKotoTangah ? $regKotoTangah->id : null,
    'kecamatan' => 'Koto Tangah',
    'deskripsi' => 'Pengujian end-to-end pembuatan laporan dengan tingkat perlindungan rahasia dan disposisi otomatis.',
    'alamat' => 'Jl. Adinegoro Km 12',
    'latitude' => -0.8350,
    'longitude' => 100.3150,
    'protection_level' => 'confidential',
    'nama_pelapor' => 'Ahmad Warga Rahasia',
    'kontak_pelapor' => '081234567890',
    'email_pelapor' => 'ahmad.rahasia@example.com',
]);

// Set user on request
$req->setUserResolver(function() use ($citizenUser) {
    return $citizenUser;
});

// We can test the store logic directly
$res = $laporanController->store($req);

$newReport = Laporan::where('judul', 'Uji Coba Laporan Civic Platform E2E')->latest()->first();
assertCondition("Report created successfully", $newReport !== null);

if ($newReport) {
    assertCondition("Report has subcategory_id set", $newReport->subcategory_id == $subcatJalan->id);
    assertCondition("Report has SLA due date populated", $newReport->sla_due_at !== null);
    assertCondition("Report protection_level is confidential", $newReport->protection_level === 'confidential');

    // Check reporter_identities isolation
    $reporterIdentity = ReporterIdentity::where('report_id', $newReport->id)->first();
    assertCondition("Reporter identity record created and isolated", $reporterIdentity !== null);
    if ($reporterIdentity) {
        assertCondition("Reporter identity stores full name", $reporterIdentity->reporter_name === 'Ahmad Warga Rahasia');
        assertCondition("Reporter identity has confidential level", $reporterIdentity->protection_level === 'confidential');
    }

    // Check masked reporter name
    assertCondition("Masked name produces privacy-protected string", $newReport->masked_reporter_name !== 'Ahmad Warga Rahasia');
    echo "  -> Raw: Ahmad Warga Rahasia | Masked: " . $newReport->masked_reporter_name . "\n";
    echo "  -> Raw phone: 081234567890 | Masked: " . $newReport->masked_contact . "\n";

    // Check assignments
    $primaryAssignment = ReportAssignment::where('report_id', $newReport->id)->first();
    assertCondition("Primary agency assignment created", $primaryAssignment !== null);
    if ($primaryAssignment) {
        $assignedOrg = Organization::find($primaryAssignment->organization_id);
        assertCondition("Primary agency routed to PUPR", $assignedOrg && str_starts_with($assignedOrg->code, 'PUPR'));
    }

    // Check Audit Log
    $auditLogs = AuditLog::where('entity_id', (string)$newReport->id)->where('entity_type', 'report')->get();
    assertCondition("Audit log recorded (count >= 1)", $auditLogs->count() >= 1);
    if ($auditLogs->isNotEmpty()) {
        echo "  -> Audit action: " . $auditLogs->first()->action . " by " . $auditLogs->first()->actor_name . "\n";
    }

    // 4. Test Adding Collaborative Secondary Agency (e.g. Dishub)
    $dishub = Organization::where('code', 'LIKE', 'DISHUB%')->first();
    if ($dishub) {
        $secondaryAssign = ReportAssignment::create([
            'report_id' => $newReport->id,
            'organization_id' => $dishub->id,
            'status' => 'received',
            'priority' => 'Sedang',
            'notes' => 'Bantuan pengalihan arus lalu lintas saat perbaikan',
        ]);
        assertCondition("Multi-agency collaborative assignment created (DISHUB)", $secondaryAssign !== null);
    }

    // 5. Test Instansi Portal Query Scope
    $puprOrg = Organization::where('code', 'LIKE', 'PUPR%')->first();
    $puprAssignments = ReportAssignment::where('organization_id', $puprOrg->id)->count();
    assertCondition("PUPR assignments scoped query works ($puprAssignments)", $puprAssignments > 0);

    // 6. Test Auditor Log Queries
    $allAudits = AuditLog::latest()->limit(5)->get();
    $cnt = $allAudits->count();
    assertCondition("Auditor can query immutable audit trail ($cnt)", $cnt > 0);
}

// 7. Test Public Transparency Controller
$transparansiController = app(App\Http\Controllers\TransparansiController::class);
$view = $transparansiController->index();
assertCondition("Transparency controller executes without errors", $view->name() === 'pages.transparansi');
assertCondition("Transparency view has aggregate statistics", isset($view->getData()['slaRate']));

echo "\n=== TEST SUMMARY: $passed PASSED, $failed FAILED ===\n";

if ($failed === 0) {
    echo "ALL TESTS PASSED SUCCESSFULLY!\n";
}
