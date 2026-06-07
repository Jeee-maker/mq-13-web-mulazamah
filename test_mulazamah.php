<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

Illuminate\Support\Facades\Auth::loginUsingId(2);
$controller = $app->make(App\Http\Controllers\MuridController::class);
try {
    $response = $controller->mulazamah();
    echo "Status: 200 OK\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
