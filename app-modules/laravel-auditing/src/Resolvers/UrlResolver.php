<?php

namespace Assist\LaravelAuditing\Resolvers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use Assist\LaravelAuditing\Contracts\Auditable;

class UrlResolver implements \Assist\LaravelAuditing\Contracts\Resolver
{
    /**
     * @return string
     */
    public static function resolve(Auditable $auditable): string
    {
        if (! empty($auditable->preloadedResolverData['url'])) {
            return $auditable->preloadedResolverData['url'];
        }

        if (App::runningInConsole()) {
            return self::resolveCommandLine();
        }

        return Request::fullUrl();
    }

    public static function resolveCommandLine(): string
    {
        $command = \Request::server('argv', null);

        if (is_array($command)) {
            return implode(' ', $command);
        }

        return 'console';
    }
}
