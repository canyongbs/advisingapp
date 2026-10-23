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

use AdvisingApp\StudentDataModel\Filament\Resources\Students\Tables\StudentsTable;
use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentAddress;
use AdvisingApp\StudentDataModel\Models\StudentPhoneNumber;
use App\Features\StudentGroupFilterConsolidationFeature;
use Filament\QueryBuilder\Constraints\Constraint;
use Filament\QueryBuilder\Constraints\SelectConstraint;
use Filament\QueryBuilder\Models\Scopes\QueryBuilderScope;
use Illuminate\Support\Arr;

use function Tests\asSuperAdmin;

/**
 * @return array<int, string>
 */
$studentsMatchingRule = function (string $type, string $attribute, string $operator, string $text): array {
    $rules = [
        'rule' => [
            'type' => $type,
            'data' => [
                'operator' => $attribute,
                'settings' => [
                    'operator' => $operator,
                    'text' => $text,
                ],
            ],
        ],
    ];

    return QueryBuilderScope::make($rules, Arr::flatten(StudentsTable::getQueryBuilderConstraints()))(Student::query())
        ->pluck((new Student())->getKeyName())
        ->all();
};

$setPrimaryPhoneNumber = function (Student $student, string $number): void {
    $phoneNumber = $student->primaryPhoneNumber;
    assert($phoneNumber instanceof StudentPhoneNumber);

    $phoneNumber->number = $number;
    $phoneNumber->saveQuietly();
};

/**
 * @return array<string, string>
 */
$enrolledTermConstraintOptions = function (string $name): array {
    $constraint = collect(StudentsTable::getQueryBuilderConstraints())
        ->flatten()
        ->firstOrFail(fn (Constraint $constraint): bool => $constraint->getName() === $name);
    assert($constraint instanceof SelectConstraint);

    return $constraint->getOptions();
};

it('does not offer enrolled terms that only archived students have', function () use ($enrolledTermConstraintOptions): void {
    asSuperAdmin();

    $active = Student::factory()->create();
    Enrollment::factory()->for($active, 'student')->create([
        'semester_name' => 'Active Term',
        'start_date' => now()->subYear(),
    ]);

    $archived = Student::factory()->create();
    Enrollment::factory()->for($archived, 'student')->create([
        'semester_name' => 'Archived Only Term',
        'start_date' => now()->subYear(),
    ]);

    expect($enrolledTermConstraintOptions('firstEnrollmentTerm.semester_name'))
        ->toContain('Active Term')
        ->toContain('Archived Only Term');

    $archived->archive();

    expect($enrolledTermConstraintOptions('firstEnrollmentTerm.semester_name'))
        ->toContain('Active Term')
        ->not->toContain('Archived Only Term');
});

it('filters by the selected name attribute', function (string $attribute, string $column) use ($studentsMatchingRule): void {
    $matching = Student::factory()->create([$column => 'Abernathy']);
    $other = Student::factory()->create([$column => 'Zimmerman']);

    expect($studentsMatchingRule('name', $attribute, 'startsWith', 'aber'))
        ->toBe([$matching->getKey()])
        ->and($studentsMatchingRule('name', $attribute, 'startsWith.inverse', 'aber'))
        ->toBe([$other->getKey()]);
})->with([
    'full name' => ['fullName', 'full_name'],
    'first name' => ['firstName', 'first'],
    'last name' => ['lastName', 'last'],
    'preferred name' => ['preferredName', 'preferred'],
]);

it('filters by the selected primary address attribute', function (string $attribute) use ($studentsMatchingRule): void {
    $matching = Student::factory()->create();
    $matchingAddress = $matching->primaryAddress;
    assert($matchingAddress instanceof StudentAddress);
    $matchingAddress->{$attribute} = 'Springfield';
    $matchingAddress->save();

    $secondaryOnly = Student::factory()->create();
    StudentAddress::factory()->create([
        'sisid' => $secondaryOnly->getKey(),
        $attribute => 'Springfield',
        'order' => 2,
    ]);

    expect($studentsMatchingRule('primaryAddress', $attribute, 'equals', 'springfield'))
        ->toBe([$matching->getKey()]);
})->with(['city', 'state', 'postal', 'country']);

