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

use AdvisingApp\StudentDataModel\Enums\StudentTermAttributeField;
use AdvisingApp\StudentDataModel\Filament\Filters\TermAttributeFilter;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentTermAttribute;
use AdvisingApp\StudentDataModel\Models\Term;
use Filament\Forms\Components\Select;

use function Tests\asSuperAdmin;

$isRequiredSelect = function (mixed $field): bool {
    assert($field instanceof Select);

    return $field->isRequired();
};

it('does not require the fields on the students list filter', function () use ($isRequiredSelect) {
    $fields = collect(TermAttributeFilter::make()->getSchemaComponents());

    expect($fields)->toHaveCount(3)
        ->and($fields->filter($isRequiredSelect))->toBeEmpty();
});

it('requires the fields in group rules', function () use ($isRequiredSelect) {
    $fields = collect(TermAttributeFilter::getFormSchema());

    expect($fields)->toHaveCount(3)
        ->and($fields->reject($isRequiredSelect))->toBeEmpty();
});

it('only offers the terms that have term attributes, latest first', function () {
    $olderTerm = Term::factory()->create(['sis_term_id' => '268', 'name' => 'FA-20', 'start_date' => '2020-08-24']);
    $latestTerm = Term::factory()->create(['sis_term_id' => '301', 'name' => 'FA-21', 'start_date' => '2021-08-23']);
    $undatedTerm = Term::factory()->create(['sis_term_id' => '302', 'name' => 'Summer TBD', 'start_date' => null]);
    Term::factory()->create(['sis_term_id' => '303', 'name' => 'SP-22', 'start_date' => '2022-01-10']);

    foreach ([$olderTerm, $latestTerm, $undatedTerm] as $term) {
        StudentTermAttribute::factory()->create(['sis_term_id' => $term->sis_term_id]);
    }

    $options = TermAttributeFilter::getTermOptions();

    expect(array_map(strval(...), array_keys($options)))->toBe(['301', '268', '302'])
        ->and(array_values($options))->toBe(['FA-21 (Aug 2021)', 'FA-20 (Aug 2020)', 'Summer TBD']);
});

it('offers the existing values of the selected attribute for the selected term', function () {
    $term = Term::factory()->create();
    $otherTerm = Term::factory()->create();

    StudentTermAttribute::factory()->create(['sis_term_id' => $term->sis_term_id, 'campus' => 'Online']);
    StudentTermAttribute::factory()->create(['sis_term_id' => $term->sis_term_id, 'campus' => 'Main']);
    StudentTermAttribute::factory()->create(['sis_term_id' => $term->sis_term_id, 'campus' => 'Main']);
    StudentTermAttribute::factory()->create(['sis_term_id' => $term->sis_term_id, 'campus' => null]);
    StudentTermAttribute::factory()->create(['sis_term_id' => $otherTerm->sis_term_id, 'campus' => 'Satellite']);

    expect(TermAttributeFilter::getValueOptions($term->sis_term_id, StudentTermAttributeField::Campus))->toBe(['Main' => 'Main', 'Online' => 'Online'])
        ->and(TermAttributeFilter::getValueOptions($term->sis_term_id, 'campus'))->toBe(['Main' => 'Main', 'Online' => 'Online'])
        ->and(TermAttributeFilter::getValueOptions(null, StudentTermAttributeField::Campus))->toBe([])
        ->and(TermAttributeFilter::getValueOptions($term->sis_term_id, 'sisid'))->toBe([]);
});

it('filters by the attribute whether it is given as an enum or a string', function (StudentTermAttributeField | string $attribute) {
    asSuperAdmin();

    $term = Term::factory()->create();

    $matching = Student::factory()->create();
    StudentTermAttribute::factory()->for($matching)->create(['sis_term_id' => $term->sis_term_id, 'campus' => 'Main']);

    $notMatching = Student::factory()->create();
    StudentTermAttribute::factory()->for($notMatching)->create(['sis_term_id' => $term->sis_term_id, 'campus' => 'Online']);

    expect(TermAttributeFilter::applyToQuery(Student::query(), $term->sis_term_id, $attribute, 'Main')->pluck('sisid')->all())
        ->toBe([$matching->sisid]);
})->with([
    'enum' => [StudentTermAttributeField::Campus],
    'string' => ['campus'],
]);

it('matches no students for an attribute outside the allow-list', function () {
    asSuperAdmin();

    $student = Student::factory()->create();

    expect(TermAttributeFilter::applyToQuery(Student::query(), '267', 'sisid', $student->sisid)->count())->toBe(0);
});

it('does not filter until the term, attribute and value are all selected', function () {
    asSuperAdmin();

    Student::factory()->count(2)->create();

    expect(TermAttributeFilter::applyToQuery(Student::query(), '267', 'campus', null)->count())->toBe(2);
});

it('summarises the filter', function () {
    Term::factory()->create(['sis_term_id' => '301', 'name' => 'FA-21', 'start_date' => '2021-08-23']);

    expect(TermAttributeFilter::getSummary('301', StudentTermAttributeField::Campus, 'Main'))->toBe('FA-21 (Aug 2021): Campus is "Main"')
        ->and(TermAttributeFilter::getSummary('999', 'campus', 'Main', isInverse: true))->toBe('Unknown Term: Campus is not "Main"');
});
