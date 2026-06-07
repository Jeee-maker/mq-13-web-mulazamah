<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/login', 'POST', [
    'username' => 'admin',
    'password' => 'UntukIndonesiaRaya123'
]);
// Add required session config for testing? 
// It's easier to just run a web request through curl.
