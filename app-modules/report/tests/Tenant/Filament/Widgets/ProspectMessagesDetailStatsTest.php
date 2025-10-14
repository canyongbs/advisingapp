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

use AdvisingApp\Engagement\Enums\EngagementResponseType;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Group\Enums\SegmentModel;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Filament\Widgets\ProspectMessagesDetailStats;

it('returns correct counts of emails and SMS sent/received with no filters', function () {
    $prospectOne = Prospect::factory()->create();
    $prospectTwo = Prospect::factory()->create();

    $emailsSentCount = 2;
    $emailsReceivedCount = 3;
    $smsSentCount = 4;
    $smsReceivedCount = 5;

    // Create sent emails
    Engagement::factory()->count($emailsSentCount)->state([
        'recipient_id' => $prospectOne->id,
        'recipient_type' => (new Prospect())->getMorphClass(),
        'channel' => NotificationChannel::Email,
    ])->create();

    // Create received emails
    EngagementResponse::factory()->count($emailsReceivedCount)->state([
        'sender_id' => $prospectTwo->id,
        'sender_type' => (new Prospect())->getMorphClass(),
        'type' => EngagementResponseType::Email,
    ])->create();

    // Create sent SMS
    Engagement::factory()->count($smsSentCount)->state([
        'recipient_id' => $prospectOne->id,
        'recipient_type' => (new Prospect())->getMorphClass(),
        'channel' => NotificationChannel::Sms,
    ])->create();

    // Create received SMS
    EngagementResponse::factory()->count($smsReceivedCount)->state([
        'sender_id' => $prospectTwo->id,
        'sender_type' => (new Prospect())->getMorphClass(),
        'type' => EngagementResponseType::Sms,
    ])->create();

    $widget = new ProspectMessagesDetailStats();
    $widget->cacheTag = 'report-prospect-messages-detail';
    $widget->pageFilters = [];

    $stats = $widget->getStats();

    expect($stats[0]->getValue())->toEqual($emailsSentCount)
        ->and($stats[1]->getValue())->toEqual($emailsReceivedCount)
        ->and($stats[2]->getValue())->toEqual($smsSentCount)
        ->and($stats[3]->getValue())->toEqual($smsReceivedCount);
});

it('returns correct counts of emails and SMS sent/received within the given date range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $prospectOne = Prospect::factory()->create();
    $prospectTwo = Prospect::factory()->create();

    $emailsSentCount = 2;
    $emailsReceivedCount = 3;
    $smsSentCount = 4;
    $smsReceivedCount = 5;

    // Create sent emails within date range
    Engagement::factory()->count($emailsSentCount)->state([
        'recipient_id' => $prospectOne->id,
        'recipient_type' => (new Prospect())->getMorphClass(),
        'channel' => NotificationChannel::Email,
        'dispatched_at' => $startDate,
    ])->create();

    // Create received emails within date range
    EngagementResponse::factory()->count($emailsReceivedCount)->state([
        'sender_id' => $prospectTwo->id,
        'sender_type' => (new Prospect())->getMorphClass(),
        'type' => EngagementResponseType::Email,
        'sent_at' => $endDate,
    ])->create();

    // Create sent SMS within date range
    Engagement::factory()->count($smsSentCount)->state([
        'recipient_id' => $prospectOne->id,
        'recipient_type' => (new Prospect())->getMorphClass(),
        'channel' => NotificationChannel::Sms,
        'dispatched_at' => $startDate,
    ])->create();

    // Create received SMS within date range
    EngagementResponse::factory()->count($smsReceivedCount)->state([
        'sender_id' => $prospectTwo->id,
        'sender_type' => (new Prospect())->getMorphClass(),
        'type' => EngagementResponseType::Sms,
        'sent_at' => $endDate,
    ])->create();

    $widget = new ProspectMessagesDetailStats();
    $widget->cacheTag = 'report-prospect-messages-detail';
    $widget->pageFilters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $stats = $widget->getStats();

    expect($stats[0]->getValue())->toEqual($emailsSentCount)
        ->and($stats[1]->getValue())->toEqual($emailsReceivedCount)
        ->and($stats[2]->getValue())->toEqual($smsSentCount)
        ->and($stats[3]->getValue())->toEqual($smsReceivedCount);
});

