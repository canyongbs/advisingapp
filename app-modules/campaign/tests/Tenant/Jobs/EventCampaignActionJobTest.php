<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Campaign\Enums\CampaignActionType;
use AdvisingApp\Campaign\Jobs\EventCampaignActionJob;
use AdvisingApp\Campaign\Models\Campaign;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Campaign\Models\CampaignActionEducatable;
use AdvisingApp\MeetingCenter\Enums\EventAttendeeStatus;
use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use AdvisingApp\MeetingCenter\Notifications\RegistrationLinkToEventAttendeeNotification;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Segment\Enums\SegmentModel;
use AdvisingApp\Segment\Enums\SegmentType;
use AdvisingApp\Segment\Models\Segment;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\assertDatabaseCount;

it('will execute appropriately on each educatable in the segment', function (Educatable $educatable) {
    Bus::fake();
    Notification::fake();

    /** @var Segment $segment */
    $segment = Segment::factory()->create([
        'type' => SegmentType::Static,
        'model' => match ($educatable::class) {
            Student::class => SegmentModel::Student,
            Prospect::class => SegmentModel::Prospect,
            default => throw new Exception('Invalid model type'),
        },
    ]);

    $campaign = Campaign::factory()
        ->for($segment, 'segment')
        ->for(User::factory()->licensed(LicenseType::cases()), 'createdBy')
        ->create();

    /** @var Event $event */
    $event = Event::factory()->create();

    $event->attendees()->truncate();

    /** @var CampaignAction $action */
    $action = CampaignAction::factory()
        ->for($campaign, 'campaign')
        ->create([
            'type' => CampaignActionType::Event,
            'data' => [
                'event' => $event->id,
            ],
        ]);

    $campaignActionEducatable = CampaignActionEducatable::factory()
        ->for($action, 'campaignAction')
        // @phpstan-ignore argument.type
        ->for($educatable, 'educatable')
        ->create();

    expect($campaignActionEducatable->succeeded_at)->toBeNull()
        ->and($campaignActionEducatable->last_failed_at)->toBeNull();

    [$job] = new EventCampaignActionJob($campaignActionEducatable)->withFakeBatch();

    $job->handle();

    assertDatabaseCount(
        EventAttendee::class,
        1,
    );

    $attendee = EventAttendee::query()
        ->where('event_id', $event->getKey())
        ->where('email', $educatable->primaryEmailAddress->address)
        ->where('status', EventAttendeeStatus::Invited)
        ->first();

    expect($attendee)->not()->toBeNull();

    Notification::assertSentTo($attendee, RegistrationLinkToEventAttendeeNotification::class);

    $campaignActionEducatable->refresh();

    expect($campaignActionEducatable->succeeded_at)->not()->toBeNull()
        ->and($campaignActionEducatable->last_failed_at)->toBeNull()
        ->and($campaignActionEducatable->related)->toEqual($attendee);
})
    ->with([
        'student' => [
            fn () => Student::factory()->create(),
        ],
        'prospect' => [
            fn () => Prospect::factory()->create(),
        ],
    ]);

it('will not duplicate an invite if the segment educatable was already invited', function (Educatable $educatable) {
    Bus::fake();
    Notification::fake();

    /** @var Segment $segment */
    $segment = Segment::factory()->create([
        'type' => SegmentType::Static,
        'model' => match ($educatable::class) {
            Student::class => SegmentModel::Student,
            Prospect::class => SegmentModel::Prospect,
            default => throw new Exception('Invalid model type'),
        },
    ]);

    $campaign = Campaign::factory()
        ->for($segment, 'segment')
        ->for(User::factory()->licensed(LicenseType::cases()), 'createdBy')
        ->create();

    /** @var Event $event */
    $event = Event::factory()->create();

    $event->attendees()->truncate();

    EventAttendee::factory()->create([
        'event_id' => $event->getKey(),
        'email' => $educatable->primaryEmailAddress->address,
        'status' => EventAttendeeStatus::Invited,
    ]);

    /** @var CampaignAction $action */
    $action = CampaignAction::factory()
        ->for($campaign, 'campaign')
        ->create([
            'type' => CampaignActionType::Event,
            'data' => [
                'event' => $event->id,
            ],
        ]);

    $campaignActionEducatable = CampaignActionEducatable::factory()
        ->for($action, 'campaignAction')
        // @phpstan-ignore argument.type
        ->for($educatable, 'educatable')
        ->create();

    expect($campaignActionEducatable->succeeded_at)->toBeNull()
        ->and($campaignActionEducatable->last_failed_at)->toBeNull();

    [$job] = new EventCampaignActionJob($campaignActionEducatable)->withFakeBatch();

    $job->handle();

    assertDatabaseCount(
        EventAttendee::class,
        1,
    );

    $attendee = EventAttendee::query()
        ->where('event_id', $event->getKey())
        ->where('email', $educatable->primaryEmailAddress->address)
        ->where('status', EventAttendeeStatus::Invited)
        ->first();

    expect($attendee)->not()->toBeNull();

    Notification::assertNotSentTo($attendee, RegistrationLinkToEventAttendeeNotification::class);

    $campaignActionEducatable->refresh();

    expect($campaignActionEducatable->succeeded_at)->not()->toBeNull()
        ->and($campaignActionEducatable->last_failed_at)->toBeNull()
        ->and($campaignActionEducatable->related)->toBeNull();
})
    ->with([
        'student' => [
            fn () => Student::factory()->create(),
        ],
        'prospect' => [
            fn () => Prospect::factory()->create(),
        ],
    ]);
