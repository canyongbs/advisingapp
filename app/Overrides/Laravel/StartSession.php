<?php

namespace App\Overrides\Laravel;

use Illuminate\Contracts\Session\Session;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Session\Middleware\StartSession as BaseStartSession;

class StartSession extends BaseStartSession
{
    protected function addCookieToResponse(Response $response, Session $session)
    {
        $config = app()->make('config')->get('session');

        if ($this->sessionIsPersistent($config)) {
            $response->headers->setCookie(new Cookie(
                $session->getName(),
                $session->getId(),
                $this->getCookieExpirationDate(),
                $config['path'],
                $config['domain'],
                $config['secure'] ?? false,
                $config['http_only'] ?? true,
                false,
                $config['same_site'] ?? null,
                $config['partitioned'] ?? false
            ));
        }
    }
}
