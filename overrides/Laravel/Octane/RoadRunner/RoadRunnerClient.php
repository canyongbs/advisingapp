<?php

namespace Laravel\Octane\RoadRunner;

use Generator;
use Throwable;
use ReflectionFunction;
use Laravel\Octane\Octane;
use Illuminate\Http\Request;
use Laravel\Octane\OctaneResponse;
use Laravel\Octane\RequestContext;
use Laravel\Octane\Contracts\Client;
use Illuminate\Foundation\Application;
use Spiral\RoadRunner\Http\PSR7Worker;
use Laravel\Octane\Contracts\StoppableClient;
use Laravel\Octane\MarshalsPsr7RequestsAndResponses;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RoadRunnerClient implements Client, StoppableClient
{
    use MarshalsPsr7RequestsAndResponses;

    public function __construct(protected PSR7Worker $client) {}

    /**
     * Marshal the given request context into an Illuminate request.
     */
    public function marshalRequest(RequestContext $context): array
    {
        return [
            $this->toHttpFoundationRequest($context->psr7Request),
            $context,
        ];
    }

    /**
     * Send the response to the server.
     */
    public function respond(RequestContext $context, OctaneResponse $octaneResponse): void
    {
        if ($octaneResponse->outputBuffer &&
            ! $octaneResponse->response instanceof StreamedResponse &&
            ! $octaneResponse->response instanceof BinaryFileResponse) {
            $octaneResponse->response->setContent(
                $octaneResponse->outputBuffer . $octaneResponse->response->getContent()
            );
        }

        $responseCallback = $octaneResponse->response->getCallback();

        if (
            ($octaneResponse->response instanceof StreamedResponse) &&
            $responseCallback &&
            (((new ReflectionFunction($responseCallback))->getReturnType()?->getName()) === Generator::class)
        ) {
            $this->client->getHttpWorker()->respond(
                $octaneResponse->response->getStatusCode(),
                $responseCallback(),
                $this->toPsr7Response($octaneResponse->response)->getHeaders(),
            );
        } else {
            $this->client->respond($this->toPsr7Response($octaneResponse->response));
        }
    }

    /**
     * Send an error message to the server.
     */
    public function error(Throwable $e, Application $app, Request $request, RequestContext $context): void
    {
        $this->client->getWorker()->error(Octane::formatExceptionForClient(
            $e,
            $app->make('config')->get('app.debug')
        ));
    }

    /**
     * Stop the underlying server / worker.
     */
    public function stop(): void
    {
        $this->client->getWorker()->stop();
    }
}
