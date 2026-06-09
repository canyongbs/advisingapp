<?php

declare(strict_types = 1);

$basePath = __DIR__;

$directories = [
    $basePath . '/vendor/laravel/framework/src',
    $basePath . '/vendor/filament',
    $basePath . '/vendor/livewire/livewire/src',
    $basePath . '/app',
    $basePath . '/app-modules',
];

$ignore = [
    '/tests/',
    '/Tests/',
    '/test/',
    '/database/migrations/',
    '/database/seeders/',
    '/database/factories/',
    '/_ide_helper',
    '/node_modules/',
];

foreach ($directories as $directory) {
    if (! is_dir($directory)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($iterator as $file) {
        if ($file->getExtension() !== 'php') {
            continue;
        }

        $path = $file->getPathname();

        foreach ($ignore as $needle) {
            if (str_contains($path, $needle)) {
                continue 2;
            }
        }

        opcache_compile_file($path);
    }
}
