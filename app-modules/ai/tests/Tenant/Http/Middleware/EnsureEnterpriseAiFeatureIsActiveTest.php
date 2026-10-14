<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\Ai\Http\Middleware\EnsureEnterpriseAiFeatureIsActive;
use AdvisingApp\Ai\Models\CustomerAdvisor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\get;
use function Pest\Laravel\postJson;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

use function Tests\setEnterpriseAiEnabled;

$handleEnterpriseAiRequest = function (Request $request): Response {
    return app(EnsureEnterpriseAiFeatureIsActive::class)->handle(
        $request,
        fn (): Response => response()->json(['ok' => true]),
    );
};

it('lets the request through while Enterprise AI is enabled', function () use ($handleEnterpriseAiRequest) {
    $response = $handleEnterpriseAiRequest(Request::create('/', 'GET'));

    expect($response->getStatusCode())->toBe(200);
});

it('returns a JSON forbidden response to JSON requests while Enterprise AI is disabled', function () use ($handleEnterpriseAiRequest) {
    setEnterpriseAiEnabled(false);

    $request = Request::create('/', 'POST');
    $request->headers->set('Accept', 'application/json');

    $response = $handleEnterpriseAiRequest($request);

    expect($response->getStatusCode())->toBe(403)
        ->and(json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR))
        ->toBe(['error' => 'Enterprise AI is not enabled.']);
});

it('aborts other requests with forbidden while Enterprise AI is disabled', function () use ($handleEnterpriseAiRequest) {
    setEnterpriseAiEnabled(false);

    $thrownException = null;

    try {
        $handleEnterpriseAiRequest(Request::create('/', 'GET'));
    } catch (HttpException $exception) {
        $thrownException = $exception;
    }

    expect($thrownException)->toBeInstanceOf(HttpException::class)
        ->and($thrownException?->getStatusCode())->toBe(403);
});

describe('customer advisor widget routes', function () {
    it('blocks the customer advisor widget api while Enterprise AI is disabled', function (string $routeName) {
        $advisor = CustomerAdvisor::factory()->create(['is_embed_enabled' => true]);

        setEnterpriseAiEnabled(false);

        postJson(route($routeName, ['advisor' => $advisor]))
            ->assertForbidden()
            ->assertJson(['error' => 'Enterprise AI is not enabled.']);
    })->with([
        'primary' => 'widgets.ai.customer-advisors.api.entry',
        'legacy' => 'widgets.ai.qna-advisors.api.entry',
    ]);

    it('blocks the customer advisor widget assets while Enterprise AI is disabled', function () {
        setEnterpriseAiEnabled(false);

        get(route('widgets.ai.customer-advisors.asset', ['file' => 'widget.js']))
            ->assertForbidden();
    });
});

describe('advisor routes', function () {
    it('protects the authenticated advisor routes', function (string $routeName) {
        expect(Route::getRoutes()->getByName($routeName)->gatherMiddleware())
            ->toContain(EnsureEnterpriseAiFeatureIsActive::class);
    })->with([
        'ai.advisors.threads.show',
        'ai.advisors.threads.messages.send',
        'ai.advisors.threads.messages.retry',
        'ai.advisors.threads.messages.complete-response',
        'ai.advisors.threads.download-image',
        'ai.customer-advisors.preview-embed',
    ]);
});
