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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiAssistantUse;
use AdvisingApp\Report\Filament\Widgets\CustomAdvisorStats;
use App\Models\User;

it('returns correct total custom advisor stats within the given date range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $count = random_int(1, 5);

    // Create custom advisors within date range
    AiAssistant::factory()->count($count)->create([
        'is_default' => false,
        'created_at' => $startDate,
    ]);

    AiAssistant::factory()->count($count)->create([
        'is_default' => false,
        'created_at' => $endDate,
    ]);

    // Create default advisors (should be excluded)
    AiAssistant::factory()->count($count)->create([
        'is_default' => true,
        'created_at' => $startDate,
    ]);

    // Create custom advisors outside date range (should be excluded)
    AiAssistant::factory()->count($count)->create([
        'is_default' => false,
        'created_at' => now()->subDays(20),
    ]);

    // Create custom advisor and unique users
    $customAdvisor = AiAssistant::factory()->create(['is_default' => false]);
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $user3 = User::factory()->create();

    // Create assistant uses with specific users within date range
    AiAssistantUse::factory()->count($count)->create([
        'assistant_id' => $customAdvisor->id,
        'user_id' => $user1->id,
        'created_at' => $startDate,
    ]);

    AiAssistantUse::factory()->count($count)->create([
        'assistant_id' => $customAdvisor->id,
        'user_id' => $user2->id,
        'created_at' => $endDate,
    ]);

    // Additional use with third user
    AiAssistantUse::factory()->create([
        'assistant_id' => $customAdvisor->id,
        'user_id' => $user3->id,
        'created_at' => $endDate,
    ]);

    // Create assistant uses for default advisor (should be excluded)
    $defaultAdvisor = AiAssistant::factory()->create(['is_default' => true]);
    AiAssistantUse::factory()->count($count)->create([
        'assistant_id' => $defaultAdvisor->id,
        'created_at' => $startDate,
    ]);

    // Create uses for default advisor with same users (should be excluded)
    AiAssistantUse::factory()->create([
        'assistant_id' => $defaultAdvisor->id,
        'user_id' => $user1->id,
        'created_at' => $startDate,
    ]);

    $widget = new CustomAdvisorStats();
    $widget->cacheTag = 'report-custom-advisor';
    $widget->filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $stats = $widget->getStats();

    expect($stats)->toHaveCount(3)
        ->and($stats[0]->getLabel())->toBe('Custom Advisors')
        ->and($stats[0]->getValue())->toEqual($count * 2) // Only custom advisors within date range
        ->and($stats[1]->getLabel())->toBe('Exchanges')
        ->and($stats[1]->getValue())->toEqual(($count * 2) + 1) // Custom advisor uses within date range
        ->and($stats[2]->getLabel())->toBe('Unique Users')
        ->and($stats[2]->getValue())->toEqual(3); // Unique users for custom advisor
});

it('returns correct total custom advisor stats without date filters', function () {
    $count = random_int(1, 5);

    // Create custom advisors
    AiAssistant::factory()->count($count)->create(['is_default' => false]);

    // Create default advisors (should be excluded)
    AiAssistant::factory()->count($count)->create(['is_default' => true]);

    // Create custom advisor and unique users
    $customAdvisor = AiAssistant::factory()->create(['is_default' => false]);
    $users = User::factory()->count(3)->create();

    // Create assistant uses with specific users
    AiAssistantUse::factory()->count($count)->create([
        'assistant_id' => $customAdvisor->id,
        'user_id' => $users[0]->id,
    ]);

    // Create assistant uses for default advisor (should be excluded)
    $defaultAdvisor = AiAssistant::factory()->create(['is_default' => true]);
    AiAssistantUse::factory()->count($count)->create([
        'assistant_id' => $defaultAdvisor->id,
    ]);

    // Create additional uses with other specific users
    foreach ($users->slice(1) as $user) {
        AiAssistantUse::factory()->create([
            'assistant_id' => $customAdvisor->id,
            'user_id' => $user->id,
        ]);
    }

    $widget = new CustomAdvisorStats();
    $widget->cacheTag = 'report-custom-advisor';
    $widget->filters = [];

    $stats = $widget->getStats();

    expect($stats)->toHaveCount(3)
        ->and($stats[0]->getLabel())->toBe('Custom Advisors')
        ->and($stats[0]->getValue())->toEqual($count + 1) // All custom advisors
        ->and($stats[1]->getLabel())->toBe('Exchanges')
        ->and($stats[1]->getValue())->toEqual($count + 2) // All custom advisor uses
        ->and($stats[2]->getLabel())->toBe('Unique Users')
        ->and($stats[2]->getValue())->toEqual(3); // Unique users for custom advisor
});

it('returns zero stats when no custom advisors exist', function () {
    // Create only default advisors
    AiAssistant::factory()->count(3)->create(['is_default' => true]);

    // Create assistant uses only for default advisors
    $defaultAdvisor = AiAssistant::factory()->create(['is_default' => true]);
    AiAssistantUse::factory()->count(5)->create([
        'assistant_id' => $defaultAdvisor->id,
    ]);

    $widget = new CustomAdvisorStats();
    $widget->cacheTag = 'report-custom-advisor';
    $widget->filters = [];

    $stats = $widget->getStats();

    expect($stats)->toHaveCount(3)
        ->and($stats[0]->getLabel())->toBe('Custom Advisors')
        ->and($stats[0]->getValue())->toEqual('0')
        ->and($stats[1]->getLabel())->toBe('Exchanges')
        ->and($stats[1]->getValue())->toEqual('0')
        ->and($stats[2]->getLabel())->toBe('Unique Users')
        ->and($stats[2]->getValue())->toEqual('0');
});
