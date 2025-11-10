<?php

namespace AdvisingApp\Form\Http\Middleware;

use AdvisingApp\Form\Models\Form;
use Closure;
use Fruitcake\Cors\CorsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class FormsWidgetCors
{
    public function __construct(
        protected CorsService $cors,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $requestingUrlHeader = $request->headers->get('origin') ?? $request->headers->get('referer');

        if (! $requestingUrlHeader) {
            return $response;
        }

        $appRootDomain = parse_url(Config::string('app.url'), PHP_URL_HOST);

        $requestingUrlHost = parse_url($requestingUrlHeader, PHP_URL_HOST);

        // Check if the requesting host is allowed in ANY Form
        $isAllowed = Form::query()
            ->whereJsonContains('allowed_domains', $requestingUrlHost)
            ->exists();

        if (
            $requestingUrlHost !== $appRootDomain &&
            ! $isAllowed
        ) {
            return $response;
        }

        $this->cors->setOptions([
            'allowedOrigins' => [$requestingUrlHeader],
            'allowedHeaders' => ['*'],
            'allowedMethods' => ['GET', 'POST', 'OPTIONS'],
            'supportsCredentials' => true,
        ]);

        if ($this->cors->isPreflightRequest($request)) {
            $response = $this->cors->handlePreflightRequest($request);

            $this->cors->varyHeader($response, 'Access-Control-Request-Method');

            return $response;
        }

        if ($request->getMethod() === 'OPTIONS') {
            $this->cors->varyHeader($response, 'Access-Control-Request-Method');
        }

        return $this->cors->addActualRequestHeaders($response, $request);
    }
}
