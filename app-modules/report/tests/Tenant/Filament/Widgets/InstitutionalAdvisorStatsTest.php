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
use AdvisingApp\Report\Filament\Widgets\InstitutionalAdvisorStats;

it('returns correct total institutional advisor stats within the given date range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $count = random_int(1, 5);

    // Create institutional (default) advisor
    $institutionalAdvisor = AiAssistant::factory()->create(['is_default' => true]);

    // Create custom advisor (should be excluded)
    $customAdvisor = AiAssistant::factory()->create(['is_default' => false]);

    // Create smart and custom prompts
    $smartPrompt = Prompt::factory()->create(['is_smart' => true]);
    $customPrompt = Prompt::factory()->create(['is_smart' => false]);

    // Create assistant uses for institutional advisor within date range
    AiAssistantUse::factory()->count($count)->create([
        'assistant_id' => $institutionalAdvisor->id,
        'created_at' => $startDate,
    ]);

    AiAssistantUse::factory()->count($count)->create([
        'assistant_id' => $institutionalAdvisor->id,
        'created_at' => $endDate,
    ]);

    // Create assistant uses for custom advisor (should be excluded)
    AiAssistantUse::factory()->count($count)->create([
        'assistant_id' => $customAdvisor->id,
        'created_at' => $startDate,
    ]);

    // Create custom prompt uses within date range
    PromptUse::factory()->count($count)->create([
        'prompt_id' => $customPrompt->id,
        'created_at' => $startDate,
    ]);

    PromptUse::factory()->count($count)->create([
        'prompt_id' => $customPrompt->id,
        'created_at' => $endDate,
    ]);

    // Create smart prompt uses within date range
    PromptUse::factory()->count($count)->create([
        'prompt_id' => $smartPrompt->id,
        'created_at' => $startDate,
    ]);

    PromptUse::factory()->count($count)->create([
        'prompt_id' => $smartPrompt->id,
        'created_at' => $endDate,
    ]);

    // Create uses outside date range (should be excluded)
    AiAssistantUse::factory()->count($count)->create([
        'assistant_id' => $institutionalAdvisor->id,
        'created_at' => now()->subDays(20),
    ]);

    PromptUse::factory()->count($count)->create([
        'prompt_id' => $customPrompt->id,
        'created_at' => now()->subDays(20),
    ]);

    PromptUse::factory()->count($count)->create([
        'prompt_id' => $smartPrompt->id,
        'created_at' => now()->subDays(20),
    ]);

    $widget = new InstitutionalAdvisorStats();
    $widget->cacheTag = 'report-institutional-advisor';
    $widget->filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $stats = $widget->getStats();

    expect($stats)->toHaveCount(3)
        ->and($stats[0]->getLabel())->toBe('Exchanges')
        ->and($stats[0]->getValue())->toEqual($count * 2) // Only institutional advisor uses within date range
        ->and($stats[1]->getLabel())->toBe('Custom Prompts')
        ->and($stats[1]->getValue())->toEqual($count * 2) // Custom prompt uses within date range
        ->and($stats[2]->getLabel())->toBe('Smart Prompts')
        ->and($stats[2]->getValue())->toEqual($count * 2); // Smart prompt uses within date range
});

it('returns correct total institutional advisor stats without date filters', function () {
    $count = random_int(1, 5);

    // Create institutional (default) advisor
    $institutionalAdvisor = AiAssistant::factory()->create(['is_default' => true]);

    // Create custom advisor (should be excluded)
    $customAdvisor = AiAssistant::factory()->create(['is_default' => false]);

    // Create smart and custom prompts
    $smartPrompt = Prompt::factory()->create(['is_smart' => true]);
    $customPrompt = Prompt::factory()->create(['is_smart' => false]);

    // Create assistant uses for institutional advisor
    AiAssistantUse::factory()->count($count)->create([
        'assistant_id' => $institutionalAdvisor->id,
    ]);

    // Create assistant uses for custom advisor (should be excluded)
    AiAssistantUse::factory()->count($count)->create([
        'assistant_id' => $customAdvisor->id,
    ]);

    // Create custom prompt uses
    PromptUse::factory()->count($count * 2)->create([
        'prompt_id' => $customPrompt->id,
    ]);

    // Create smart prompt uses
    PromptUse::factory()->count($count * 3)->create([
        'prompt_id' => $smartPrompt->id,
    ]);

    $widget = new InstitutionalAdvisorStats();
    $widget->cacheTag = 'report-institutional-advisor';
    $widget->filters = [];

    $stats = $widget->getStats();

    expect($stats)->toHaveCount(3)
        ->and($stats[0]->getLabel())->toBe('Exchanges')
        ->and($stats[0]->getValue())->toEqual($count) // Only institutional advisor uses
        ->and($stats[1]->getLabel())->toBe('Custom Prompts')
        ->and($stats[1]->getValue())->toEqual($count * 2) // All custom prompt uses
        ->and($stats[2]->getLabel())->toBe('Smart Prompts')
        ->and($stats[2]->getValue())->toEqual($count * 3); // All smart prompt uses
});

