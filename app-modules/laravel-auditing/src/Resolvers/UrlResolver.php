<?php

namespace Assist\Auditing\Resolvers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use Assist\Auditing\Contracts\Auditable;

class UrlResolver implements \Assist\Auditing\Contracts\Resolver
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
