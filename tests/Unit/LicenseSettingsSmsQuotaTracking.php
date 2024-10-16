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

use App\Models\User;
use Twilio\Rest\Client;
use Tests\Unit\ClientMock;
use Twilio\Rest\Api\V2010;
use Twilio\Rest\MessagingBase;
use App\Models\Authenticatable;
use App\Settings\LicenseSettings;
use Tests\Unit\TestSmsNotification;
use AdvisingApp\Prospect\Models\Prospect;

use function Pest\Laravel\assertDatabaseCount;

use Twilio\Rest\Api\V2010\Account\MessageList;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use AdvisingApp\Notification\Models\OutboundDeliverable;
use AdvisingApp\IntegrationTwilio\Settings\TwilioSettings;
use AdvisingApp\Notification\Enums\NotificationDeliveryStatus;
use AdvisingApp\Notification\Exceptions\NotificationQuotaExceeded;

it('An sms is allowed to be sent if there is available quota and its quota usage is tracked', function () {
    $notifiable = Prospect::factory()->create();

    $notification = new TestSmsNotification();

    $settings = app()->make(TwilioSettings::class);

    $settings->account_sid = 'abc123';
    $settings->auth_token = 'abc123';
    $settings->from_number = '+11231231234';

    $settings->save();

    $mockMessageList = mock(MessageList::class);

    $numSegments = rand(1, 5);

    $mockMessageList->shouldReceive('create')->andReturn(
        new MessageInstance(
            new V2010(new MessagingBase(new Client())),
            [
                'sid' => 'abc123',
                'status' => 'queued',
                'from' => '+11231231234',
                'to' => '+11231231234',
                'body' => 'test',
                'num_segments' => $numSegments,
            ],
            'abc123'
        )
    );

    app()->bind(Client::class, fn () => new ClientMock(
        messageList: $mockMessageList,
        username: $settings->account_sid,
        password: $settings->auth_token,
    ));

    $notifiable->notify($notification);

    $outboundDeliverable = OutboundDeliverable::first();

    expect($outboundDeliverable->quota_usage)
        ->toBe($numSegments)
        ->and($outboundDeliverable->delivery_status)
        ->toBe(NotificationDeliveryStatus::Dispatched);
});

it('An sms is prevented from being sent if there is no available quota', function () {
    $notifiable = Prospect::factory()->create();

    $notification = new TestSmsNotification();

    $settings = app()->make(TwilioSettings::class);

    $settings->account_sid = 'abc123';
    $settings->auth_token = 'abc123';
    $settings->from_number = '+11231231234';

    $settings->save();

    $licenseSettings = app(LicenseSettings::class);

    $licenseSettings->data->limits->sms = 0;
    $licenseSettings->save();

    $mockMessageList = mock(MessageList::class);

    $numSegments = rand(1, 5);

    $mockMessageList->shouldReceive('create')->andReturn(
        new MessageInstance(
            new V2010(new MessagingBase(new Client())),
            [
                'sid' => 'abc123',
                'status' => 'queued',
                'from' => '+11231231234',
                'to' => '+11231231234',
                'body' => 'test',
                'num_segments' => $numSegments,
            ],
            'abc123'
        )
    );

    app()->bind(Client::class, fn () => new ClientMock(
        messageList: $mockMessageList,
        username: $settings->account_sid,
        password: $settings->auth_token,
    ));

    expect(fn () => $notifiable->notify($notification))
        ->toThrow(NotificationQuotaExceeded::class);

    assertDatabaseCount(OutboundDeliverable::class, 0);
});

it('An sms is sent to a super admin user even if there is no available quota', function () {
    $notifiable = User::factory()->create();

    $notifiable->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

    $notification = new TestSmsNotification();

    $settings = app()->make(TwilioSettings::class);

    $settings->account_sid = 'abc123';
    $settings->auth_token = 'abc123';
    $settings->from_number = '+11231231234';

    $settings->save();

    $licenseSettings = app(LicenseSettings::class);

    $licenseSettings->data->limits->sms = 0;
    $licenseSettings->save();

    $mockMessageList = mock(MessageList::class);

    $numSegments = rand(1, 5);

    $mockMessageList->shouldReceive('create')->andReturn(
        new MessageInstance(
            new V2010(new MessagingBase(new Client())),
            [
                'sid' => 'abc123',
                'status' => 'queued',
                'from' => '+11231231234',
                'to' => $notifiable->phone_number,
                'body' => 'test',
                'num_segments' => $numSegments,
            ],
            'abc123'
        )
    );

    app()->bind(Client::class, fn () => new ClientMock(
        messageList: $mockMessageList,
        username: $settings->account_sid,
        password: $settings->auth_token,
    ));

    $notifiable->notify($notification);

    assertDatabaseCount(OutboundDeliverable::class, 1);
});