it('filters by the primary phone country code', function () use ($studentsMatchingRule, $setPrimaryPhoneNumber): void {
    $unitedStates = Student::factory()->create();
    $setPrimaryPhoneNumber($unitedStates, '+12025550123');

    $unitedKingdom = Student::factory()->create();
    $setPrimaryPhoneNumber($unitedKingdom, '+442071234567');

    expect($studentsMatchingRule('primaryPhone', 'countryCode', 'equals', '+44'))
        ->toBe([$unitedKingdom->getKey()])
        ->and($studentsMatchingRule('primaryPhone', 'countryCode', 'equals', '1'))
        ->toBe([$unitedStates->getKey()]);
});

it('filters by the primary phone area code of North American numbers only', function () use ($studentsMatchingRule, $setPrimaryPhoneNumber): void {
    $matching = Student::factory()->create();
    $setPrimaryPhoneNumber($matching, '+12025550123');

    $otherAreaCode = Student::factory()->create();
    $setPrimaryPhoneNumber($otherAreaCode, '+13125550123');

    $international = Student::factory()->create();
    $setPrimaryPhoneNumber($international, '+442021234567');

    expect($studentsMatchingRule('primaryPhone', 'areaCode', 'equals', '(202)'))
        ->toBe([$matching->getKey()])
        ->and($studentsMatchingRule('primaryPhone', 'areaCode', 'equals.inverse', '202'))
        ->toBe([$otherAreaCode->getKey()]);
});

it('does not filter by phone numbers other than the primary one', function () use ($studentsMatchingRule, $setPrimaryPhoneNumber): void {
    $student = Student::factory()->create();
    $setPrimaryPhoneNumber($student, '+12025550123');

    StudentPhoneNumber::factory()->createQuietly([
        'sisid' => $student->getKey(),
        'number' => '+442071234567',
        'order' => 2,
    ]);

    expect($studentsMatchingRule('primaryPhone', 'countryCode', 'equals', '44'))->toBeEmpty();
});

it('does not match any phone number when the value has no digits', function (string $operator) use ($studentsMatchingRule, $setPrimaryPhoneNumber): void {
    $student = Student::factory()->create();
    $setPrimaryPhoneNumber($student, '+12025550123');

    expect($studentsMatchingRule('primaryPhone', 'areaCode', $operator, 'abc'))->toBeEmpty();
})->with(['contains', 'startsWith', 'endsWith', 'equals', 'contains.inverse']);

it('summarizes a rule by its attribute', function (): void {
    $constraint = collect(StudentsTable::getQueryBuilderConstraints())
        ->flatten()
        ->firstOrFail(fn (Constraint $constraint): bool => $constraint->getName() === 'name');

    $operator = $constraint->getOperator('lastName')
        ->constraint($constraint)
        ->settings(['operator' => 'startsWith', 'text' => 'A']);

    expect($operator->getSummary())->toBe('Last Name starts with A');
});

it('offers the legacy filters until the consolidation migration has run', function (): void {
    $constraintNames = fn (): array => collect(StudentsTable::getQueryBuilderConstraints())
        ->flatten()
        ->map(fn (Constraint $constraint): string => $constraint->getName())
        ->all();

    $legacyNames = ['full_name', 'first', 'last', 'preferred', 'sisid', 'otherid', 'email', 'phone', 'address', 'address_2', 'enrollmentSisid'];
    $consolidatedNames = ['name', 'primaryPhone', 'primaryAddress'];

    expect($constraintNames())->toContain(...$consolidatedNames)
        ->and(array_intersect($constraintNames(), $legacyNames))->toBeEmpty();

    StudentGroupFilterConsolidationFeature::deactivate();

    expect($constraintNames())->toContain(...$legacyNames)
        ->and(array_intersect($constraintNames(), $consolidatedNames))->toBeEmpty();
});
