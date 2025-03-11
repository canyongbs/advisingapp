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

use AdvisingApp\IntegrationTwilio\Settings\TwilioSettings;
use AdvisingApp\IntegrationTwilio\Tests\Fixtures\ClientMock;
use AdvisingApp\Notification\Enums\SmsMessageEventType;
use AdvisingApp\Notification\Models\SmsMessage;
use AdvisingApp\Notification\Tests\Fixtures\TestSmsNotification;
use AdvisingApp\Prospect\Models\Prospect;
use Twilio\Rest\Api\V2010;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Twilio\Rest\Api\V2010\Account\MessageList;
use Twilio\Rest\Client;
use Twilio\Rest\MessagingBase;

beforeEach(function () {
    $settings = app()->make(TwilioSettings::class);

    $settings->account_sid = 'abc123';
    $settings->auth_token = 'abc123';
    $settings->from_number = '+11231231234';

    $settings->save();

    $mockMessageList = mock(MessageList::class);

    $numSegments = rand(1, 5);

    $mockMessageList->shouldReceive('create')->andReturn(
        new MessageInstance(
            new V2010(new MessagingBase(new Client(
                username: $settings->account_sid,
                password: $settings->auth_token,
            ))),
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
});

it('will create an SmsMessage for the notification', function () {
    $notifiable = Prospect::factory()->create();

    $notification = new TestSmsNotification();

    $notifiable->notify($notification);

    $smsMessages = SmsMessage::all();

    expect($smsMessages->count())->toBe(1);
    expect($smsMessages->first()->notification_class)->toBe(TestSmsNotification::class);
    expect($smsMessages->first()->events->first()->type)->toBe(SmsMessageEventType::Dispatched);
});

// TODO Add more tests for SMS Demo mode etc.
