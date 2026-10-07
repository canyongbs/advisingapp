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

use AdvisingApp\Group\Actions\TranslateGroupFilters;
use AdvisingApp\Group\Filament\Resources\Groups\Pages\EditGroup;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\StudentDataModel\Enums\SisSystem;
use AdvisingApp\StudentDataModel\Enums\StudentTermAttributeField;
use AdvisingApp\StudentDataModel\Filament\Filters\TermAttributeOperator;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentTermAttribute;
use AdvisingApp\StudentDataModel\Models\Term;
use AdvisingApp\StudentDataModel\Settings\StudentInformationSystemSettings;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

beforeEach(function () {
    asSuperAdmin();

    $sisSettings = app(StudentInformationSystemSettings::class);
    $sisSettings->is_enabled = true;
    $sisSettings->sis_system = SisSystem::ThesisElements;
    $sisSettings->save();
});

/**
 * @return array<string, mixed>
 */
$termAttributeFilters = function (string $termId, string $attribute, string $value, bool $isInverse = false): array {
    return [
        'queryBuilder' => [
            'rules' => [
                'aBcD' => [
                    'type' => 'termAttribute',
                    'data' => [
                        'operator' => $isInverse ? 'termAttribute.inverse' : 'termAttribute',
                        'settings' => [
                            'sis_term_id' => $termId,
                            'attribute' => $attribute,
                            'value' => $value,
                        ],
                    ],
                ],
            ],
        ],
    ];
};

/**
 * @return array{term: Term, matching: Student, differentValue: Student, differentTerm: Student, withoutTermAttributes: Student}
 */
$createStudents = function (): array {
    $term = Term::factory()->create();
    $otherTerm = Term::factory()->create();

    $matching = Student::factory()->create();
    StudentTermAttribute::factory()->for($matching)->create(['sis_term_id' => $term->sis_term_id, 'campus' => 'Main']);

    $differentValue = Student::factory()->create();
    StudentTermAttribute::factory()->for($differentValue)->create(['sis_term_id' => $term->sis_term_id, 'campus' => 'Online']);

    $differentTerm = Student::factory()->create();
    StudentTermAttribute::factory()->for($differentTerm)->create(['sis_term_id' => $otherTerm->sis_term_id, 'campus' => 'Main']);

    return [
        'term' => $term,
        'matching' => $matching,
        'differentValue' => $differentValue,
        'differentTerm' => $differentTerm,
        'withoutTermAttributes' => Student::factory()->create(),
    ];
};

it('filters the group builder by term, attribute and value', function () use ($termAttributeFilters, $createStudents) {
    ['term' => $term, 'matching' => $matching, 'differentValue' => $differentValue, 'differentTerm' => $differentTerm, 'withoutTermAttributes' => $withoutTermAttributes] = $createStudents();

    $group = Group::factory()->student()->dynamic()->create([
        'filters' => $termAttributeFilters($term->sis_term_id, 'campus', 'Main'),
    ]);

    livewire(EditGroup::class, ['record' => $group->getRouteKey()])
        ->assertCanSeeTableRecords([$matching])
        ->assertCanNotSeeTableRecords([$differentValue, $differentTerm, $withoutTermAttributes]);
});

it('excludes the matching students from the group builder with the `is not` operator', function () use ($termAttributeFilters, $createStudents) {
    ['term' => $term, 'matching' => $matching, 'differentValue' => $differentValue, 'differentTerm' => $differentTerm, 'withoutTermAttributes' => $withoutTermAttributes] = $createStudents();

    $group = Group::factory()->student()->dynamic()->create([
        'filters' => $termAttributeFilters($term->sis_term_id, 'campus', 'Main', isInverse: true),
    ]);

    livewire(EditGroup::class, ['record' => $group->getRouteKey()])
        ->assertCanSeeTableRecords([$differentValue, $differentTerm, $withoutTermAttributes])
        ->assertCanNotSeeTableRecords([$matching]);
});

it('filters the students of a saved group', function () use ($termAttributeFilters, $createStudents) {
    ['term' => $term, 'matching' => $matching] = $createStudents();

    $group = Group::factory()->student()->dynamic()->create([
        'filters' => $termAttributeFilters($term->sis_term_id, 'campus', 'Main'),
    ]);

    expect(app(TranslateGroupFilters::class)->execute($group)->pluck('sisid')->all())
        ->toBe([$matching->sisid]);
});

it('matches no students for an attribute outside the allow-list', function () use ($termAttributeFilters, $createStudents) {
    ['term' => $term, 'matching' => $matching] = $createStudents();

    $group = Group::factory()->student()->dynamic()->create([
        'filters' => $termAttributeFilters($term->sis_term_id, 'sisid', $matching->sisid),
    ]);

    expect(app(TranslateGroupFilters::class)->execute($group)->count())->toBe(0);
});

it('summarises the rule', function (bool $isInverse, string $summary) {
    Term::factory()->create(['sis_term_id' => '301', 'name' => 'FA-21', 'start_date' => '2021-08-23']);

    $operator = TermAttributeOperator::make()
        ->settings([
            'sis_term_id' => '301',
            'attribute' => StudentTermAttributeField::Campus,
            'value' => 'Main',
        ])
        ->inverse($isInverse);

    expect($operator->getSummary())->toBe($summary);
})->with([
    'is' => [false, 'FA-21 (Aug 2021): Campus is "Main"'],
    'is not' => [true, 'FA-21 (Aug 2021): Campus is not "Main"'],
]);
