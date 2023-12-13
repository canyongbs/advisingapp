<?php

namespace App\Concerns;

use Illuminate\Support\Facades\Event;
use Nuwave\Lighthouse\Events\BuildSchemaString;
use Nuwave\Lighthouse\Schema\Source\SchemaStitcher;

trait GraphSchemaDiscovery
{
    public function discoverSchema(string $path): void
    {
        Event::listen(function (BuildSchemaString $event) use ($path) {
            return (new SchemaStitcher($path))->getSchemaString();
        });
    }
}
