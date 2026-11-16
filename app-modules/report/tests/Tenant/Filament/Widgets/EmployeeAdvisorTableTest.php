<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiAssistantUse;
use AdvisingApp\Report\Filament\Widgets\EmployeeAdvisorTable;
use Livewire\Livewire;

/**
 * @return array{AiAssistant, AiAssistant, AiAssistant}
 */
function createEmployeeAdvisorsForSorting(): array
{
    $tutoring = AiAssistant::factory()->create([
        'name' => 'Tutoring Services - Staff Advisor',
        'is_default' => false,
        'created_at' => now()->subDays(30),
        'updated_at' => now()->subDays(2),
    ]);

    $financialAid = AiAssistant::factory()->create([
        'name' => 'Financial Aid Handbook Advisor',
        'is_default' => false,
        'created_at' => now()->subDays(20),
        'updated_at' => now()->subDays(25),
    ]);

    $admissions = AiAssistant::factory()->create([
        'name' => 'Admissions FAQ Advisor',
        'is_default' => false,
        'created_at' => now()->subDays(10),
        'updated_at' => now()->subDays(12),
    ]);

    AiAssistantUse::factory()->count(5)->create(['assistant_id' => $tutoring->id, 'created_at' => now()->subDays(3)]);
    AiAssistantUse::factory()->count(1)->create(['assistant_id' => $financialAid->id, 'created_at' => now()->subDays(3)]);
    AiAssistantUse::factory()->count(3)->create(['assistant_id' => $admissions->id, 'created_at' => now()->subDays(3)]);

    return [$tutoring, $financialAid, $admissions];
}

it('lists employee advisors newest first by default', function (): void {
    [$tutoring, $financialAid, $admissions] = createEmployeeAdvisorsForSorting();

    Livewire::test(EmployeeAdvisorTable::class, ['cacheTag' => 'report-employee-advisor'])
        ->assertCanSeeTableRecords([$admissions, $financialAid, $tutoring], inOrder: true);
});

it('does not list default advisors', function (): void {
    [$tutoring] = createEmployeeAdvisorsForSorting();

    $defaultAdvisor = AiAssistant::factory()->create(['is_default' => true]);

    Livewire::test(EmployeeAdvisorTable::class, ['cacheTag' => 'report-employee-advisor'])
        ->assertCanSeeTableRecords([$tutoring])
        ->assertCanNotSeeTableRecords([$defaultAdvisor]);
});

it('sorts by the selected column', function (string $column, string $direction, array $expectedOrder): void {
    $advisors = createEmployeeAdvisorsForSorting();

    $expected = collect($expectedOrder)->map(fn (int $index): AiAssistant => $advisors[$index]);

    Livewire::test(EmployeeAdvisorTable::class, ['cacheTag' => 'report-employee-advisor'])
        ->sortTable($column, $direction)
        ->assertCanSeeTableRecords($expected, inOrder: true);
})->with([
    'exchanges ascending' => ['uses_count', 'asc', [1, 2, 0]],
    'exchanges descending' => ['uses_count', 'desc', [0, 2, 1]],
    'created ascending' => ['created_at', 'asc', [0, 1, 2]],
    'created descending' => ['created_at', 'desc', [2, 1, 0]],
    'last updated ascending' => ['updated_at', 'asc', [1, 2, 0]],
    'last updated descending' => ['updated_at', 'desc', [0, 2, 1]],
]);
