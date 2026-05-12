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

use AdvisingApp\IntegrationTwilio\Settings\TwilioSettings;
use AdvisingApp\IntegrationTwilio\Tests\Fixtures\ClientMock;
use AdvisingApp\Notification\DataTransferObjects\SmsChannelResultData;
use AdvisingApp\Notification\Enums\SmsMessageEventType;
use AdvisingApp\Notification\Enums\SmsMessagingProvider;
use AdvisingApp\Notification\Exceptions\BouncedSmsException;
use AdvisingApp\Notification\Exceptions\SmsOptOutException;
use AdvisingApp\Notification\Models\SmsMessage;
use AdvisingApp\Notification\Notifications\Channels\SmsChannel;
use AdvisingApp\Notification\Tests\Fixtures\TestSmsNotification;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\BouncedPhoneNumber;
use AdvisingApp\StudentDataModel\Models\SmsOptOutPhoneNumber;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

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

it('will not send an SMS if recipient phone number has previously bounced', function () {
    $notifiable = Prospect::factory()->create();

    $phoneNumber = $notifiable->phoneNumbers()->create([
        'number' => '+13125000001',
        'can_receive_sms' => true,
    ]);

    $notifiable->primaryPhoneNumber()->associate($phoneNumber)->save();

    BouncedPhoneNumber::factory()->create([
        'number' => '+13125000001',
        'external_error_code' => '40001',
    ]);

    try {
        expect(fn () => $notifiable->notify(new TestSmsNotification()))->toThrow(BouncedSmsException::class);
    } finally {
        assertDatabaseHas('sms_message_events', [
            'type' => SmsMessageEventType::FailedDispatch,
        ]);
    }
});

it('creates an SmsOptOutPhoneNumber and records a FailedDispatch event when Telnyx returns error 40300, without rethrowing', function () {
    $notifiable = Prospect::factory()->create();

    $phoneNumber = $notifiable->phoneNumbers()->create([
        'number' => '+13125000002',
        'can_receive_sms' => true,
    ]);

    $notifiable->primaryPhoneNumber()->associate($phoneNumber)->save();

    $settings = app()->make(TwilioSettings::class);
    $settings->provider = SmsMessagingProvider::Telnyx;
    $settings->telnyx_api_key = 'test_api_key';
    $settings->save();

    $failureResult = SmsChannelResultData::from(['success' => false]);
    $failureResult->error = 'Forbidden - User has opted out of SMS';
    $failureResult->errorCode = '40300';

    $channel = mock(SmsChannel::class)
        ->shouldAllowMockingProtectedMethods()
        ->makePartial();
    $channel->shouldReceive('sendViaTelnyx')->andReturn($failureResult);

    app()->instance(SmsChannel::class, $channel);

    $notifiable->notify(new TestSmsNotification());

    assertDatabaseHas(SmsOptOutPhoneNumber::class, [
        'number' => '+13125000002',
    ]);

    $event = SmsMessage::first()->events()->first();
    expect($event->type)->toBe(SmsMessageEventType::FailedDispatch)
        ->and($event->payload['error_code'])->toBe('40300');
});

it('creates a BouncedPhoneNumber and records a FailedDispatch event when Telnyx returns error 40310, without rethrowing', function () {
    $notifiable = Prospect::factory()->create();

    $phoneNumber = $notifiable->phoneNumbers()->create([
        'number' => '+13125000003',
        'can_receive_sms' => true,
    ]);

    $notifiable->primaryPhoneNumber()->associate($phoneNumber)->save();

    $settings = app()->make(TwilioSettings::class);
    $settings->provider = SmsMessagingProvider::Telnyx;
    $settings->telnyx_api_key = 'test_api_key';
    $settings->save();

    $failureResult = SmsChannelResultData::from(['success' => false]);
    $failureResult->error = 'Not routable - destination is a landline or non-routable number';
    $failureResult->errorCode = '40310';

    $channel = mock(SmsChannel::class)
        ->shouldAllowMockingProtectedMethods()
        ->makePartial();
    $channel->shouldReceive('sendViaTelnyx')->andReturn($failureResult);

    app()->instance(SmsChannel::class, $channel);

    $notifiable->notify(new TestSmsNotification());

    assertDatabaseHas(BouncedPhoneNumber::class, [
        'number' => '+13125000003',
        'external_error_code' => '40310',
    ]);

    assertDatabaseMissing(SmsOptOutPhoneNumber::class, [
        'number' => '+13125000003',
    ]);

    $event = SmsMessage::first()->events()->first();
    expect($event->type)->toBe(SmsMessageEventType::FailedDispatch)
        ->and($event->payload['error_code'])->toBe('40310');
});

