<?php
// Ensure view path exists in Vercel's writable /tmp directory
if (!is_dir('/tmp/views')) {
    mkdir('/tmp/views', 0755, true);
}

// Forward Vercel requests to normal index.php
require __DIR__ . '/../public/index.php';
