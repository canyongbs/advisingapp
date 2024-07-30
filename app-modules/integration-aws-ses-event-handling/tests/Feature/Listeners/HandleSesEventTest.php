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

use function Pest\Laravel\withHeaders;
use function Tests\loadFixtureFromModule;
use function Pest\Laravel\withoutMiddleware;

use AdvisingApp\Notification\Models\OutboundDeliverable;
use AdvisingApp\Webhook\Http\Middleware\VerifyAwsSnsRequest;
use AdvisingApp\Notification\Enums\NotificationDeliveryStatus;

beforeEach(function () {
    withoutMiddleware(VerifyAwsSnsRequest::class);
});

it('correctly handles the incoming SES event', function (string $event, NotificationDeliveryStatus $status, ?string $deliveryResponse) {
    // TODO: Change this test to run in a isolated non-tenantized landlord environment

    // Given that we have an outbound deliverable
    $deliverable = OutboundDeliverable::factory()->create();

    $tenant = Tenant::current();

    // And we receive some sort of SES event when attempting to deliver
    $snsData = loadFixtureFromModule('integration-aws-ses-event-handling', 'sns-notification');
    $messageContent = loadFixtureFromModule('integration-aws-ses-event-handling', $event);
    data_set($messageContent, 'mail.tags.outbound_deliverable_id.0', $deliverable->id);
    data_set($messageContent, 'mail.tags.tenant_id.0', $tenant->getKey());
    $snsData['Message'] = json_encode($messageContent);

    expect($deliverable->hasBeenDelivered())->toBe(false);
    expect($deliverable->delivery_status)->toBe(NotificationDeliveryStatus::Awaiting);
    expect($deliverable->last_delivery_attempt)->toBeNull();

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

    // The outbound deliverable should be appropriately updated based on the event
    $deliverable->refresh();

    if ($status === NotificationDeliveryStatus::Failed) {
        expect($deliverable->hasBeenDelivered())->toBe(false);
    } else {
        expect($deliverable->hasBeenDelivered())->toBe(true);
    }

    expect($deliverable->external_status)->toBe($event);
    expect($deliverable->delivery_status)->toBe($status);
    expect($deliverable->last_delivery_attempt)->tobeTruthy();

    if (! $deliveryResponse) {
        expect($deliverable->delivery_response)->toBe($deliveryResponse);
    }
})->with([
    'HandleSesBounceEvent' => [
        'event' => 'Bounce',
        'status' => NotificationDeliveryStatus::Failed,
        'response' => 'The email was not successfully delivered due to a permanent rejection from the recipient mail server.',
    ],
    'HandleSesDeliveryEvent' => [
        'event' => 'Delivery',
        'status' => NotificationDeliveryStatus::Successful,
        'response' => null,
    ],
    'HandleSesDeliveryDelayEvent' => [
        'event' => 'DeliveryDelay',
        'status' => NotificationDeliveryStatus::Failed,
        'response' => 'The email was not successfully delivered due to a temporary issue.',
    ],
    'HandleSesRejectEvent' => [
        'event' => 'Reject',
        'status' => NotificationDeliveryStatus::Failed,
        'response' => 'The email was not attempted to be delivered due to unsafe contents.',
    ],
    'HandleSesRenderingFailureEvent' => [
        'event' => 'RenderingFailure',
        'status' => NotificationDeliveryStatus::Failed,
        'response' => 'The email not successfully delivered due to a template rendering error.',
    ],
]);