it('returns correct counts of emails and SMS sent/received based on segment filters', function () {
    $segment = Group::factory()->create([
        'model' => SegmentModel::Prospect,
        'filters' => [
            'queryBuilder' => [
                'rules' => [
                    'C0Cy' => [
                        'type' => 'last_name',
                        'data' => [
                            'operator' => 'contains',
                            'settings' => [
                                'text' => 'John',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ]);

    $prospectOne = Prospect::factory()->state(['last_name' => 'John'])->create();
    $prospectTwo = Prospect::factory()->state(['last_name' => 'John'])->create();
    $prospectThree = Prospect::factory()->state(['last_name' => 'Doe'])->create();
    $prospectFour = Prospect::factory()->state(['last_name' => 'Doe'])->create();

    $emailsSentForJohn = 2;
    $emailsReceivedForJohn = 3;
    $smsSentForJohn = 4;
    $smsReceivedForJohn = 5;
    $emailsSentForDoe = 1;
    $emailsReceivedForDoe = 2;
    $smsSentForDoe = 3;
    $smsReceivedForDoe = 4;

    // Create sent emails for John prospects
    Engagement::factory()->count($emailsSentForJohn)->state([
        'recipient_id' => $prospectOne->id,
        'recipient_type' => (new Prospect())->getMorphClass(),
        'channel' => NotificationChannel::Email,
    ])->create();

    // Create received emails for John prospects
    EngagementResponse::factory()->count($emailsReceivedForJohn)->state([
        'sender_id' => $prospectTwo->id,
        'sender_type' => (new Prospect())->getMorphClass(),
        'type' => EngagementResponseType::Email,
    ])->create();

    // Create sent SMS for John prospects
    Engagement::factory()->count($smsSentForJohn)->state([
        'recipient_id' => $prospectOne->id,
        'recipient_type' => (new Prospect())->getMorphClass(),
        'channel' => NotificationChannel::Sms,
    ])->create();

    // Create received SMS for John prospects
    EngagementResponse::factory()->count($smsReceivedForJohn)->state([
        'sender_id' => $prospectTwo->id,
        'sender_type' => (new Prospect())->getMorphClass(),
        'type' => EngagementResponseType::Sms,
    ])->create();

    // Create sent emails for Doe prospects
    Engagement::factory()->count($emailsSentForDoe)->state([
        'recipient_id' => $prospectThree->id,
        'recipient_type' => (new Prospect())->getMorphClass(),
        'channel' => NotificationChannel::Email,
    ])->create();

    // Create received emails for Doe prospects
    EngagementResponse::factory()->count($emailsReceivedForDoe)->state([
        'sender_id' => $prospectFour->id,
        'sender_type' => (new Prospect())->getMorphClass(),
        'type' => EngagementResponseType::Email,
    ])->create();

    // Create sent SMS for Doe prospects
    Engagement::factory()->count($smsSentForDoe)->state([
        'recipient_id' => $prospectThree->id,
        'recipient_type' => (new Prospect())->getMorphClass(),
        'channel' => NotificationChannel::Sms,
    ])->create();

    // Create received SMS for Doe prospects
    EngagementResponse::factory()->count($smsReceivedForDoe)->state([
        'sender_id' => $prospectFour->id,
        'sender_type' => (new Prospect())->getMorphClass(),
        'type' => EngagementResponseType::Sms,
    ])->create();

    $widget = new ProspectMessagesDetailStats();
    $widget->cacheTag = 'report-prospect-messages-detail';
    $widget->pageFilters = [
        'populationSegment' => $segment->getKey(),
    ];

    $stats = $widget->getStats();

    expect($stats[0]->getValue())->toEqual($emailsSentForJohn)
        ->and($stats[1]->getValue())->toEqual($emailsReceivedForJohn)
        ->and($stats[2]->getValue())->toEqual($smsSentForJohn)
        ->and($stats[3]->getValue())->toEqual($smsReceivedForJohn);

    // Without filter
    $widget = new ProspectMessagesDetailStats();
    $widget->cacheTag = 'report-prospect-messages-detail';
    $widget->pageFilters = [];

    $stats = $widget->getStats();

    expect($stats[0]->getValue())->toEqual($emailsSentForJohn + $emailsSentForDoe)
        ->and($stats[1]->getValue())->toEqual($emailsReceivedForJohn + $emailsReceivedForDoe)
        ->and($stats[2]->getValue())->toEqual($smsSentForJohn + $smsSentForDoe)
        ->and($stats[3]->getValue())->toEqual($smsReceivedForJohn + $smsReceivedForDoe);
});
