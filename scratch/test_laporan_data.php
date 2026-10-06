<?php

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require_once dirname(__DIR__) . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Laporan;
use Carbon\Carbon;

echo "--- TOTAL LAPORAN ---\n";
echo "Total: " . Laporan::count() . "\n";

// Sample Dates
$dates = Laporan::pluck('created_at');
echo "Dates sample: \n";
foreach ($dates->take(5) as $d) {
    echo $d->toDateTimeString() . "\n";
}
