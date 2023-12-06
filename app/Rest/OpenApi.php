<?php

namespace App\Rest;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Lomkit\Rest\Documentation\Schemas\Path;
use Lomkit\Rest\Http\Controllers\Controller;

class OpenApi extends \Lomkit\Rest\Documentation\Schemas\OpenAPI
{
    public function generatePaths()
    {
        $paths = [];

        foreach (Route::getRoutes() as $route) {
            /** @var \Illuminate\Routing\Route $route */
            if (is_null($route->getName())) {
                continue;
            }

            if ($route->getControllerClass()) {
                $controller = $route->getController();

                if ($controller instanceof Controller) {
                    $path = match (Str::afterLast($route->getName(), '.')) {
                        'details' => (new Path())->generateDetailAndDestroy($controller),
                        'search' => (new Path())->generateSearch($controller),
                        'mutate' => (new Path())->generateMutate($controller),
                        'operate' => (new Path())->generateActions($controller),
                        'restore' => (new Path())->generateRestore($controller),
                        'forceDelete' => (new Path())->generateForceDelete($controller),
                        default => null
                    };

                    if (! is_null($path)) {
                        $paths['/' . $route->uri()] = $path;
                    }
                }
            }
        }

        return $paths;
    }
}