it('returns zero stats when no institutional advisor usage exists', function () {
    // Create only custom advisor
    $customAdvisor = AiAssistant::factory()->create(['is_default' => false]);

    // Create smart and custom prompts but no uses
    $smartPrompt = Prompt::factory()->create(['is_smart' => true]);
    $customPrompt = Prompt::factory()->create(['is_smart' => false]);

    // Create assistant uses only for custom advisor
    AiAssistantUse::factory()->count(5)->create([
        'assistant_id' => $customAdvisor->id,
    ]);

    $widget = new InstitutionalAdvisorStats();
    $widget->cacheTag = 'report-institutional-advisor';
    $widget->filters = [];

    $stats = $widget->getStats();

    expect($stats)->toHaveCount(3)
        ->and($stats[0]->getLabel())->toBe('Exchanges')
        ->and($stats[0]->getValue())->toEqual('0')
        ->and($stats[1]->getLabel())->toBe('Custom Prompts')
        ->and($stats[1]->getValue())->toEqual('0')
        ->and($stats[2]->getLabel())->toBe('Smart Prompts')
        ->and($stats[2]->getValue())->toEqual('0');
});

it('correctly separates smart and custom prompt stats', function () {
    // Create smart and custom prompts
    $smartPrompt1 = Prompt::factory()->create(['is_smart' => true]);
    $smartPrompt2 = Prompt::factory()->create(['is_smart' => true]);
    $customPrompt1 = Prompt::factory()->create(['is_smart' => false]);
    $customPrompt2 = Prompt::factory()->create(['is_smart' => false]);

    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    // Create smart prompt uses
    PromptUse::factory()->count(3)->create([
        'prompt_id' => $smartPrompt1->id,
        'created_at' => $startDate,
    ]);

    PromptUse::factory()->count(2)->create([
        'prompt_id' => $smartPrompt2->id,
        'created_at' => $endDate,
    ]);

    // Create custom prompt uses
    PromptUse::factory()->count(4)->create([
        'prompt_id' => $customPrompt1->id,
        'created_at' => $startDate,
    ]);

    PromptUse::factory()->count(1)->create([
        'prompt_id' => $customPrompt2->id,
        'created_at' => $endDate,
    ]);

    $widget = new InstitutionalAdvisorStats();
    $widget->cacheTag = 'report-institutional-advisor';
    $widget->filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $stats = $widget->getStats();

    expect($stats[1]->getValue())->toEqual(5) // 4 + 1 custom prompt uses
        ->and($stats[2]->getValue())->toEqual(5); // 3 + 2 smart prompt uses
});

it('handles null dates correctly for caching', function () {
    // Create institutional advisor and prompts
    $institutionalAdvisor = AiAssistant::factory()->create(['is_default' => true]);
    $smartPrompt = Prompt::factory()->create(['is_smart' => true]);
    $customPrompt = Prompt::factory()->create(['is_smart' => false]);

    // Create some uses
    AiAssistantUse::factory()->count(3)->create([
        'assistant_id' => $institutionalAdvisor->id,
    ]);

    PromptUse::factory()->count(2)->create([
        'prompt_id' => $customPrompt->id,
    ]);

    PromptUse::factory()->count(4)->create([
        'prompt_id' => $smartPrompt->id,
    ]);

    // Test with null filters (should use cache)
    $widget = new InstitutionalAdvisorStats();
    $widget->cacheTag = 'report-institutional-advisor';
    $widget->filters = [];

    $stats = $widget->getStats();

    expect($stats)->toHaveCount(3)
        ->and($stats[0]->getValue())->toEqual(3)
        ->and($stats[1]->getValue())->toEqual(2)
        ->and($stats[2]->getValue())->toEqual(4);
});
