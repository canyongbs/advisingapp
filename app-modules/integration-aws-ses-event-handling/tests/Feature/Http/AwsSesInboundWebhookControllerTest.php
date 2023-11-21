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

use Illuminate\Support\Facades\Event;

use function Pest\Laravel\withHeaders;
use function Tests\loadFixtureFromModule;
use function Pest\Laravel\withoutMiddleware;

use Assist\Webhook\Http\Middleware\VerifyAwsSnsRequest;
use Assist\IntegrationAwsSesEventHandling\Events\SesOpenEvent;
use Assist\IntegrationAwsSesEventHandling\Events\SesSendEvent;
use Assist\IntegrationAwsSesEventHandling\Events\SesClickEvent;
use Assist\IntegrationAwsSesEventHandling\Events\SesBounceEvent;
use Assist\IntegrationAwsSesEventHandling\Events\SesRejectEvent;
use Assist\IntegrationAwsSesEventHandling\Events\SesDeliveryEvent;
use Assist\IntegrationAwsSesEventHandling\Events\SesComplaintEvent;
use Assist\IntegrationAwsSesEventHandling\Events\SesSubscriptionEvent;
use Assist\IntegrationAwsSesEventHandling\Events\SesDeliveryDelayEvent;
use Assist\IntegrationAwsSesEventHandling\Events\SesRenderingFailureEvent;

beforeEach(function () {
    Event::fake();

    withoutMiddleware(VerifyAwsSnsRequest::class);
});

it('handles a bounce event', function () {
    $snsData = loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');

    $snsData['Message'] = json_encode(loadFixtureFromModule('integration-aws-ses-event-handling', 'bounce'));

    $response = withHeaders(
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
    )->postJson(
        route('inbound.webhook.awsses'),
        $snsData,
    );

    $response->assertOk();

    Event::assertDispatched(SesBounceEvent::class);
});

it('handles a complaint event', function () {
    $snsData = loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');

    $snsData['Message'] = json_encode(loadFixtureFromModule('integration-aws-ses-event-handling', 'complaint'));

    $response = withHeaders(
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
    )->postJson(
        route('inbound.webhook.awsses'),
        $snsData,
    );

    $response->assertOk();

    Event::assertDispatched(SesComplaintEvent::class);
});

it('handles a delivery event', function () {
    $snsData = loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');

    $snsData['Message'] = json_encode(loadFixtureFromModule('integration-aws-ses-event-handling', 'delivery'));

    $response = withHeaders(
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
    )->postJson(
        route('inbound.webhook.awsses'),
        $snsData,
    );

    $response->assertOk();

    Event::assertDispatched(SesDeliveryEvent::class);
});

it('handles a send event', function () {
    $snsData = loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');

    $snsData['Message'] = json_encode(loadFixtureFromModule('integration-aws-ses-event-handling', 'send'));

    $response = withHeaders(
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
    )->postJson(
        route('inbound.webhook.awsses'),
        $snsData,
    );

    $response->assertOk();

    Event::assertDispatched(SesSendEvent::class);
});

it('handles a reject event', function () {
    $snsData = loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');

    $snsData['Message'] = json_encode(loadFixtureFromModule('integration-aws-ses-event-handling', 'reject'));

    $response = withHeaders(
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
    )->postJson(
        route('inbound.webhook.awsses'),
        $snsData,
    );

    $response->assertOk();

    Event::assertDispatched(SesRejectEvent::class);
});

it('handles a open event', function () {
    $snsData = loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');

    $snsData['Message'] = json_encode(loadFixtureFromModule('integration-aws-ses-event-handling', 'open'));

    $response = withHeaders(
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
    )->postJson(
        route('inbound.webhook.awsses'),
        $snsData,
    );

    $response->assertOk();

    Event::assertDispatched(SesOpenEvent::class);
});

it('handles a click event', function () {
    $snsData = loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');

    $snsData['Message'] = json_encode(loadFixtureFromModule('integration-aws-ses-event-handling', 'click'));

    $response = withHeaders(
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
    )->postJson(
        route('inbound.webhook.awsses'),
        $snsData,
    );

    $response->assertOk();

    Event::assertDispatched(SesClickEvent::class);
});

it('handles a renderingFailure event', function () {
    $snsData = loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');

    $snsData['Message'] = json_encode(loadFixtureFromModule('integration-aws-ses-event-handling', 'renderingFailure'));

    $response = withHeaders(
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
    )->postJson(
        route('inbound.webhook.awsses'),
        $snsData,
    );

    $response->assertOk();

    Event::assertDispatched(SesRenderingFailureEvent::class);
});

it('handles a DeliveryDelay event', function () {
    $snsData = loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');

    $snsData['Message'] = json_encode(loadFixtureFromModule('integration-aws-ses-event-handling', 'DeliveryDelay'));

    $response = withHeaders(
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
    )->postJson(
        route('inbound.webhook.awsses'),
        $snsData,
    );

    $response->assertOk();

    Event::assertDispatched(SesDeliveryDelayEvent::class);
});

it('handles a Subscription event', function () {
    $snsData = loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');

    $snsData['Message'] = json_encode(loadFixtureFromModule('integration-aws-ses-event-handling', 'Subscription'));

    $response = withHeaders(
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
    )->postJson(
        route('inbound.webhook.awsses'),
        $snsData,
    );

    $response->assertOk();

    Event::assertDispatched(SesSubscriptionEvent::class);
});
