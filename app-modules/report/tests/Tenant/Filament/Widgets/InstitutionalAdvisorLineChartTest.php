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

use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiAssistantUse;
use AdvisingApp\Ai\Models\Prompt;
use AdvisingApp\Ai\Models\PromptUse;
use AdvisingApp\Report\Filament\Widgets\InstitutionalAdvisorLineChart;

it('returns correct monthly institutional advisor usage data within the given date range', function () {
    $startDate = now()->subDays(90);
    $endDate = now()->subDays(5);

    // Create institutional (default) advisor
    $institutionalAdvisor = AiAssistant::factory()->create(['is_default' => true]);

    // Create custom advisor (should be excluded)
    $customAdvisor = AiAssistant::factory()->create(['is_default' => false]);

    // Create smart and custom prompts
    $smartPrompt = Prompt::factory()->create(['is_smart' => true]);
    $customPrompt = Prompt::factory()->create(['is_smart' => false]);

    // Create assistant uses for institutional advisor within date range
    AiAssistantUse::factory()->count(5)->create([
        'assistant_id' => $institutionalAdvisor->id,
        'created_at' => $startDate,
    ]);

    AiAssistantUse::factory()->count(3)->create([
        'assistant_id' => $institutionalAdvisor->id,
        'created_at' => $endDate,
    ]);

    // Create assistant uses for custom advisor (should be excluded)
    AiAssistantUse::factory()->count(4)->create([
        'assistant_id' => $customAdvisor->id,
        'created_at' => $startDate,
    ]);

    // Create custom prompt uses within date range
    PromptUse::factory()->count(6)->create([
        'prompt_id' => $customPrompt->id,
        'created_at' => $startDate,
    ]);

    PromptUse::factory()->count(2)->create([
        'prompt_id' => $customPrompt->id,
        'created_at' => $endDate,
    ]);

    // Create smart prompt uses within date range
    PromptUse::factory()->count(4)->create([
        'prompt_id' => $smartPrompt->id,
        'created_at' => $startDate,
    ]);

    PromptUse::factory()->count(7)->create([
        'prompt_id' => $smartPrompt->id,
        'created_at' => $endDate,
    ]);

    // Create uses outside date range (should be excluded)
    AiAssistantUse::factory()->count(2)->create([
        'assistant_id' => $institutionalAdvisor->id,
        'created_at' => now()->subDays(120),
    ]);

    PromptUse::factory()->count(3)->create([
        'prompt_id' => $customPrompt->id,
        'created_at' => now()->subDays(120),
    ]);

    $widgetInstance = new InstitutionalAdvisorLineChart();
    $widgetInstance->cacheTag = 'report-institutional-advisor';
    $widgetInstance->pageFilters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $data = $widgetInstance->getData();

    expect($data)->toHaveKeys(['datasets', 'labels'])
        ->and($data['datasets'])->toHaveCount(3)
        ->and($data['datasets'][0])->toHaveKeys(['label', 'data', 'borderColor', 'pointBackgroundColor'])
        ->and($data['datasets'][0]['label'])->toBe('Exchanges')
        ->and($data['datasets'][0]['borderColor'])->toBe('#2C8BCA')
        ->and($data['datasets'][1]['label'])->toBe('Custom Prompts')
        ->and($data['datasets'][1]['borderColor'])->toBe('#F59E0B')
        ->and($data['datasets'][2]['label'])->toBe('Smart Prompts')
        ->and($data['datasets'][2]['borderColor'])->toBe('#10B981')
        ->and($data['labels'])->not->toBeEmpty()
        ->and(array_sum($data['datasets'][0]['data']))->toBe(8) // 5 + 3 exchanges
        ->and(array_sum($data['datasets'][1]['data']))->toBe(8) // 6 + 2 custom prompt uses
        ->and(array_sum($data['datasets'][2]['data']))->toBe(11); // 4 + 7 smart prompt uses
});