it('records a FailedDispatch event without creating opt-out or bounce records for other Telnyx error codes', function (string $errorCode) {
    $notifiable = Prospect::factory()->create();

    $phoneNumber = $notifiable->phoneNumbers()->create([
        'number' => '+13125000004',
        'can_receive_sms' => true,
    ]);

    $notifiable->primaryPhoneNumber()->associate($phoneNumber)->save();

    $settings = app()->make(TwilioSettings::class);
    $settings->provider = SmsMessagingProvider::Telnyx;
    $settings->telnyx_api_key = 'test_api_key';
    $settings->save();

    $failureResult = SmsChannelResultData::from(['success' => false]);
    $failureResult->error = 'Telnyx send failed';
    $failureResult->errorCode = $errorCode;

    $channel = mock(SmsChannel::class)
        ->shouldAllowMockingProtectedMethods()
        ->makePartial();
    $channel->shouldReceive('sendViaTelnyx')->andReturn($failureResult);

    app()->instance(SmsChannel::class, $channel);

    $notifiable->notify(new TestSmsNotification());

    assertDatabaseMissing(SmsOptOutPhoneNumber::class, ['number' => '+13125000004']);
    assertDatabaseMissing(BouncedPhoneNumber::class, ['number' => '+13125000004']);

    $event = SmsMessage::first()->events()->first();
    expect($event->type)->toBe(SmsMessageEventType::FailedDispatch)
        ->and($event->payload['error_code'])->toBe($errorCode);
})->with([
    '40301',
    '40302',
    '40304',
    '40306',
    '40307',
    '40308',
    '40309',
    '40311',
    '40313',
    '40315',
    '40316',
    '40318',
    '40319',
    '40320',
    '40321',
    '40322',
    '40325',
    '40328',
    '40329',
    '40330',
    '40331',
    '40333',
]);

it('short-circuits with BouncedSmsException before the API call when the number is already bounced', function () {
    $notifiable = Prospect::factory()->create();

    $phoneNumber = $notifiable->phoneNumbers()->create([
        'number' => '+13125000010',
        'can_receive_sms' => true,
    ]);

    $notifiable->primaryPhoneNumber()->associate($phoneNumber)->save();

    BouncedPhoneNumber::factory()->create([
        'number' => '+13125000010',
        'external_error_code' => '40001',
    ]);

    // sendViaTelnyx must never be called — if it were, the mock would fail the test
    $channel = mock(SmsChannel::class)
        ->shouldAllowMockingProtectedMethods()
        ->makePartial();
    $channel->shouldNotReceive('sendViaTelnyx');
    $channel->shouldNotReceive('sendViaTwilio');

    app()->instance(SmsChannel::class, $channel);

    expect(fn () => $notifiable->notify(new TestSmsNotification()))
        ->toThrow(BouncedSmsException::class);

    $event = SmsMessage::first()->events()->first();
    expect($event->type)->toBe(SmsMessageEventType::FailedDispatch)
        ->and($event->payload['error'])->toBe('Recipient phone number has previously bounced.');
});

it('short-circuits with SmsOptOutException before the API call when the number is already opted out', function () {
    $notifiable = Prospect::factory()->create();

    $phoneNumber = $notifiable->phoneNumbers()->create([
        'number' => '+13125000011',
        'can_receive_sms' => true,
    ]);

    $notifiable->primaryPhoneNumber()->associate($phoneNumber)->save();

    SmsOptOutPhoneNumber::factory()->create([
        'number' => '+13125000011',
    ]);

    // sendViaTelnyx must never be called — if it were, the mock would fail the test
    $channel = mock(SmsChannel::class)
        ->shouldAllowMockingProtectedMethods()
        ->makePartial();
    $channel->shouldNotReceive('sendViaTelnyx');
    $channel->shouldNotReceive('sendViaTwilio');

    app()->instance(SmsChannel::class, $channel);

    expect(fn () => $notifiable->notify(new TestSmsNotification()))
        ->toThrow(SmsOptOutException::class);

    $event = SmsMessage::first()->events()->first();
    expect($event->type)->toBe(SmsMessageEventType::FailedDispatch)
        ->and($event->payload['error'])->toBe('Recipient phone number has opted out of SMS messages.');
});
// TODO Add more tests for SMS Demo mode etc.
