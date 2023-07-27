<?php

namespace App\Actions\Finders;

use InvalidArgumentException;

class ApplicationModules
{
    public function moduleConfig(string $module, string $path)
    {
        $path = base_path("app-modules/{$module}/config/{$path}.php");

        if (file_exists($path)) {
            $config = require $path;

            return $config;
        }

        throw new InvalidArgumentException("Module [{$module}] does not have a configuration file.");
    }
}
