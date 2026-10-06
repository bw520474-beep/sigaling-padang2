<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$svc = new App\Services\RoutingEngineService();
// Subcat 16: jalan-rusak-berlubang
$subcatJalan = App\Models\Subcategory::where('slug', 'jalan-rusak-berlubang')->first();
$res = $svc->resolveRouting($subcatJalan->category_id, $subcatJalan->id);

echo "1. Jalan Rusak -> Org: " . $res['organization']->name . " | Bidang: " . ($res['department'] ? $res['department']->name : '-') . " | Priority: " . $res['priority'] . " | SLA: " . $res['sla_hours'] . " jam\n";

// Subcat: limbah cair sungai
$subcatLimbah = App\Models\Subcategory::where('slug', 'limbah-cair-sungai')->first();
$res2 = $svc->resolveRouting($subcatLimbah->category_id, $subcatLimbah->id);
echo "2. Limbah Sungai -> Org: " . $res2['organization']->name . " | Bidang: " . ($res2['department'] ? $res2['department']->name : '-') . " | Priority: " . $res2['priority'] . " | SLA: " . $res2['sla_hours'] . " jam\n";

// Suggestion test
$sug = $svc->suggestClassification("Ada laporan jalan aspal berlubang parah di dekat pasar");
echo "3. Auto-suggestion test: " . ($sug['matched'] ? $sug['subcategory']->name . " (Kategori: " . $sug['category']->nama_kategori . ")" : "Tidak terdeteksi") . "\n";
