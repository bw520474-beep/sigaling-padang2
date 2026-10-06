<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Http\Request;

echo "=== TESTING HTTP VIEWS & PERMISSIONS ===\n\n";

$routes = [
    // Public routes (Guest)
    ['uri' => '/', 'role' => null, 'desc' => 'Landing page'],
    ['uri' => '/transparansi', 'role' => null, 'desc' => 'Public transparency'],
    ['uri' => '/peta', 'role' => null, 'desc' => 'Public map'],
    ['uri' => '/buat-laporan', 'role' => null, 'desc' => 'Create report form'],
    ['uri' => '/tracking', 'role' => null, 'desc' => 'Tracking page'],
    ['uri' => '/riwayat', 'role' => null, 'desc' => 'Public history'],
    
    // Superadmin
    ['uri' => '/admin', 'role' => 'superadmin', 'desc' => 'Superadmin Dashboard'],
    ['uri' => '/admin/laporan', 'role' => 'superadmin', 'desc' => 'Superadmin All Reports'],
    ['uri' => '/admin/instansi', 'role' => 'superadmin', 'desc' => 'Master Instansi'],
    ['uri' => '/admin/wilayah', 'role' => 'superadmin', 'desc' => 'Master Wilayah'],
    ['uri' => '/admin/routing', 'role' => 'superadmin', 'desc' => 'Master Routing Rules'],
    ['uri' => '/admin/sla', 'role' => 'superadmin', 'desc' => 'Master SLA Rules'],
    ['uri' => '/admin/audit-log', 'role' => 'superadmin', 'desc' => 'Superadmin Audit Trail'],

    // Instansi (PUPR)
    ['uri' => '/instansi', 'role' => 'admin_instansi', 'desc' => 'Instansi Dashboard'],
    ['uri' => '/instansi/laporan', 'role' => 'admin_instansi', 'desc' => 'Instansi Reports'],

    // Auditor
    ['uri' => '/auditor', 'role' => 'auditor', 'desc' => 'Auditor Dashboard'],
    ['uri' => '/auditor/laporan', 'role' => 'auditor', 'desc' => 'Auditor Reports SLA Monitor'],
    ['uri' => '/auditor/audit-log', 'role' => 'auditor', 'desc' => 'Auditor Independent Audit Trail'],

    // Petugas
    ['uri' => '/petugas', 'role' => 'petugas', 'desc' => 'Petugas Field Dashboard'],
    ['uri' => '/petugas/tugas', 'role' => 'petugas', 'desc' => 'Petugas Tasks'],
];

$pass = 0;
$fail = 0;

foreach ($routes as $r) {
    $request = Request::create($r['uri'], 'GET');
    $app->instance('request', $request);
    
    if ($r['role']) {
        $user = User::where('role', $r['role'])->first();
        if ($user) {
            $request->setUserResolver(function () use ($user) {
                return $user;
            });
            auth()->login($user);
        }
    } else {
        auth()->logout();
    }

    try {
        $response = $kernel->handle($request);
        $status = $response->getStatusCode();
        
        if ($status >= 200 && $status < 400) {
            echo "[OK $status] {$r['desc']} ({$r['uri']})\n";
            $pass++;
        } else {
            echo "[FAIL $status] {$r['desc']} ({$r['uri']})\n";
            $fail++;
        }
    } catch (\Throwable $e) {
        echo "[ERROR] {$r['desc']} ({$r['uri']}): " . $e->getMessage() . "\n";
        $fail++;
    }
}

echo "\nSummary: $pass passed, $fail failed\n";
