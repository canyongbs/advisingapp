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
use AdvisingApp\Campaign\Models\Campaign;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Engagement\Enums\EngagementResponseType;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Engagement\Models\HolisticEngagement;
use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Report\Filament\Widgets\StudentMessagesDetailTable;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use Filament\Actions\ExportAction;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('displays properly with no filters', function () {
    $student = Student::factory()->create();

    $engagement = Engagement::factory()->create([
        'recipient_id' => $student->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
    ]);

    $engagementResponse = EngagementResponse::factory()->create([
        'sender_id' => $student->sisid,
        'sender_type' => (new Student())->getMorphClass(),
    ]);

    $holisticEngagementOutbound = HolisticEngagement::where('record_id', $engagement->id)->where('record_type', new Engagement()->getMorphClass())->first();
    $holisticEngagementInbound = HolisticEngagement::where('record_id', $engagementResponse->id)->where('record_type', new EngagementResponse()->getMorphClass())->first();

    livewire(StudentMessagesDetailTable::class, [
        'cacheTag' => 'report-student-messages',
        'filters' => [],
    ])
        ->assertCanSeeTableRecords(collect([
            $holisticEngagementOutbound,
            $holisticEngagementInbound,
        ]));
});

it('displays engagements and responses within the given date range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $studentOne = Student::factory()->create();
    $studentTwo = Student::factory()->create();

    $engagementInRange = Engagement::factory()->create([
        'recipient_id' => $studentOne->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
        'dispatched_at' => $startDate,
    ]);

    $engagementResponseInRange = EngagementResponse::factory()->create([
        'sender_id' => $studentTwo->sisid,
        'sender_type' => (new Student())->getMorphClass(),
        'sent_at' => $endDate,
    ]);

    $engagementOutOfRange = Engagement::factory()->create([
        'recipient_id' => $studentOne->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
        'dispatched_at' => now()->subDays(20),
    ]);

    $holisticEngagementInRangeOutbound = HolisticEngagement::where('record_id', $engagementInRange->id)->where('record_type', new Engagement()->getMorphClass())->first();
    $holisticEngagementInRangeInbound = HolisticEngagement::where('record_id', $engagementResponseInRange->id)->where('record_type', new EngagementResponse()->getMorphClass())->first();
    $holisticEngagementOutOfRange = HolisticEngagement::where('record_id', $engagementOutOfRange->id)->where('record_type', new Engagement()->getMorphClass())->first();

    $filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    livewire(StudentMessagesDetailTable::class, [
        'cacheTag' => 'report-student-messages',
        'pageFilters' => $filters,
    ])
        ->assertCanSeeTableRecords(collect([
            $holisticEngagementInRangeOutbound,
            $holisticEngagementInRangeInbound,
        ]))
        ->assertCanNotSeeTableRecords(collect([$holisticEngagementOutOfRange]));
});

it('displays engagements and responses based on segment filters', function () {
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

    $studentJohn = Student::factory()->state(['last' => 'John'])->create();
    $studentDoe = Student::factory()->state(['last' => 'Doe'])->create();

    $engagementJohn = Engagement::factory()->create([
        'recipient_id' => $studentJohn->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
    ]);

    $engagementResponseJohn = EngagementResponse::factory()->create([
        'sender_id' => $studentJohn->sisid,
        'sender_type' => (new Student())->getMorphClass(),
    ]);

    $engagementDoe = Engagement::factory()->create([
        'recipient_id' => $studentDoe->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
    ]);

    $holisticEngagementJohnOutbound = HolisticEngagement::where('record_id', $engagementJohn->id)->where('record_type', new Engagement()->getMorphClass())->first();
    $holisticEngagementJohnInbound = HolisticEngagement::where('record_id', $engagementResponseJohn->id)->where('record_type', new EngagementResponse()->getMorphClass())->first();
    $holisticEngagementDoe = HolisticEngagement::where('record_id', $engagementDoe->id)->where('record_type', new Engagement()->getMorphClass())->first();

    $filters = [
        'populationSegment' => $segment->getKey(),
    ];

    livewire(StudentMessagesDetailTable::class, [
        'cacheTag' => 'report-student-messages',
        'pageFilters' => $filters,
    ])
        ->assertCanSeeTableRecords(collect([
            $holisticEngagementJohnOutbound,
            $holisticEngagementJohnInbound,
        ]))
        ->assertCanNotSeeTableRecords(collect([$holisticEngagementDoe]));

    // Without filter
    livewire(StudentMessagesDetailTable::class, [
        'cacheTag' => 'report-student-messages',
        'filters' => [],
    ])
        ->assertCanSeeTableRecords(collect([
            $holisticEngagementJohnOutbound,
            $holisticEngagementJohnInbound,
            $holisticEngagementDoe,
        ]));
});

