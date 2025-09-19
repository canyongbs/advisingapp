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

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectEmailAddress;
use AdvisingApp\Report\Filament\Widgets\ProspectReportTableChart;
use App\Models\User;
use Filament\Tables\Actions\ExportAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('displays only prospects added within the selected date range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $prospectWithinRange1 = Prospect::factory()->state([
        'created_at' => $startDate,
    ])->create();

    $prospectWithinRange2 = Prospect::factory()->state([
        'created_at' => $endDate,
    ])->create();

    $prospectOutsideRange = Prospect::factory()->state([
        'created_at' => now()->subDays(20),
    ])->create();

    $filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    livewire(ProspectReportTableChart::class, [
        'cacheTag' => 'prospect-report-cache',
        'filters' => $filters,
    ])
        ->assertCanSeeTableRecords(collect([
            $prospectWithinRange1,
            $prospectWithinRange2,
        ]))
        ->assertCanNotSeeTableRecords(collect([$prospectOutsideRange]));
});

it('Export Action is available and correct exporter', function () {
    livewire(ProspectReportTableChart::class, [
        'cacheTag' => 'prospect-report-cache',
        'filters' => [],
    ])->assertTableActionExists(ExportAction::class);
});

it('Trigger the export action and send the notification message', function () {
    $count = random_int(1, 5);

    $user = User::factory()->create();
    actingAs($user);

    $prospects = Prospect::factory()->count($count)->for($user, 'createdBy')->create();

    $prospects->each(function ($prospect) {
        $email = ProspectEmailAddress::factory()->create([
            'prospect_id' => $prospect->getKey(),
        ]);
        $prospect->update(['primary_email_id' => $email->getKey()]);
    });

    livewire(ProspectReportTableChart::class, [
        'cacheTag' => 'prospect-report-cache',
        'filters' => [],
    ])
        ->callTableAction(ExportAction::class)
        ->assertNotified();
});
