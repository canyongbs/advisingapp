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

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Campaign\Enums\CampaignActionType;
use AdvisingApp\Campaign\Jobs\EngagementCampaignActionJob;
use AdvisingApp\Campaign\Models\Campaign;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Campaign\Models\CampaignActionEducatable;
use AdvisingApp\Campaign\Models\CampaignActionEducatableRelated;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Engagement\Notifications\EngagementNotification;
use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Enums\SegmentType;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\assertDatabaseCount;

it('will execute appropriately on each educatable in the group', function (Educatable $educatable, NotificationChannel $channel) {
    Bus::fake();
    Notification::fake();

    /** @var Group $group */
    $group = Group::factory()->create([
        'type' => SegmentType::Static,
        'model' => match ($educatable::class) {
            Student::class => GroupModel::Student,
            Prospect::class => GroupModel::Prospect,
            default => throw new Exception('Invalid model type'),
        },
    ]);

    $campaign = Campaign::factory()
        ->for($group, 'segment')
        ->for(User::factory()->licensed(LicenseType::cases()), 'createdBy')
        ->create();

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

    [$job] = (new EngagementCampaignActionJob($campaignActionEducatable))->withFakeBatch();

    $job->handle();

    assertDatabaseCount(
        Engagement::class,
        1,
    );

    /** @var Engagement $engagement */
    $engagement = Engagement::query()->first();

    expect($engagement->channel->value)->toEqual($channel->value)
        ->and($engagement->subject)->toEqual($subject)
        ->and($engagement->body)->toEqual($body);

    Notification::assertSentTo($educatable, EngagementNotification::class);

    $campaignActionEducatable->refresh();

    expect($campaignActionEducatable->succeeded_at)->not()->toBeNull()
        ->and($campaignActionEducatable->last_failed_at)->toBeNull()
        ->and($campaignActionEducatable->related)->toHaveCount(1);

    /** @var CampaignActionEducatableRelated $campaignActionEducatableRelated */
    $campaignActionEducatableRelated = $campaignActionEducatable->related->first();

    expect($campaignActionEducatableRelated->related->is($engagement))->toBeTrue();
})
    ->with([
        'student' => [
            fn () => Student::factory()->create(),
        ],
        'prospect' => [
            fn () => Prospect::factory()->create(),
        ],
    ])
    ->with([
        'email' => [
            fn () => NotificationChannel::Email,
        ],
        'sms' => [
            fn () => NotificationChannel::Sms,
        ],
    ]);

it('will throw an exception if a canRecieve check fails', function (Educatable $educatable, NotificationChannel $channel) {
    Bus::fake();
    Notification::fake();

    match ($channel) {
        NotificationChannel::Email => $educatable->primaryEmailAddress()->delete(),
        NotificationChannel::Sms => $educatable->primaryPhoneNumber()->delete(),
        default => throw new Exception('Invalid channel type'),
    };

    $educatable->refresh();

    /** @var Group $group */
    $group = Group::factory()->create([
        'type' => SegmentType::Static,
        'model' => match ($educatable::class) {
            Student::class => GroupModel::Student,
            Prospect::class => GroupModel::Prospect,
            default => throw new Exception('Invalid model type'),
        },
    ]);

    $campaign = Campaign::factory()
        ->for($group, 'segment')
        ->for(User::factory()->licensed(LicenseType::cases()), 'createdBy')
        ->create();

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

    [$job] = (new EngagementCampaignActionJob($campaignActionEducatable))->withFakeBatch();

    $job->handle();

    assertDatabaseCount(
        Engagement::class,
        0,
    );

    /** @var Engagement $engagement */
    $engagement = Engagement::query()->first();

    expect($engagement->channel->value)->toEqual($channel->value)
        ->and($engagement->subject)->toEqual($subject)
        ->and($engagement->body)->toEqual($body);

    Notification::assertNotSentTo($educatable, EngagementNotification::class);

    $campaignActionEducatable->refresh();

    expect($campaignActionEducatable->succeeded_at)->toBeNull()
        ->and($campaignActionEducatable->last_failed_at)->not->toBeNull();
})
    ->with([
        'student' => [
            fn () => Student::factory()->create(),
        ],
        'prospect' => [
            fn () => Prospect::factory()->create(),
        ],
    ])
    ->with([
        'email' => [
            fn () => NotificationChannel::Email,
        ],
        'sms' => [
            fn () => NotificationChannel::Sms,
        ],
    ])
    ->throws(
        Exception::class,
        'The educatable cannot receive notifications on this channel.'
    );
