<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
