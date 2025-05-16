<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Campaign\Enums\CampaignActionType;
use AdvisingApp\Campaign\Jobs\SubscriptionCampaignActionJob;
use AdvisingApp\Campaign\Models\Campaign;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Campaign\Models\CampaignActionEducatable;
use AdvisingApp\Notification\Actions\SubscriptionCreate;
use AdvisingApp\Notification\Models\Contracts\Subscribable;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Segment\Enums\SegmentModel;
use AdvisingApp\Segment\Enums\SegmentType;
use AdvisingApp\Segment\Models\Segment;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Bus;

it('will execute appropriately on each educatable in the segment', function (array $priorSubscriptions, Educatable&Subscribable $educatable, bool $removePrior) {
    Bus::fake();

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

    collect($priorSubscriptions)
        ->each(
            fn ($userId) => resolve(SubscriptionCreate::class)
                ->handle(User::find($userId), $educatable, false)
        );

    $users = User::factory()->count(3)->create();

    /** @var CampaignAction $action */
    $action = CampaignAction::factory()
        ->for($campaign, 'campaign')
        ->create([
            'type' => CampaignActionType::Subscription,
            'data' => [
                'user_ids' => $users->pluck('id')->toArray(),
                'remove_prior' => $removePrior,
            ],
        ]);

    $campaignActionEducatable = CampaignActionEducatable::factory()
        ->for($action, 'campaignAction')
        // @phpstan-ignore argument.type
        ->for($educatable, 'educatable')
        ->create();

    expect($campaignActionEducatable->succeeded_at)->toBeNull()
        ->and($campaignActionEducatable->last_failed_at)->toBeNull();

    [$job] = new SubscriptionCampaignActionJob($campaignActionEducatable)->withFakeBatch();

    $job->handle();

    expect($educatable->subscriptions()->pluck('user_id')->toArray())
        ->toEqualCanonicalizing(
            $removePrior
                ? $users->pluck('id')->toArray()
                : [...$priorSubscriptions, ...$users->pluck('id')->toArray()]
        );

    expect($campaignActionEducatable->succeeded_at)->not()->toBeNull()
        ->and($campaignActionEducatable->last_failed_at)->toBeNull();
})
    ->with(
        [
            'no prior subscriptions | prospect | remove prior false' => [
                [],
                fn () => Prospect::factory()->create(),
                false,
            ],
            'no prior subscriptions | prospect | remove prior true' => [
                [],
                fn () => Prospect::factory()->create(),
                true,
            ],
            'prior subscriptions | prospect | remove prior false' => [
                fn () => User::factory()->create()->pluck('id')->toArray(),
                fn () => Prospect::factory()->create(),
                false,
            ],
            'prior subscriptions | prospect | remove prior true' => [
                fn () => User::factory()->create()->pluck('id')->toArray(),
                fn () => Prospect::factory()->create(),
                true,
            ],
            'no prior subscriptions | student | remove prior false' => [
                [],
                fn () => Student::factory()->create(),
                false,
            ],
            'no prior subscriptions | student | remove prior true' => [
                [],
                fn () => Student::factory()->create(),
                true,
            ],
            'prior subscriptions | student | remove prior false' => [
                fn () => User::factory()->create()->pluck('id')->toArray(),
                fn () => Student::factory()->create(),
                false,
            ],
            'prior subscriptions | student | remove prior true' => [
                fn () => User::factory()->create()->pluck('id')->toArray(),
                fn () => Student::factory()->create(),
                true,
            ],
        ]
    );
