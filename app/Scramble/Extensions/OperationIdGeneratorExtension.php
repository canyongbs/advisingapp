<?php

namespace App\Scramble\Extensions;

use Dedoc\Scramble\Extensions\OperationExtension;
use Dedoc\Scramble\Support\Generator\Operation;
use Dedoc\Scramble\Support\RouteInfo;

class OperationIdGeneratorExtension extends OperationExtension
{
    public function handle(Operation $operation, RouteInfo $routeInfo): void
    {
        if ($routeInfo->phpDoc()->getTagsByName('@operationId')) {
            return;
        }

        $routeName = $routeInfo->route->getName();

        // Remove `api.v1.` or `api.v2.` prefix from the route name
        if (preg_match('/^api\.v\d+\./', $routeName)) {
            $routeName = preg_replace('/^api\.v\d+\./', '', $routeName);
        }

        $operation->setOperationId($routeName);
    }
}
