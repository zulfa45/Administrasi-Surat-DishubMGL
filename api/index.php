<?php

// Create storage directories in /tmp for serverless environment
$tmpDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
];
foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Forward all incoming Vercel serverless requests to Laravel's public/index.php
require __DIR__ . '/../public/index.php';
