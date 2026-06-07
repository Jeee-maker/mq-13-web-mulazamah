<?php
// Ensure storage paths exist in Vercel's writable /tmp directory
$dirs = ['framework/views', 'framework/cache', 'framework/sessions', 'logs', 'bootstrap/cache'];
foreach ($dirs as $dir) {
    if (!is_dir("/tmp/storage/{$dir}")) {
        mkdir("/tmp/storage/{$dir}", 0755, true);
    }
}

// Override cache files to use /tmp
$_ENV['APP_SERVICES_CACHE'] = '/tmp/storage/bootstrap/cache/services.php';
$_ENV['APP_PACKAGES_CACHE'] = '/tmp/storage/bootstrap/cache/packages.php';
$_ENV['APP_CONFIG_CACHE'] = '/tmp/storage/bootstrap/cache/config.php';
$_ENV['APP_ROUTES_CACHE'] = '/tmp/storage/bootstrap/cache/routes.php';
$_ENV['APP_EVENTS_CACHE'] = '/tmp/storage/bootstrap/cache/events.php';

// Forward Vercel requests to normal index.php
require __DIR__ . '/../public/index.php';
