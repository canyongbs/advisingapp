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

use Illuminate\Http\Request;
use Assist\Webhook\Models\InboundWebhook;

use function Tests\loadFixtureFromModule;

use Assist\Webhook\Enums\InboundWebhookSource;
use Assist\Webhook\Http\Middleware\HandleAwsSnsRequest;

it('will successfully handle a SubscriptionConfirmation request', function () {
    $request = Request::create(
        uri: url('/'),
        method: 'POST',
        content: json_encode(loadFixtureFromModule('webhook', 'HandleAwsSnsRequest/SubscriptionConfirmation')),
    );

    $request->headers->add(
        [
            'x-amz-sns-message-type' => 'SubscriptionConfirmation',
            'x-amz-sns-message-id' => '22b80b92-fdea-4c2c-8f9d-bdfb0c7bf324',
            'x-amz-sns-topic-arn' => 'arn:aws:sns:us-west-2:123456789012:MyTopic',
            'Content-Length' => '1336',
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Host' => 'example.com',
            'Connection' => 'Keep-Alive',
            'User-Agent' => 'Amazon Simple Notification Service Agent',
        ]
    );

    $next = function () {
        return response('Made it through.');
    };

    $response = (new HandleAwsSnsRequest())->handle($request, $next);

    expect($response->getContent())->toBe('')
        ->and($response->getStatusCode())->toBe(200);

    $inboundWebhooks = InboundWebhook::all();

    expect($inboundWebhooks)->toHaveCount(1)
        ->and($inboundWebhooks->first()->source)->toBe(InboundWebhookSource::AwsSns)
        ->and($inboundWebhooks->first()->event)->toBe('SubscriptionConfirmation')
        ->and($inboundWebhooks->first()->url)->toBe('http://example.com')
        ->and($inboundWebhooks->first()->payload)->toBe(json_encode(loadFixtureFromModule('webhook', 'HandleAwsSnsRequest/SubscriptionConfirmation')));
});

it('will successfully handle a UnsubscribeConfirmation request', function () {
    $request = Request::create(
        uri: url('/'),
        method: 'POST',
        content: json_encode(loadFixtureFromModule('webhook', 'HandleAwsSnsRequest/UnsubscribeConfirmation')),
    );

    $request->headers->add(
        [
            'x-amz-sns-message-type' => 'UnsubscribeConfirmation',
            'x-amz-sns-message-id' => '22b80b92-fdea-4c2c-8f9d-bdfb0c7bf324',
            'x-amz-sns-topic-arn' => 'arn:aws:sns:us-west-2:123456789012:MyTopic',
            'Content-Length' => '1336',
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Host' => 'example.com',
            'Connection' => 'Keep-Alive',
            'User-Agent' => 'Amazon Simple Notification Service Agent',
        ]
    );

    $next = function () {
        return response('Made it through.');
    };

    $response = (new HandleAwsSnsRequest())->handle($request, $next);

    expect($response->getContent())->toBe('')
        ->and($response->getStatusCode())->toBe(200);

    $inboundWebhooks = InboundWebhook::all();

    expect($inboundWebhooks)->toHaveCount(1)
        ->and($inboundWebhooks->first()->source)->toBe(InboundWebhookSource::AwsSns)
        ->and($inboundWebhooks->first()->event)->toBe('UnsubscribeConfirmation')
        ->and($inboundWebhooks->first()->url)->toBe('http://example.com')
        ->and($inboundWebhooks->first()->payload)->toBe(json_encode(loadFixtureFromModule('webhook', 'HandleAwsSnsRequest/UnsubscribeConfirmation')));
});

it('will throw an error if the type is not expected', function () {
    $request = Request::create(
        uri: url('/'),
        method: 'POST',
        content: json_encode(loadFixtureFromModule('webhook', 'HandleAwsSnsRequest/Unknown')),
    );

    $request->headers->add(
        [
            'x-amz-sns-message-type' => 'Unknown',
            'x-amz-sns-message-id' => '22b80b92-fdea-4c2c-8f9d-bdfb0c7bf324',
            'x-amz-sns-topic-arn' => 'arn:aws:sns:us-west-2:123456789012:MyTopic',
            'Content-Length' => '1336',
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Host' => 'example.com',
            'Connection' => 'Keep-Alive',
            'User-Agent' => 'Amazon Simple Notification Service Agent',
        ]
    );

    $next = function () {
        return response('Made it through.');
    };

    (new HandleAwsSnsRequest())->handle($request, $next);

    $inboundWebhooks = InboundWebhook::all();

    expect($inboundWebhooks)->toHaveCount(1)
        ->and($inboundWebhooks->first()->source)->toBe(InboundWebhookSource::AwsSns)
        ->and($inboundWebhooks->first()->event)->toBe('UnknownSnsType')
        ->and($inboundWebhooks->first()->url)->toBe('http://example.com')
        ->and($inboundWebhooks->first()->payload)->toBe(json_encode(loadFixtureFromModule('webhook', 'HandleAwsSnsRequest/UnsubscribeConfirmation')));
})->throws(Exception::class, 'Unknown AWS SNS webhook type');

it('will successfully handle a Notification request', function () {
    $request = Request::create(
        uri: url('/'),
        method: 'POST',
        content: json_encode(loadFixtureFromModule('webhook', 'HandleAwsSnsRequest/Notification')),
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
        return response('Made it through.');
    };

    $response = (new HandleAwsSnsRequest())->handle($request, $next);

    expect($response->getContent())->toBe('Made it through.')
        ->and($response->getStatusCode())->toBe(200);

    $inboundWebhooks = InboundWebhook::all();

    expect($inboundWebhooks)->toHaveCount(1)
        ->and($inboundWebhooks->first()->source)->toBe(InboundWebhookSource::AwsSns)
        ->and($inboundWebhooks->first()->event)->toBe('Notification')
        ->and($inboundWebhooks->first()->url)->toBe('http://example.com')
        ->and($inboundWebhooks->first()->payload)->toBe(json_encode(loadFixtureFromModule('webhook', 'HandleAwsSnsRequest/Notification')));
});