it('ensures direction is set properly for engagements and responses', function () {
    $student = Student::factory()->create();

    $engagement = Engagement::factory()->create([
        'recipient_id' => $student->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
    ]);

    $engagementResponse = EngagementResponse::factory()->create([
        'sender_id' => $student->sisid,
        'sender_type' => (new Student())->getMorphClass(),
    ]);

    $holisticEngagementOutbound = HolisticEngagement::where('record_id', $engagement->id)->where('record_type', new Engagement()->getMorphClass())->first();
    $holisticEngagementInbound = HolisticEngagement::where('record_id', $engagementResponse->id)->where('record_type', new EngagementResponse()->getMorphClass())->first();

    livewire(StudentMessagesDetailTable::class, [
        'cacheTag' => 'report-student-messages',
        'filters' => [],
    ])
        ->assertTableColumnStateSet('direction', 'outbound', record: $holisticEngagementOutbound)
        ->assertTableColumnStateSet('direction', 'inbound', record: $holisticEngagementInbound);
});

it('ensures status is set properly for engagements and responses', function () {
    $student = Student::factory()->create();

    $engagement = Engagement::factory()->create([
        'recipient_id' => $student->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
    ]);

    $engagementResponse = EngagementResponse::factory()->create([
        'sender_id' => $student->sisid,
        'sender_type' => (new Student())->getMorphClass(),
    ]);

    $holisticEngagementOutbound = HolisticEngagement::where('record_id', $engagement->id)->where('record_type', new Engagement()->getMorphClass())->first();
    $holisticEngagementInbound = HolisticEngagement::where('record_id', $engagementResponse->id)->where('record_type', new EngagementResponse()->getMorphClass())->first();

    livewire(StudentMessagesDetailTable::class, [
        'cacheTag' => 'report-student-messages',
        'filters' => [],
    ])
        ->assertTableColumnFormattedStateSet('status', 'Scheduled', record: $holisticEngagementOutbound)
        ->assertTableColumnFormattedStateSet('status', 'New', record: $holisticEngagementInbound);
});

it('ensures sent_by is properly rendered in the table', function () {
    $user = User::factory()->create();
    $student = Student::factory()->create();

    $engagement = Engagement::factory()->create([
        'user_id' => $user->id,
        'recipient_id' => $student->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
    ]);

    $engagementResponse = EngagementResponse::factory()->create([
        'sender_id' => $student->sisid,
        'sender_type' => (new Student())->getMorphClass(),
    ]);

    $holisticEngagementOutbound = HolisticEngagement::where('record_id', $engagement->id)->where('record_type', new Engagement()->getMorphClass())->first();
    $holisticEngagementInbound = HolisticEngagement::where('record_id', $engagementResponse->id)->where('record_type', new EngagementResponse()->getMorphClass())->first();

    livewire(StudentMessagesDetailTable::class, [
        'cacheTag' => 'report-student-messages',
        'filters' => [],
    ])
        ->assertTableColumnStateSet('sent_by', $user->name, $holisticEngagementOutbound)
        ->assertTableColumnStateSet('sent_by', $student->full_name, $holisticEngagementInbound);
});

it('ensures sent_to is properly rendered in the table', function () {
    $student = Student::factory()->create();

    $engagement = Engagement::factory()->create([
        'recipient_id' => $student->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
    ]);

    $engagementResponse = EngagementResponse::factory()->create([
        'sender_id' => $student->sisid,
        'sender_type' => (new Student())->getMorphClass(),
    ]);

    $holisticEngagementOutbound = HolisticEngagement::where('record_id', $engagement->id)->where('record_type', new Engagement()->getMorphClass())->first();
    $holisticEngagementInbound = HolisticEngagement::where('record_id', $engagementResponse->id)->where('record_type', new EngagementResponse()->getMorphClass())->first();

    livewire(StudentMessagesDetailTable::class, [
        'cacheTag' => 'report-student-messages',
        'filters' => [],
    ])
        ->assertTableColumnStateSet('sent_to', $student->full_name, $holisticEngagementOutbound)
        ->assertTableColumnStateSet('sent_to', 'N/A', $holisticEngagementInbound);
});

it('ensures type is properly rendered in the table', function () {
    $student = Student::factory()->create();

    $engagement = Engagement::factory()->create([
        'recipient_id' => $student->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
        'channel' => NotificationChannel::Email,
    ]);

    $engagementResponse = EngagementResponse::factory()->create([
        'sender_id' => $student->sisid,
        'sender_type' => (new Student())->getMorphClass(),
        'type' => EngagementResponseType::Sms,
    ]);

    $holisticEngagementOutbound = HolisticEngagement::where('record_id', $engagement->id)->where('record_type', new Engagement()->getMorphClass())->first();
    $holisticEngagementInbound = HolisticEngagement::where('record_id', $engagementResponse->id)->where('record_type', new EngagementResponse()->getMorphClass())->first();

    livewire(StudentMessagesDetailTable::class, [
        'cacheTag' => 'report-student-messages',
        'filters' => [],
    ])
        ->assertTableColumnStateSet('type', 'email', $holisticEngagementOutbound)
        ->assertTableColumnStateSet('type', 'sms', $holisticEngagementInbound);
});

