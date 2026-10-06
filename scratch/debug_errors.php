<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Http\Request;

$targets = [
    ['uri' => '/buat-laporan', 'role' => null],
    ['uri' => '/admin/routing', 'role' => 'superadmin'],
    ['uri' => '/admin/audit-log', 'role' => 'superadmin'],
    ['uri' => '/instansi', 'role' => 'admin_instansi'],
    ['uri' => '/instansi/laporan', 'role' => 'admin_instansi'],
    ['uri' => '/auditor/audit-log', 'role' => 'auditor'],
];

foreach ($targets as $t) {
    echo "--- TESTING: {$t['uri']} ---\n";
    $request = Request::create($t['uri'], 'GET');
    $app->instance('request', $request);

    if ($t['role']) {
        $user = User::where('role', $t['role'])->first();
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
        echo "Status: " . $response->getStatusCode() . "\n";
        if ($response->getStatusCode() >= 400 && $response->exception) {
            echo "Exception: " . get_class($response->exception) . "\n";
            echo "Message: " . $response->exception->getMessage() . "\n";
            echo "File: " . $response->exception->getFile() . ":" . $response->exception->getLine() . "\n";
        }
    } catch (\Throwable $e) {
        echo "Direct Exception: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . "\n";
    }
}
