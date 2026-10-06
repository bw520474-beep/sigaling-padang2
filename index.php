<?php

// Arahkan otomatis ke folder public jika diakses via Apache localhost/IP
$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? ''
);

// Jika diakses langsung via subfolder root
if (!str_contains($uri, '/public')) {
    $target = rtrim($uri, '/') . '/public/';
    header("Location: {$target}", true, 302);
    exit;
}

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
