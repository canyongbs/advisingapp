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

use AdvisingApp\Prospect\Database\Seeders\ProspectStatusSeeder;
use AdvisingApp\Prospect\Enums\SystemProspectClassification;
use AdvisingApp\Prospect\Models\ProspectStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use function Pest\Laravel\seed;

it('seeds the default statuses with their required classification and protection on a fresh tenant', function (): void {
    seed([ProspectStatusSeeder::class]);

    expect(ProspectStatus::query()->count())->toBe(6);

    $new = ProspectStatus::query()->where('name', 'New')->sole();
    $converted = ProspectStatus::query()->where('name', 'Converted')->sole();

    expect($new->classification)->toBe(SystemProspectClassification::New)
        ->and($new->is_system_protected)->toBeTrue()
        ->and($converted->classification)->toBe(SystemProspectClassification::Converted)
        ->and($converted->is_system_protected)->toBeTrue();
});

it('reconciles provisioned "New" and "Converted" rows that were created without system protection', function (): void {
    // Simulate the earlier 2024_10_08_161832 data migration, which inserts the default
    // "New" and "Converted" statuses with the column default is_system_protected = false.
    DB::table('prospect_statuses')->insert([
        [
            'id' => (string) Str::orderedUuid(),
            'classification' => SystemProspectClassification::New->value,
            'name' => 'New',
            'color' => 'sky',
            'is_system_protected' => false,
            'sort' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'id' => (string) Str::orderedUuid(),
            'classification' => SystemProspectClassification::Converted->value,
            'name' => 'Converted',
            'color' => 'green',
            'is_system_protected' => false,
            'sort' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);

    seed([ProspectStatusSeeder::class]);

    // No duplicates are created for the already-present names.
    expect(ProspectStatus::query()->where('name', 'New')->count())->toBe(1)
        ->and(ProspectStatus::query()->where('name', 'Converted')->count())->toBe(1);

    $new = ProspectStatus::query()->where('name', 'New')->sole();
    $converted = ProspectStatus::query()->where('name', 'Converted')->sole();

    // The existing rows are reconciled to the required classification and protection.
    expect($new->classification)->toBe(SystemProspectClassification::New)
        ->and($new->is_system_protected)->toBeTrue()
        ->and($converted->classification)->toBe(SystemProspectClassification::Converted)
        ->and($converted->is_system_protected)->toBeTrue();
});

it('is idempotent and does not fail when protected statuses already exist', function (): void {
    seed([ProspectStatusSeeder::class]);
    seed([ProspectStatusSeeder::class]);

    expect(ProspectStatus::query()->count())->toBe(6)
        ->and(ProspectStatus::query()->where('is_system_protected', true)->pluck('name')->sort()->values()->all())
        ->toBe(['Converted', 'New']);
});
