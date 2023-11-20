<?php

namespace Tests\Concerns;

use App\Actions\Paths\ModulePath;

trait LoadsFixtures
{
    public function loadFixtureFromModule(string $module, string $file): mixed
    {
        $modulePath = resolve(ModulePath::class);

        return json_decode(file_get_contents($modulePath($module, "tests/Fixtures/{$file}.json")), true);
    }
}