it('returns correct monthly institutional advisor usage data without date filters', function () {
    // Create institutional (default) advisor
    $institutionalAdvisor = AiAssistant::factory()->create(['is_default' => true]);

    // Create custom advisor (should be excluded)
    $customAdvisor = AiAssistant::factory()->create(['is_default' => false]);

    // Create smart and custom prompts
    $smartPrompt = Prompt::factory()->create(['is_smart' => true]);
    $customPrompt = Prompt::factory()->create(['is_smart' => false]);

    // Create assistant uses for institutional advisor
    AiAssistantUse::factory()->count(7)->create([
        'assistant_id' => $institutionalAdvisor->id,
        'created_at' => now()->subDays(30),
    ]);

    // Create assistant uses for custom advisor (should be excluded)
    AiAssistantUse::factory()->count(4)->create([
        'assistant_id' => $customAdvisor->id,
        'created_at' => now()->subDays(30),
    ]);

    // Create custom prompt uses
    PromptUse::factory()->count(9)->create([
        'prompt_id' => $customPrompt->id,
        'created_at' => now()->subDays(60),
    ]);

    // Create smart prompt uses
    PromptUse::factory()->count(5)->create([
        'prompt_id' => $smartPrompt->id,
        'created_at' => now()->subDays(30),
    ]);

    $widgetInstance = new InstitutionalAdvisorLineChart();
    $widgetInstance->cacheTag = 'report-institutional-advisor';
    $widgetInstance->pageFilters = [];

    $data = $widgetInstance->getData();

    expect($data)->toHaveKeys(['datasets', 'labels'])
        ->and($data['datasets'])->toHaveCount(3)
        ->and($data['datasets'][0]['label'])->toBe('Exchanges')
        ->and($data['datasets'][1]['label'])->toBe('Custom Prompts')
        ->and($data['datasets'][2]['label'])->toBe('Smart Prompts')
        ->and($data['labels'])->toHaveCount(12) // Default 12 months
        ->and(array_sum($data['datasets'][0]['data']))->toBe(7) // Only institutional advisor uses
        ->and(array_sum($data['datasets'][1]['data']))->toBe(9) // Custom prompt uses
        ->and(array_sum($data['datasets'][2]['data']))->toBe(5); // Smart prompt uses
});

it('returns empty data when no institutional advisor usage exists', function () {
    // Create only custom advisor
    $customAdvisor = AiAssistant::factory()->create(['is_default' => false]);

    // Create smart and custom prompts but no uses
    Prompt::factory()->create(['is_smart' => true]);
    Prompt::factory()->create(['is_smart' => false]);

    // Create assistant uses only for custom advisor
    AiAssistantUse::factory()->count(3)->create([
        'assistant_id' => $customAdvisor->id,
        'created_at' => now()->subDays(30),
    ]);

    $widgetInstance = new InstitutionalAdvisorLineChart();
    $widgetInstance->cacheTag = 'report-institutional-advisor';
    $widgetInstance->pageFilters = [];

    $data = $widgetInstance->getData();

    expect($data)->toHaveKeys(['datasets', 'labels'])
        ->and($data['datasets'])->toHaveCount(3)
        ->and($data['datasets'][0]['data'])->each->toBe(0)
        ->and($data['datasets'][1]['data'])->each->toBe(0)
        ->and($data['datasets'][2]['data'])->each->toBe(0)
        ->and(array_sum($data['datasets'][0]['data']))->toBe(0)
        ->and(array_sum($data['datasets'][1]['data']))->toBe(0)
        ->and(array_sum($data['datasets'][2]['data']))->toBe(0);
});

it('separates smart and custom prompt usage correctly', function () {
    // Create prompts
    $smartPrompt1 = Prompt::factory()->create(['is_smart' => true]);
    $smartPrompt2 = Prompt::factory()->create(['is_smart' => true]);
    $customPrompt1 = Prompt::factory()->create(['is_smart' => false]);
    $customPrompt2 = Prompt::factory()->create(['is_smart' => false]);

    $startDate = now()->subDays(30);

    // Create smart prompt uses
    PromptUse::factory()->count(3)->create([
        'prompt_id' => $smartPrompt1->id,
        'created_at' => $startDate,
    ]);

    PromptUse::factory()->count(2)->create([
        'prompt_id' => $smartPrompt2->id,
        'created_at' => $startDate,
    ]);

    // Create custom prompt uses
    PromptUse::factory()->count(4)->create([
        'prompt_id' => $customPrompt1->id,
        'created_at' => $startDate,
    ]);

    PromptUse::factory()->count(1)->create([
        'prompt_id' => $customPrompt2->id,
        'created_at' => $startDate,
    ]);

    $widgetInstance = new InstitutionalAdvisorLineChart();
    $widgetInstance->cacheTag = 'report-institutional-advisor';
    $widgetInstance->pageFilters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => now()->toDateString(),
    ];

    $data = $widgetInstance->getData();

    expect(array_sum($data['datasets'][1]['data']))->toBe(5) // 4 + 1 custom prompt uses
        ->and(array_sum($data['datasets'][2]['data']))->toBe(5); // 3 + 2 smart prompt uses
});
