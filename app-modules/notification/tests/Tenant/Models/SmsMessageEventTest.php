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

use AdvisingApp\Notification\Models\SmsMessage;
use AdvisingApp\Notification\Models\SmsMessageEvent;
use Illuminate\Support\Facades\DB;

it('stores occurred_at with microsecond precision', function (): void {
    $message = SmsMessage::factory()->create();

    $event = $message->events()->save(SmsMessageEvent::factory()->make(['occurred_at' => '2026-01-01 10:00:00.123456']));

    expect($event->fresh()?->occurred_at->format('Y-m-d H:i:s.u'))->toBe('2026-01-01 10:00:00.123456');
});

it('reads a whole-second occurred_at', function (): void {
    $message = SmsMessage::factory()->create();

    $event = $message->events()->save(SmsMessageEvent::factory()->make());

    // Postgres returns no fractional part when the microseconds are zero.
    DB::table('sms_message_events')->where('id', $event->getKey())->update(['occurred_at' => '2026-01-01 10:00:00']);

    expect($event->fresh()?->occurred_at->format('Y-m-d H:i:s.u'))->toBe('2026-01-01 10:00:00.000000');
});
