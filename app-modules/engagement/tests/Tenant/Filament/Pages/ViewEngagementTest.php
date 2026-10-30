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

use AdvisingApp\Engagement\Filament\Pages\ViewEngagement;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Notification\Enums\EmailMessageEventType;
use AdvisingApp\Notification\Enums\SmsMessageEventType;
use AdvisingApp\Notification\Models\EmailMessage;
use AdvisingApp\Notification\Models\EmailMessageEvent;
use AdvisingApp\Notification\Models\SmsMessage;
use AdvisingApp\Notification\Models\SmsMessageEvent;
use Livewire\Livewire;

use function Pest\Laravel\get;
use function Tests\asSuperAdmin;
use function Tests\setUnifiedInboxEnabled;

it('requires the unified inbox feature addon to access', function (): void {
    asSuperAdmin();

    $engagement = Engagement::factory()->create();

    setUnifiedInboxEnabled(false);

    get(ViewEngagement::getUrl([$engagement]))->assertForbidden();

    setUnifiedInboxEnabled(true);

    get(ViewEngagement::getUrl([$engagement]))->assertOk();
});

it('lists email events newest first, breaking ties by id', function (): void {
    asSuperAdmin();
    setUnifiedInboxEnabled(true);

    $engagement = Engagement::factory()->email()->create();

    $message = EmailMessage::factory()->for($engagement, 'related')->create();

    // Inserted in the opposite order to the expected one, so insertion order can't satisfy the test.
    $message->events()->save(EmailMessageEvent::factory()->make([
        'id' => '00000000-0000-4000-8000-000000000000',
        'type' => EmailMessageEventType::Send,
        'occurred_at' => '2025-03-14 10:15:00',
    ]));
    $message->events()->save(EmailMessageEvent::factory()->make([
        'id' => 'ffffffff-ffff-4fff-bfff-ffffffffffff',
        'type' => EmailMessageEventType::Delivery,
        'occurred_at' => '2025-03-14 10:15:00',
    ]));

    Livewire::test(ViewEngagement::class, [
        'record' => $engagement,
    ])
        ->assertSchemaComponentStateSet('message_events.0.type', 'Delivery', 'infolist')
        ->assertSchemaComponentStateSet('message_events.1.type', 'Send', 'infolist');
});

it('lists SMS events newest first, breaking ties by id', function (): void {
    asSuperAdmin();
    setUnifiedInboxEnabled(true);

    $engagement = Engagement::factory()->sms()->create();

    $message = SmsMessage::factory()->for($engagement, 'related')->create();

    // Inserted in the opposite order to the expected one, so insertion order can't satisfy the test.
    $message->events()->save(SmsMessageEvent::factory()->make([
        'id' => '00000000-0000-4000-8000-000000000000',
        'type' => SmsMessageEventType::Sent,
        'occurred_at' => '2025-03-14 10:15:00',
    ]));
    $message->events()->save(SmsMessageEvent::factory()->make([
        'id' => 'ffffffff-ffff-4fff-bfff-ffffffffffff',
        'type' => SmsMessageEventType::Delivered,
        'occurred_at' => '2025-03-14 10:15:00',
    ]));

    Livewire::test(ViewEngagement::class, [
        'record' => $engagement,
    ])
        ->assertSchemaComponentStateSet('message_events.0.type', 'Delivered', 'infolist')
        ->assertSchemaComponentStateSet('message_events.1.type', 'Sent', 'infolist');
});
