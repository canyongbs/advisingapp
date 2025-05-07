<?php

use AdvisingApp\Engagement\Enums\EngagementDisplayStatus;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Notification\Enums\EmailMessageEventType;
use AdvisingApp\Notification\Models\EmailMessage;
use AdvisingApp\Notification\Models\EmailMessageEvent;

it('returns the correct case given a particular Engagement', function (Engagement $engagement, EngagementDisplayStatus $expectedStatus) {
    expect(EngagementDisplayStatus::getStatus($engagement))->toBe($expectedStatus);
})->with([
    'email, not scheduled' => [
        fn () => Engagement::factory()->email()->deliverNow()->create(),
        EngagementDisplayStatus::Pending,
    ],
    'email, scheduled' => [
        fn () => Engagement::factory()->email()->deliverLater()->create(),
        EngagementDisplayStatus::Scheduled,
    ],
    'email, dispatched' => [
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
]);