it('ensures details are properly rendered in the table', function () {
    $student = Student::factory()->create();

    $engagementEmail = Engagement::factory()->create([
        'recipient_id' => $student->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
        'channel' => NotificationChannel::Email,
    ]);

    $engagementSms = Engagement::factory()->create([
        'recipient_id' => $student->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
        'channel' => NotificationChannel::Sms,
    ]);

    $responseEmail = EngagementResponse::factory()->create([
        'sender_id' => $student->sisid,
        'sender_type' => (new Student())->getMorphClass(),
        'type' => EngagementResponseType::Email,
    ]);

    $responseSms = EngagementResponse::factory()->create([
        'sender_id' => $student->sisid,
        'sender_type' => (new Student())->getMorphClass(),
        'type' => EngagementResponseType::Sms,
    ]);

    $holisticEngagementEmail = HolisticEngagement::where('record_id', $engagementEmail->id)->where('record_type', new Engagement()->getMorphClass())->first();
    $holisticEngagementSms = HolisticEngagement::where('record_id', $engagementSms->id)->where('record_type', new Engagement()->getMorphClass())->first();
    $holisticResponseEmail = HolisticEngagement::where('record_id', $responseEmail->id)->where('record_type', new EngagementResponse()->getMorphClass())->first();
    $holisticResponseSms = HolisticEngagement::where('record_id', $responseSms->id)->where('record_type', new EngagementResponse()->getMorphClass())->first();

    livewire(StudentMessagesDetailTable::class, [
        'cacheTag' => 'report-student-messages',
        'filters' => [],
    ])
        ->assertTableColumnStateSet('details', Str::limit($engagementEmail->getSubjectMarkdown(), 50), $holisticEngagementEmail)
        ->assertTableColumnStateSet('details', Str::limit($engagementSms->getBodyMarkdown(), 50), $holisticEngagementSms)
        ->assertTableColumnStateSet('details', Str::limit($responseEmail->subject, 50), $holisticResponseEmail)
        ->assertTableColumnStateSet('details', Str::limit($responseSms->getBodyMarkdown(), 50), $holisticResponseSms);
});

it('ensures campaign is properly rendered in the table', function () {
    $student = Student::factory()->create();

    $campaign = Campaign::factory()->create();
    $campaignAction = CampaignAction::factory()->create(['campaign_id' => $campaign->id]);

    $engagementWithCampaign = Engagement::factory()->create([
        'recipient_id' => $student->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
        'campaign_action_id' => $campaignAction->id,
    ]);

    $engagementWithoutCampaign = Engagement::factory()->create([
        'recipient_id' => $student->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
    ]);

    $engagementResponse = EngagementResponse::factory()->create([
        'sender_id' => $student->sisid,
        'sender_type' => (new Student())->getMorphClass(),
    ]);

    $holisticEngagementWithCampaign = HolisticEngagement::where('record_id', $engagementWithCampaign->id)->where('record_type', new Engagement()->getMorphClass())->first();
    $holisticEngagementWithoutCampaign = HolisticEngagement::where('record_id', $engagementWithoutCampaign->id)->where('record_type', new Engagement()->getMorphClass())->first();
    $holisticEngagementResponse = HolisticEngagement::where('record_id', $engagementResponse->id)->where('record_type', new EngagementResponse()->getMorphClass())->first();

    livewire(StudentMessagesDetailTable::class, [
        'cacheTag' => 'report-student-messages',
        'filters' => [],
    ])
        ->assertTableColumnStateSet('campaign', $campaign->name, $holisticEngagementWithCampaign)
        ->assertTableColumnStateSet('campaign', 'N/A', $holisticEngagementWithoutCampaign)
        ->assertTableColumnStateSet('campaign', 'N/A', $holisticEngagementResponse);
});

