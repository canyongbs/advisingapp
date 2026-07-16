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

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Group\Actions\TranslateGroupFilters;
use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;

use function Pest\Laravel\actingAs;

/**
 * @return array<string, mixed>
 */
function studentLastNameContainsFilters(string $text): array
{
    return [
        'queryBuilder' => [
            'rules' => [
                'C0Cy' => [
                    'type' => 'last',
                    'data' => [
                        'operator' => 'contains',
                        'settings' => [
                            'text' => $text,
                        ],
                    ],
                ],
            ],
        ],
    ];
}

it('resolves ad-hoc live filters to the same records as an equivalent saved group', function () {
    actingAs(User::factory()->licensed(LicenseType::cases())->create());

    $filters = studentLastNameContainsFilters('John');

    Student::factory()->count(3)->create(['last' => 'John']);
    Student::factory()->count(2)->create(['last' => 'Doe']);

    $group = Group::factory()->create([
        'model' => GroupModel::Student,
        'filters' => $filters,
    ]);

    $savedIds = app(TranslateGroupFilters::class)->execute($group)->pluck((new Student())->getKeyName())->all();
    $rawIds = app(TranslateGroupFilters::class)->executeRawFilters(GroupModel::Student, $filters)->pluck((new Student())->getKeyName())->all();

    sort($savedIds);
    sort($rawIds);

    expect($rawIds)->toHaveCount(3)
        ->and($rawIds)->toEqual($savedIds);
});

it('applies ad-hoc live filters onto an existing query', function () {
    actingAs(User::factory()->licensed(LicenseType::cases())->create());

    $filters = studentLastNameContainsFilters('John');

    Student::factory()->count(4)->create(['last' => 'John']);
    Student::factory()->count(6)->create(['last' => 'Doe']);

    $matchingIds = app(TranslateGroupFilters::class)
        ->applyRawFiltersToQuery(GroupModel::Student, $filters, Student::query())
        ->pluck((new Student())->getKeyName())
        ->all();

    expect($matchingIds)->toHaveCount(4);
});

it('treats empty ad-hoc live filters as no filter', function () {
    actingAs(User::factory()->licensed(LicenseType::cases())->create());

    Student::factory()->count(5)->create();

    $ids = app(TranslateGroupFilters::class)
        ->executeRawFilters(GroupModel::Student, [])
        ->pluck((new Student())->getKeyName())
        ->all();

    expect($ids)->toHaveCount(5);
});
