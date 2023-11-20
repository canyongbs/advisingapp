<?php

namespace App\Actions\Paths;

class ModulePath
{
    public function __invoke(string $module, string $file): string
    {
        // Base directory for all modules
        $moduleBaseDir = base_path('app-modules');

        // Construct and return the full path
        return $moduleBaseDir . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $file;
    }
}
