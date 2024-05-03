<?php

namespace App\GraphQL\Execution;

use Closure;
use GraphQL\Error\Error;
use App\Exceptions\IntegrationException;
use Nuwave\Lighthouse\Execution\ErrorHandler;

class IntegrationErrorHandler implements ErrorHandler
{
    public function __invoke(?Error $error, Closure $next): ?array
    {
        if ($error === null) {
            return $next(null);
        }

        if ($error->getPrevious() instanceof IntegrationException) {
            report($error->getPrevious());
        }

        return $next($error);
    }
}
