<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use Mockery\MockInterface;
use Illuminate\Http\Request;
use Aws\Sns\MessageValidator;

use function Pest\Laravel\mock;
use function Tests\loadFixtureFromModule;

use Assist\Webhook\Http\Middleware\VerifyAwsSnsRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
