<?php

namespace App\Actions\Finders;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class ApplicationModules
{
    public function moduleConfig(string $module, string $path): array
    {
        $path = base_path("app-modules/{$module}/config/{$path}.php");

        if (file_exists($path)) {
            $config = require $path;

            return $config;
        }

        return [];
    }

    public function moduleConfigDirectory(string $module, string $path): Collection
    {
        $path = base_path("app-modules/{$module}/config/{$path}");

        return collect(File::files($path))->map(function ($file, $key) {
            return explode('.' . $file->getExtension(), $file->getFilename())[0];
        });
    }
}
