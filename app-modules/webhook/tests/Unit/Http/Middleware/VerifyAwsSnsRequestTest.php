<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\Webhook\Http\Middleware\VerifyAwsSnsRequest;
use Aws\Sns\MessageValidator;
use Illuminate\Http\Request;
use Mockery\MockInterface;

use function Pest\Laravel\mock;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use function Tests\loadFixtureFromModule;

it('will abort the request if the request cannot be verified to have originated from AWS SNS', function () {
    mock(MessageValidator::class, function (MockInterface $mock) {
        $mock->shouldReceive('isValid')->andReturn(false);
    });

    $request = Request::create(
        uri: url('/'),
        method: 'POST',
        content: json_encode(loadFixtureFromModule('webhook', 'sns-notification')),
    );

    $request->headers->add(
        [
            'x-amz-sns-message-type' => 'Notification',
            'x-amz-sns-message-id' => '22b80b92-fdea-4c2c-8f9d-bdfb0c7bf324',
            'x-amz-sns-topic-arn' => 'arn:aws:sns:us-west-2:123456789012:MyTopic',
            'x-amz-sns-subscription-arn' => 'arn:aws:sns:us-west-2:123456789012:MyTopic:c9135db0-26c4-47ec-8998-413945fb5a96',
            'Content-Length' => '773',
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Host' => 'example.com',
            'Connection' => 'Keep-Alive',
            'User-Agent' => 'Amazon Simple Notification Service Agent',
        ]
    );

    $next = function () {
        return response('test');
    };

    (new VerifyAwsSnsRequest())->handle($request, $next);
})->throws(exception: NotFoundHttpException::class);

it('will process the request if the request can be verified to have originated from AWS SNS', function () {
    mock(MessageValidator::class, function (MockInterface $mock) {
        $mock->shouldReceive('isValid')->andReturn(true);
    });

    $request = Request::create(
        uri: url('/'),
        method: 'POST',
        content: json_encode(loadFixtureFromModule('webhook', 'sns-notification')),
    );

    $request->headers->add(
        [
            'x-amz-sns-message-type' => 'Notification',
            'x-amz-sns-message-id' => '22b80b92-fdea-4c2c-8f9d-bdfb0c7bf324',
            'x-amz-sns-topic-arn' => 'arn:aws:sns:us-west-2:123456789012:MyTopic',
            'x-amz-sns-subscription-arn' => 'arn:aws:sns:us-west-2:123456789012:MyTopic:c9135db0-26c4-47ec-8998-413945fb5a96',
            'Content-Length' => '773',
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Host' => 'example.com',
            'Connection' => 'Keep-Alive',
            'User-Agent' => 'Amazon Simple Notification Service Agent',
        ]
    );

    $next = function () {
        return response('test');
    };

    $response = (new VerifyAwsSnsRequest())->handle($request, $next);

    expect($response->getContent())->toBe('test')
        ->and($response->getStatusCode())->toBe(200);
});
