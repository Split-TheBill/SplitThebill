<?php

declare(strict_types=1);

$storagePath = '/tmp/storage';

putenv('LARAVEL_STORAGE_PATH='.$storagePath);
$_ENV['LARAVEL_STORAGE_PATH'] = $storagePath;
$_SERVER['LARAVEL_STORAGE_PATH'] = $storagePath;

if (($vercelUrl = getenv('VERCEL_URL')) !== false && $vercelUrl !== '') {
    $appUrl = 'https://'.$vercelUrl;

    putenv('APP_URL='.$appUrl);
    $_ENV['APP_URL'] = $appUrl;
    $_SERVER['APP_URL'] = $appUrl;
}

foreach ([
    $storagePath.'/framework/cache/data',
    $storagePath.'/framework/sessions',
    $storagePath.'/framework/views',
    $storagePath.'/logs',
    '/tmp/views',
] as $directory) {
    if (! is_dir($directory)) {
        @mkdir($directory, 0775, true);
    }
}

require __DIR__.'/../public/index.php';
