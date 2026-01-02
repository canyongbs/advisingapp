<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiAssistantUse;
use AdvisingApp\Report\Filament\Widgets\CustomAdvisorLineChart;

it('returns correct monthly custom advisor exchanges data within the given date range', function () {
    $startDate = now()->subMonths(3);
    $endDate = now()->subDays(5);

    // Create custom advisors
    $customAdvisor1 = AiAssistant::factory()->create(['is_default' => false]);
    $customAdvisor2 = AiAssistant::factory()->create(['is_default' => false]);

    // Create default advisor (should be excluded)
    $defaultAdvisor = AiAssistant::factory()->create(['is_default' => true]);

    // Create assistant uses for custom advisors within date range
    AiAssistantUse::factory()->count(5)->create([
        'assistant_id' => $customAdvisor1->id,
        'created_at' => $startDate,
    ]);

    AiAssistantUse::factory()->count(3)->create([
        'assistant_id' => $customAdvisor2->id,
        'created_at' => $endDate,
    ]);

    // Create assistant uses for default advisor (should be excluded)
    AiAssistantUse::factory()->count(4)->create([
        'assistant_id' => $defaultAdvisor->id,
        'created_at' => $startDate,
    ]);

    // Create assistant uses outside date range (should be excluded)
    AiAssistantUse::factory()->count(2)->create([
        'assistant_id' => $customAdvisor1->id,
        'created_at' => now()->subMonths(4),
    ]);

    $widgetInstance = new CustomAdvisorLineChart();
    $widgetInstance->cacheTag = 'report-custom-advisor';
    $widgetInstance->pageFilters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $data = $widgetInstance->getData();

    expect($data)->toHaveKeys(['datasets', 'labels'])
        ->and($data['datasets'])->toHaveCount(1)
        ->and($data['datasets'][0])->toHaveKeys(['label', 'data', 'borderColor', 'pointBackgroundColor'])
        ->and($data['datasets'][0]['label'])->toBe('Exchanges')
        ->and($data['datasets'][0]['borderColor'])->toBe('#7C3AED')
        ->and($data['datasets'][0]['pointBackgroundColor'])->toBe('#7C3AED')
        ->and($data['labels'])->not->toBeEmpty()
        ->and(array_sum($data['datasets'][0]['data']))->toBe(8); // 5 + 3 custom advisor uses
});

it('returns correct monthly custom advisor exchanges data without date filters', function () {
    // Create custom advisors
    $customAdvisor1 = AiAssistant::factory()->create(['is_default' => false]);
    $customAdvisor2 = AiAssistant::factory()->create(['is_default' => false]);

    // Create default advisor (should be excluded)
    $defaultAdvisor = AiAssistant::factory()->create(['is_default' => true]);

    // Create assistant uses for custom advisors
    AiAssistantUse::factory()->count(7)->create([
        'assistant_id' => $customAdvisor1->id,
        'created_at' => now()->subMonth(),
    ]);

    AiAssistantUse::factory()->count(4)->create([
        'assistant_id' => $customAdvisor2->id,
        'created_at' => now()->subMonths(2),
    ]);

    // Create assistant uses for default advisor (should be excluded)
    AiAssistantUse::factory()->count(5)->create([
        'assistant_id' => $defaultAdvisor->id,
        'created_at' => now()->subDays(30),
    ]);

    $widgetInstance = new CustomAdvisorLineChart();
    $widgetInstance->cacheTag = 'report-custom-advisor';
    $widgetInstance->pageFilters = [];

    $data = $widgetInstance->getData();

    expect($data)->toHaveKeys(['datasets', 'labels'])
        ->and($data['datasets'])->toHaveCount(1)
        ->and($data['datasets'][0])->toHaveKeys(['label', 'data', 'borderColor', 'pointBackgroundColor'])
        ->and($data['datasets'][0]['label'])->toBe('Exchanges')
        ->and($data['labels'])->toHaveCount(12) // Default 12 months
        ->and(array_sum($data['datasets'][0]['data']))->toBe(11); // 7 + 4 custom advisor uses
});

it('returns empty data when no custom advisor exchanges exist', function () {
    // Create only default advisor
    $defaultAdvisor = AiAssistant::factory()->create(['is_default' => true]);

    // Create assistant uses only for default advisor
    AiAssistantUse::factory()->count(3)->create([
        'assistant_id' => $defaultAdvisor->id,
        'created_at' => now()->subMonth(),
    ]);

    $widgetInstance = new CustomAdvisorLineChart();
    $widgetInstance->cacheTag = 'report-custom-advisor';
    $widgetInstance->pageFilters = [];

    $data = $widgetInstance->getData();

    expect($data)->toHaveKeys(['datasets', 'labels'])
        ->and($data['datasets'])->toHaveCount(1)
        ->and($data['datasets'][0]['data'])->each->toBe(0)
        ->and(array_sum($data['datasets'][0]['data']))->toBe(0);
});