it('filters by direction properly', function () {
    $student = Student::factory()->create();

    $engagement = Engagement::factory()->create([
        'recipient_id' => $student->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
    ]);

    $engagementResponse = EngagementResponse::factory()->create([
        'sender_id' => $student->sisid,
        'sender_type' => (new Student())->getMorphClass(),
    ]);

    $holisticEngagementOutbound = HolisticEngagement::where('record_id', $engagement->id)->where('record_type', new Engagement()->getMorphClass())->first();
    $holisticEngagementInbound = HolisticEngagement::where('record_id', $engagementResponse->id)->where('record_type', new EngagementResponse()->getMorphClass())->first();

    // Filter by outbound
    livewire(StudentMessagesDetailTable::class, [
        'cacheTag' => 'report-student-messages',
        'filters' => [],
    ])
        ->filterTable('direction', 'outbound')
        ->assertCanSeeTableRecords(collect([$holisticEngagementOutbound]))
        ->assertCanNotSeeTableRecords(collect([$holisticEngagementInbound]));

    // Filter by inbound
    livewire(StudentMessagesDetailTable::class, [
        'cacheTag' => 'report-student-messages',
        'filters' => [],
    ])
        ->filterTable('direction', 'inbound')
        ->assertCanSeeTableRecords(collect([$holisticEngagementInbound]))
        ->assertCanNotSeeTableRecords(collect([$holisticEngagementOutbound]));
});

it('filters by type properly', function () {
    $student = Student::factory()->create();

    $engagementEmail = Engagement::factory()->create([
        'recipient_id' => $student->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
        'channel' => NotificationChannel::Email,
    ]);

    $engagementSms = Engagement::factory()->create([
        'recipient_id' => $student->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
        'channel' => NotificationChannel::Sms,
    ]);

    $responseEmail = EngagementResponse::factory()->create([
        'sender_id' => $student->sisid,
        'sender_type' => (new Student())->getMorphClass(),
        'type' => EngagementResponseType::Email,
    ]);

    $responseSms = EngagementResponse::factory()->create([
        'sender_id' => $student->sisid,
        'sender_type' => (new Student())->getMorphClass(),
        'type' => EngagementResponseType::Sms,
    ]);

    $holisticEngagementEmail = HolisticEngagement::where('record_id', $engagementEmail->id)->where('record_type', new Engagement()->getMorphClass())->first();
    $holisticEngagementSms = HolisticEngagement::where('record_id', $engagementSms->id)->where('record_type', new Engagement()->getMorphClass())->first();
    $holisticResponseEmail = HolisticEngagement::where('record_id', $responseEmail->id)->where('record_type', new EngagementResponse()->getMorphClass())->first();
    $holisticResponseSms = HolisticEngagement::where('record_id', $responseSms->id)->where('record_type', new EngagementResponse()->getMorphClass())->first();

    // Filter by email
    livewire(StudentMessagesDetailTable::class, [
        'cacheTag' => 'report-student-messages',
        'filters' => [],
    ])
        ->filterTable('type', 'email')
        ->assertCanSeeTableRecords(collect([$holisticEngagementEmail, $holisticResponseEmail]))
        ->assertCanNotSeeTableRecords(collect([$holisticEngagementSms, $holisticResponseSms]));

    // Filter by sms
    livewire(StudentMessagesDetailTable::class, [
        'cacheTag' => 'report-student-messages',
        'filters' => [],
    ])
        ->filterTable('type', 'sms')
        ->assertCanSeeTableRecords(collect([$holisticEngagementSms, $holisticResponseSms]))
        ->assertCanNotSeeTableRecords(collect([$holisticEngagementEmail, $holisticResponseEmail]));
});

it('has export action with correct exporter class', function () {
    $student = Student::factory()->create();

    $engagement = Engagement::factory()->create([
        'recipient_id' => $student->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
    ]);

    $holisticEngagement = HolisticEngagement::where('record_id', $engagement->id)
        ->where('record_type', new Engagement()
            ->getMorphClass())
        ->first();

    livewire(StudentMessagesDetailTable::class, [
        'cacheTag' => 'report-student-messages-detail',
        'filters' => [],
    ])
        ->assertCanSeeTableRecords(collect([$holisticEngagement]))
        ->assertTableActionExists(ExportAction::class);
});

it('can start an export, sending a notification', function () {
    $count = random_int(1, 5);
    Storage::fake('s3');

    $user = User::factory()->create();
    actingAs($user);

    Event::fake();

    $students = Student::factory()->count($count)->create();

    $students->each(function (Student $student) use ($user) {
        Engagement::factory()
            ->count(2)
            ->email()
            ->for($user)
            ->create([
                'recipient_id' => $student->sisid,
                'recipient_type' => (new Student())->getMorphClass(),
            ]);

        EngagementResponse::factory()
            ->count(1)
            ->create([
                'sender_id' => $student->sisid,
                'sender_type' => (new Student())->getMorphClass(),
                'type' => EngagementResponseType::Email,
            ]);
    });

    livewire(StudentMessagesDetailTable::class, [
        'cacheTag' => 'report-student-messages-detail',
        'filters' => [],
    ])
        ->callTableAction(ExportAction::class)
        ->assertNotified();
});
