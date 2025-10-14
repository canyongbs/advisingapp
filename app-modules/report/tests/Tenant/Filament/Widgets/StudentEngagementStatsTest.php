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

use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Report\Filament\Widgets\StudentEngagementStats;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;

it('returns correct counts of students, emails, texts, and staff engagements within the given date range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $student1 = Student::factory()->state(['created_at_source' => $startDate])->create();
    $student2 = Student::factory()->state(['created_at_source' => $endDate])->create();

    $emailCount = 2;
    $textCount = 3;

    $user1 = User::factory()->create();
    Engagement::factory()->count($emailCount)->state([
        'user_id' => $user1->getKey(),
        'recipient_id' => $student1->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
        'channel' => NotificationChannel::Email,
        'created_at' => $startDate,
    ])->create();

    $user2 = User::factory()->create();
    Engagement::factory()->count($textCount)->state([
        'user_id' => $user2->getKey(),
        'recipient_id' => $student2->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
        'channel' => NotificationChannel::Sms,
        'created_at' => $endDate,
    ])->create();

    $widget = new StudentEngagementStats();
    $widget->cacheTag = 'report-student-engagement';
    $widget->pageFilters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $stats = $widget->getStats();

    expect($stats[0]->getValue())->toEqual(2)
        ->and($stats[1]->getValue())->toEqual($emailCount)
        ->and($stats[2]->getValue())->toEqual($textCount)
        ->and($stats[3]->getValue())->toEqual(2);
});

it('returns correct counts of students, emails, texts, and staff engagements based on segment filters', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $segment = Group::factory()->create([
        'model' => GroupModel::Student,
        'filters' => [
            'queryBuilder' => [
                'rules' => [
                    'C0Cy' => [
                        'type' => 'last',
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

    $studentOne = Student::factory()->state(['created_at_source' => $startDate, 'last' => 'John'])->create();
    $studentTwo = Student::factory()->state(['created_at_source' => $endDate, 'last' => 'John'])->create();
    $studentThree = Student::factory()->state(['created_at_source' => $startDate, 'last' => 'Doe'])->create();
    $studentFour = Student::factory()->state(['created_at_source' => $endDate, 'last' => 'Doe'])->create();

    $emailCountForJohnName = 2;
    $textCountForJohnName = 3;
    $emailCountForDoeName = 2;
    $textCountForDoeName = 3;

    $user1 = User::factory()->create();
    Engagement::factory()->count($emailCountForJohnName)->state([
        'user_id' => $user1->getKey(),
        'recipient_id' => $studentOne->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
        'channel' => NotificationChannel::Email,
        'created_at' => $startDate,
    ])->create();

    $user2 = User::factory()->create();
    Engagement::factory()->count($textCountForJohnName)->state([
        'user_id' => $user2->getKey(),
        'recipient_id' => $studentTwo->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
        'channel' => NotificationChannel::Sms,
        'created_at' => $endDate,
    ])->create();

    $user3 = User::factory()->create();
    Engagement::factory()->count($emailCountForDoeName)->state([
        'user_id' => $user3->getKey(),
        'recipient_id' => $studentThree->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
        'channel' => NotificationChannel::Email,
        'created_at' => $startDate,
    ])->create();

    $user4 = User::factory()->create();
    Engagement::factory()->count($textCountForDoeName)->state([
        'user_id' => $user4->getKey(),
        'recipient_id' => $studentFour->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
        'channel' => NotificationChannel::Sms,
        'created_at' => $endDate,
    ])->create();

    $widget = new StudentEngagementStats();
    $widget->cacheTag = 'report-student-engagement';
    $widget->pageFilters = [
        'populationSegment' => $segment->getKey(),
    ];

    $stats = $widget->getStats();

    expect($stats[0]->getValue())->toEqual(2)
        ->and($stats[1]->getValue())->toEqual($emailCountForJohnName)
        ->and($stats[2]->getValue())->toEqual($textCountForJohnName)
        ->and($stats[3]->getValue())->toEqual(2);

    // without filter
    $widget = new StudentEngagementStats();
    $widget->cacheTag = 'report-student-engagement';
    $widget->pageFilters = [];

    $stats = $widget->getStats();

    expect($stats[0]->getValue())->toEqual(4)
        ->and($stats[1]->getValue())->toEqual($emailCountForJohnName + $emailCountForDoeName)
        ->and($stats[2]->getValue())->toEqual($textCountForJohnName + $textCountForDoeName)
        ->and($stats[3]->getValue())->toEqual(4);
});
