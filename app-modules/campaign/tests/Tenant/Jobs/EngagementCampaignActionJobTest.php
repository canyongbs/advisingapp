<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Campaign\Enums\CampaignActionType;
use AdvisingApp\Campaign\Jobs\EngagementCampaignActionJob;
use AdvisingApp\Campaign\Models\Campaign;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Campaign\Models\CampaignActionEducatable;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Engagement\Notifications\EngagementNotification;
use AdvisingApp\Notification\Enums\NotificationChannel;
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

// TODO: Tests for canRecieve checks

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

    // TODO: Change this to a dataset
    $channel = fake()->randomElement([NotificationChannel::Email, NotificationChannel::Sms]);

    $subject = ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => fake()->sentence]]]]];
    $body = ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => fake()->paragraph]]]]];

    /** @var CampaignAction $action */
    $action = CampaignAction::factory()
        ->for($campaign, 'campaign')
        ->create([
            'type' => match ($channel) {
                NotificationChannel::Email => CampaignActionType::BulkEngagementEmail,
                NotificationChannel::Sms => CampaignActionType::BulkEngagementSms,
                default => throw new Exception('Invalid channel type'),
            },
            'data' => [
                'channel' => $channel->value,
                'subject' => $subject,
                'body' => $body,
            ],
        ]);

    $campaignActionEducatable = CampaignActionEducatable::factory()
        ->for($action, 'campaignAction')
        // @phpstan-ignore argument.type
        ->for($educatable, 'educatable')
        ->create();

    expect($campaignActionEducatable->succeeded_at)->toBeNull()
        ->and($campaignActionEducatable->last_failed_at)->toBeNull();

    [$job] = new EngagementCampaignActionJob($campaignActionEducatable)->withFakeBatch();

    $job->handle();

    assertDatabaseCount(
        Engagement::class,
        1,
    );

    /** @var Engagement $engagement */
    $engagement = Engagement::query()->get()->first();

    expect($engagement->channel->value)->toEqual($channel->value)
        ->and($engagement->subject)->toEqual($subject)
        ->and($engagement->body)->toEqual($body);

    Notification::assertSentTo($educatable, EngagementNotification::class);

    $campaignActionEducatable->refresh();

    expect($campaignActionEducatable->succeeded_at)->not()->toBeNull()
        ->and($campaignActionEducatable->last_failed_at)->toBeNull()
        ->and($campaignActionEducatable->related)->toEqual($engagement);
})
    ->with([
        'student' => [
            fn () => Student::factory()->create(),
        ],
        'prospect' => [
            fn () => Prospect::factory()->create(),
        ],
    ]);
