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

/**
 * Tied events are inserted in the given order so that insertion order alone can't satisfy the expected sort.
 *
 * @param array<int, string> $tiedIdsInInsertionOrder
 *
 * @return array{message: SmsMessage, early: SmsMessageEvent}
 */
$createSmsMessageEventsForBuilderOrdering = function (array $tiedIdsInInsertionOrder): array {
    $message = SmsMessage::factory()->create();
    $early = $message->events()->save(SmsMessageEvent::factory()->make(['occurred_at' => '2026-01-01 09:00:00']));

    foreach ($tiedIdsInInsertionOrder as $id) {
        $message->events()->save(SmsMessageEvent::factory()->make(['id' => $id, 'occurred_at' => '2026-01-01 10:00:00']));
    }

    return ['message' => $message, 'early' => $early];
};

it('orders events by occurred_at then id ascending', function () use ($createSmsMessageEventsForBuilderOrdering) {
    ['early' => $early] = $createSmsMessageEventsForBuilderOrdering(['ffffffff-ffff-4fff-bfff-ffffffffffff', '00000000-0000-4000-8000-000000000000']);

    expect(SmsMessageEvent::query()->orderByOccurrence()->pluck('id')->all())
        ->toBe([$early->getKey(), '00000000-0000-4000-8000-000000000000', 'ffffffff-ffff-4fff-bfff-ffffffffffff']);
});

it('orders events by occurred_at then id descending', function () use ($createSmsMessageEventsForBuilderOrdering) {
    ['early' => $early] = $createSmsMessageEventsForBuilderOrdering(['00000000-0000-4000-8000-000000000000', 'ffffffff-ffff-4fff-bfff-ffffffffffff']);

    $expected = ['ffffffff-ffff-4fff-bfff-ffffffffffff', '00000000-0000-4000-8000-000000000000', $early->getKey()];

    expect(SmsMessageEvent::query()->orderByOccurrence('desc')->pluck('id')->all())->toBe($expected)
        ->and(SmsMessageEvent::query()->orderByOccurrence(SortDirection::Descending)->pluck('id')->all())->toBe($expected)
        ->and(SmsMessageEvent::query()->orderByOccurrenceDesc()->pluck('id')->all())->toBe($expected);
});

it('orders events through the message relation', function () use ($createSmsMessageEventsForBuilderOrdering) {
    ['message' => $message, 'early' => $early] = $createSmsMessageEventsForBuilderOrdering(['00000000-0000-4000-8000-000000000000', 'ffffffff-ffff-4fff-bfff-ffffffffffff']);

    expect($message->events()->orderByOccurrenceDesc()->pluck('id')->all())
        ->toBe(['ffffffff-ffff-4fff-bfff-ffffffffffff', '00000000-0000-4000-8000-000000000000', $early->getKey()]);
});
