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

use AdvisingApp\Engagement\Enums\EngagementDisplayStatus;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Notification\Enums\EmailMessageEventType;
use AdvisingApp\Notification\Models\EmailMessage;
use AdvisingApp\Notification\Models\EmailMessageEvent;

it('returns the correct case given a particular Engagement', function (Engagement $engagement, EngagementDisplayStatus $expectedStatus) {
    expect(EngagementDisplayStatus::getStatus($engagement))->toBe($expectedStatus);
})->with([
    'email | not scheduled' => [
        fn () => Engagement::factory()->email()->deliverNow()->create(),
        EngagementDisplayStatus::Pending,
    ],
    'email | scheduled' => [
        fn () => Engagement::factory()->email()->deliverLater()->create(),
        EngagementDisplayStatus::Scheduled,
    ],
    'email | scheduled, dispatched' => [
        fn () => Engagement::factory()
            ->has(
                EmailMessage::factory()
                    ->has(
                        EmailMessageEvent::factory()
                            ->state(['type' => EmailMessageEventType::Dispatched]),
                        'events'
                    ),
                'latestEmailMessage'
            )
            ->email()
            ->deliverLater()
            ->create(),
        EngagementDisplayStatus::Pending,
    ],
    'email | dispatched' => [
        fn () => Engagement::factory()
            ->has(
                EmailMessage::factory()
                    ->has(
                        EmailMessageEvent::factory()
                            ->state(['type' => EmailMessageEventType::Dispatched]),
                        'events'
                    ),
                'latestEmailMessage'
            )
            ->email()
            ->deliverNow()
            ->create(),
        EngagementDisplayStatus::Pending,
    ],
    'email | dispatched, send' => [
        fn () => Engagement::factory()
            ->has(
                EmailMessage::factory()
                    ->has(
                        EmailMessageEvent::factory()
                            ->count(2)
                            ->sequence(
                                [
                                    'type' => EmailMessageEventType::Dispatched,
                                ],
                                [
                                    'type' => EmailMessageEventType::Send,
                                ],
                            ),
                        'events'
                    ),
                relationship: 'emailMessages'
            )
            ->email()
            ->deliverNow()
            ->create(),
        EngagementDisplayStatus::Sent,
    ],
    'email | send, dispatched' => [
        fn () => Engagement::factory()
            ->has(
                EmailMessage::factory()
                    ->has(
                        EmailMessageEvent::factory()
                            ->count(2)
                            ->sequence(
                                [
                                    'type' => EmailMessageEventType::Send,
                                ],
                                [
                                    'type' => EmailMessageEventType::Dispatched,
                                ],
                            ),
                        'events'
                    ),
                relationship: 'emailMessages'
            )
            ->email()
            ->deliverNow()
            ->create(),
        EngagementDisplayStatus::Sent,
    ],
    'email | failedDispatch' => [
        fn () => Engagement::factory()
            ->has(
                EmailMessage::factory()
                    ->has(
                        EmailMessageEvent::factory()
                            ->state(['type' => EmailMessageEventType::FailedDispatch]),
                        'events'
                    ),
                'latestEmailMessage'
            )
            ->email()
            ->deliverNow()
            ->create(),
        EngagementDisplayStatus::Failed,
    ],
    'email | rateLimited' => [
        fn () => Engagement::factory()
            ->has(
                EmailMessage::factory()
                    ->has(
                        EmailMessageEvent::factory()
                            ->state(['type' => EmailMessageEventType::RateLimited]),
                        'events'
                    ),
                'latestEmailMessage'
            )
            ->email()
            ->deliverNow()
            ->create(),
        EngagementDisplayStatus::Failed,
    ],
    'email | blockedByDemoMode' => [
        fn () => Engagement::factory()
            ->has(
                EmailMessage::factory()
                    ->has(
                        EmailMessageEvent::factory()
                            ->state(['type' => EmailMessageEventType::BlockedByDemoMode]),
                        'events'
                    ),
                'latestEmailMessage'
            )
            ->email()
            ->deliverNow()
            ->create(),
        EngagementDisplayStatus::Delivered,
    ],
    'email | dispatched, send, bounce' => [
        fn () => Engagement::factory()
            ->has(
                EmailMessage::factory()
                    ->has(
                        EmailMessageEvent::factory()
                            ->count(3)
                            ->sequence(
                                ['type' => EmailMessageEventType::Dispatched],
                                ['type' => EmailMessageEventType::Send],
                                ['type' => EmailMessageEventType::Bounce],
                            ),
                        'events'
                    ),
                'latestEmailMessage'
            )
            ->email()
            ->deliverNow()
            ->create(),
        EngagementDisplayStatus::Bounced,
    ],
    'email | dispatched, send, delivery' => [
        fn () => Engagement::factory()
            ->has(
                EmailMessage::factory()
                    ->has(
                        EmailMessageEvent::factory()
                            ->count(3)
                            ->sequence(
                                ['type' => EmailMessageEventType::Dispatched],
                                ['type' => EmailMessageEventType::Send],
                                ['type' => EmailMessageEventType::Delivery],
                            ),
                        'events'
                    ),
                'latestEmailMessage'
            )
            ->email()
            ->deliverNow()
            ->create(),
        EngagementDisplayStatus::Delivered,
    ],
    'email | dispatched, send, delivery, complaint' => [
        fn () => Engagement::factory()
            ->has(
                EmailMessage::factory()
                    ->has(
                        EmailMessageEvent::factory()
                            ->count(4)
                            ->sequence(
                                ['type' => EmailMessageEventType::Dispatched],
                                ['type' => EmailMessageEventType::Send],
                                ['type' => EmailMessageEventType::Delivery],
                                ['type' => EmailMessageEventType::Complaint],
                            ),
                        'events'
                    ),
                'latestEmailMessage'
            )
            ->email()
            ->deliverNow()
            ->create(),
        EngagementDisplayStatus::Complaint,
    ],
    'email | dispatched, send, reject' => [
        fn () => Engagement::factory()
            ->has(
                EmailMessage::factory()
                    ->has(
                        EmailMessageEvent::factory()
                            ->count(3)
                            ->sequence(
                                ['type' => EmailMessageEventType::Dispatched],
                                ['type' => EmailMessageEventType::Send],
                                ['type' => EmailMessageEventType::Reject],
                            ),
                        'events'
                    ),
                'latestEmailMessage'
            )
            ->email()
            ->deliverNow()
            ->create(),
        EngagementDisplayStatus::Failed,
    ],
    'email | dispatched, send, delivery, open' => [
        fn () => Engagement::factory()
            ->has(
                EmailMessage::factory()
                    ->has(
                        EmailMessageEvent::factory()
                            ->count(4)
                            ->sequence(
                                ['type' => EmailMessageEventType::Dispatched],
                                ['type' => EmailMessageEventType::Send],
                                ['type' => EmailMessageEventType::Delivery],
                                ['type' => EmailMessageEventType::Open],
                            ),
                        'events'
                    ),
                'latestEmailMessage'
            )
            ->email()
            ->deliverNow()
            ->create(),
        EngagementDisplayStatus::Read,
    ],
    'email | dispatched, send, delivery, open, click' => [
        fn () => Engagement::factory()
            ->has(
                EmailMessage::factory()
                    ->has(
                        EmailMessageEvent::factory()
                            ->count(5)
                            ->sequence(
                                ['type' => EmailMessageEventType::Dispatched],
                                ['type' => EmailMessageEventType::Send],
                                ['type' => EmailMessageEventType::Delivery],
                                ['type' => EmailMessageEventType::Open],
                                ['type' => EmailMessageEventType::Click],
                            ),
                        'events'
                    ),
                'latestEmailMessage'
            )
            ->email()
            ->deliverNow()
            ->create(),
        EngagementDisplayStatus::Clicked,
    ],
    'email | dispatched, send, delivery, open, click, open' => [
        fn () => Engagement::factory()
            ->has(
                EmailMessage::factory()
                    ->has(
                        EmailMessageEvent::factory()
                            ->count(6)
                            ->sequence(
                                ['type' => EmailMessageEventType::Dispatched],
                                ['type' => EmailMessageEventType::Send],
                                ['type' => EmailMessageEventType::Delivery],
                                ['type' => EmailMessageEventType::Open],
                                ['type' => EmailMessageEventType::Click],
                                ['type' => EmailMessageEventType::Open],
                            ),
                        'events'
                    ),
                'latestEmailMessage'
            )
            ->email()
            ->deliverNow()
            ->create(),
        EngagementDisplayStatus::Clicked,
    ],
]);
