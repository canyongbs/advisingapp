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

use App\Models\Tenant;
use Illuminate\Support\Facades\Event;

use function Pest\Laravel\withHeaders;
use function Tests\loadFixtureFromModule;
use function Pest\Laravel\withoutMiddleware;

use AdvisingApp\Webhook\Http\Middleware\VerifyAwsSnsRequest;
use AdvisingApp\IntegrationAwsSesEventHandling\Events\SesOpenEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\Events\SesSendEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\Events\SesClickEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\Events\SesBounceEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\Events\SesRejectEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\Events\SesDeliveryEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\Events\SesComplaintEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\Events\SesSubscriptionEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\Events\SesDeliveryDelayEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\Events\SesRenderingFailureEvent;

beforeEach(function () {
    Event::fake();

    withoutMiddleware(VerifyAwsSnsRequest::class);
});

it('handles a bounce event', function () {
    $snsData = loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');

    $tenant = Tenant::current();

    $messageContent = loadFixtureFromModule('integration-aws-ses-event-handling', 'Bounce');
    data_set($messageContent, 'mail.tags.tenant_id.0', $tenant->getKey());
    $snsData['Message'] = json_encode($messageContent);

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
        route('landlord.api.inbound.webhook.awsses'),
        $snsData,
    );

    $response->assertOk();

    Event::assertDispatched(SesBounceEvent::class);
});

it('handles a complaint event', function () {
    $snsData = loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');

    $tenant = Tenant::current();

    $messageContent = loadFixtureFromModule('integration-aws-ses-event-handling', 'Complaint');
    data_set($messageContent, 'mail.tags.tenant_id.0', $tenant->getKey());
    $snsData['Message'] = json_encode($messageContent);

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
        route('landlord.api.inbound.webhook.awsses'),
        $snsData,
    );

    $response->assertOk();

    Event::assertDispatched(SesComplaintEvent::class);
});

it('handles a delivery event', function () {
    $snsData = loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');

    $tenant = Tenant::current();

    $messageContent = loadFixtureFromModule('integration-aws-ses-event-handling', 'Delivery');
    data_set($messageContent, 'mail.tags.tenant_id.0', $tenant->getKey());
    $snsData['Message'] = json_encode($messageContent);

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
        route('landlord.api.inbound.webhook.awsses'),
        $snsData,
    );

    $response->assertOk();

    Event::assertDispatched(SesDeliveryEvent::class);
});

it('handles a send event', function () {
    $snsData = loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');

    $tenant = Tenant::current();

    $messageContent = loadFixtureFromModule('integration-aws-ses-event-handling', 'Send');
    data_set($messageContent, 'mail.tags.tenant_id.0', $tenant->getKey());
    $snsData['Message'] = json_encode($messageContent);

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
        route('landlord.api.inbound.webhook.awsses'),
        $snsData,
    );

    $response->assertOk();

    Event::assertDispatched(SesSendEvent::class);
});

it('handles a reject event', function () {
    $snsData = loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');

    $tenant = Tenant::current();

    $messageContent = loadFixtureFromModule('integration-aws-ses-event-handling', 'Reject');
    data_set($messageContent, 'mail.tags.tenant_id.0', $tenant->getKey());
    $snsData['Message'] = json_encode($messageContent);

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
        route('landlord.api.inbound.webhook.awsses'),
        $snsData,
    );

    $response->assertOk();

    Event::assertDispatched(SesRejectEvent::class);
});

it('handles a open event', function () {
    $snsData = loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');

    $tenant = Tenant::current();

    $messageContent = loadFixtureFromModule('integration-aws-ses-event-handling', 'Open');
    data_set($messageContent, 'mail.tags.tenant_id.0', $tenant->getKey());
    $snsData['Message'] = json_encode($messageContent);

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
        route('landlord.api.inbound.webhook.awsses'),
        $snsData,
    );

    $response->assertOk();

    Event::assertDispatched(SesOpenEvent::class);
});

it('handles a click event', function () {
    $snsData = loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');

    $tenant = Tenant::current();

    $messageContent = loadFixtureFromModule('integration-aws-ses-event-handling', 'Click');
    data_set($messageContent, 'mail.tags.tenant_id.0', $tenant->getKey());
    $snsData['Message'] = json_encode($messageContent);

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
        route('landlord.api.inbound.webhook.awsses'),
        $snsData,
    );

    $response->assertOk();

    Event::assertDispatched(SesClickEvent::class);
});

it('handles a renderingFailure event', function () {
    $snsData = loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');

    $tenant = Tenant::current();

    $messageContent = loadFixtureFromModule('integration-aws-ses-event-handling', 'RenderingFailure');
    data_set($messageContent, 'mail.tags.tenant_id.0', $tenant->getKey());
    $snsData['Message'] = json_encode($messageContent);

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
        route('landlord.api.inbound.webhook.awsses'),
        $snsData,
    );

    $response->assertOk();

    Event::assertDispatched(SesRenderingFailureEvent::class);
});

it('handles a DeliveryDelay event', function () {
    $snsData = loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');

    $tenant = Tenant::current();

    $messageContent = loadFixtureFromModule('integration-aws-ses-event-handling', 'DeliveryDelay');
    data_set($messageContent, 'mail.tags.tenant_id.0', $tenant->getKey());
    $snsData['Message'] = json_encode($messageContent);

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
        route('landlord.api.inbound.webhook.awsses'),
        $snsData,
    );

    $response->assertOk();

    Event::assertDispatched(SesDeliveryDelayEvent::class);
});

it('handles a Subscription event', function () {
    $snsData = loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');

    $tenant = Tenant::current();

    $messageContent = loadFixtureFromModule('integration-aws-ses-event-handling', 'Subscription');
    data_set($messageContent, 'mail.tags.tenant_id.0', $tenant->getKey());
    $snsData['Message'] = json_encode($messageContent);

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
        route('landlord.api.inbound.webhook.awsses'),
        $snsData,
    );

    $response->assertOk();

    Event::assertDispatched(SesSubscriptionEvent::class);
});
