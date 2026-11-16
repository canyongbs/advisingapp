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
use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\Tables\StudentsTable;
use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentAddress;
use AdvisingApp\StudentDataModel\Models\StudentPhoneNumber;
use AdvisingApp\StudentDataModel\Models\Term;
use App\Features\MatchedEnrolledTermsFeature;
use App\Features\StudentGroupFilterConsolidationFeature;
use Filament\QueryBuilder\Constraints\Constraint;
use Filament\QueryBuilder\Constraints\SelectConstraint;
use Filament\QueryBuilder\Models\Scopes\QueryBuilderScope;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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

it('does not offer enrolled terms that only archived students have when the feature is inactive', function () use ($enrolledTermConstraintOptions): void {
    MatchedEnrolledTermsFeature::deactivate();

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

describe('enrolled term constraints', function () use ($enrolledTermConstraintOptions): void {
    /**
     * @param array<string> $values
     *
     * @return array<int, string>
     */
    $studentsMatchingEnrolledTerms = fn (string $type, array $values, bool $isInverse = false): array => QueryBuilderScope::make(
        [
            'rule' => [
                'type' => $type,
                'data' => [
                    'operator' => $isInverse ? 'is.inverse' : 'is',
                    'settings' => ['values' => $values],
                ],
            ],
        ],
        Arr::flatten(StudentsTable::getQueryBuilderConstraints()),
    )(Student::query())
        ->pluck((new Student())->getKeyName())
        ->all();

    /**
     * @return array{firstFallSecondSpring: Student, firstSpringSecondFall: Student, withoutMatchedTerms: Student, withoutEnrollments: Student}
     */
    $createStudents = function (): array {
        $earliestTerm = Term::factory()->create(['name' => 'SU-19', 'start_date' => '2019-06-01']);
        $fallTerm = Term::factory()->create(['name' => 'FA-20', 'start_date' => '2020-09-01']);
        $springTerm = Term::factory()->create(['name' => 'SP-21', 'start_date' => '2021-01-15']);
        $laterFallTerm = Term::factory()->create(['name' => 'FA-21', 'start_date' => '2021-09-01']);

        $enroll = fn (Student $student, array $attributes): Enrollment => Enrollment::factory()->for($student, 'student')->create([
            'id' => (string) Str::uuid(),
            'sis_term_id' => null,
            'semester_name' => null,
            ...$attributes,
        ]);

        $firstFallSecondSpring = Student::factory()->create();
        $enroll($firstFallSecondSpring, ['sis_term_id' => $earliestTerm->sis_term_id])->delete();
        $enroll($firstFallSecondSpring, ['semester_name' => 'Unmatched', 'start_date' => '2000-01-01']);
        $enroll($firstFallSecondSpring, ['sis_term_id' => $fallTerm->sis_term_id]);
        $enroll($firstFallSecondSpring, ['semester_name' => 'sp-21']);

        $firstSpringSecondFall = Student::factory()->create();
        $enroll($firstSpringSecondFall, ['sis_term_id' => $springTerm->sis_term_id]);
        $enroll($firstSpringSecondFall, ['sis_term_id' => $laterFallTerm->sis_term_id]);

        $withoutMatchedTerms = Student::factory()->create();
        $enroll($withoutMatchedTerms, ['semester_name' => 'Unmatched', 'semester_code' => '4201', 'start_date' => '2020-09-01']);

        return [
            'firstFallSecondSpring' => $firstFallSecondSpring,
            'firstSpringSecondFall' => $firstSpringSecondFall,
            'withoutMatchedTerms' => $withoutMatchedTerms,
            'withoutEnrollments' => Student::factory()->create(),
        ];
    };

    it('offers the names of the terms, most recent first, listing names differing only in case once', function () use ($enrolledTermConstraintOptions): void {
        Term::factory()->create(['name' => 'FA-20', 'start_date' => '2020-09-01']);
        Term::factory()->create(['name' => 'fa-20', 'start_date' => '2020-08-24']);
        Term::factory()->create(['name' => 'SP-21', 'start_date' => '2021-01-15']);
        Term::factory()->withoutSisTermId()->create(['name' => 'Manual Term']);
        Term::factory()->create(['name' => 'Deleted Term', 'deleted_at' => now()]);

        $expectedOptions = [
            'sp-21' => 'SP-21',
            'fa-20' => 'FA-20',
            'manual term' => 'Manual Term',
        ];

        expect($enrolledTermConstraintOptions('firstEnrolledTerm'))->toBe($expectedOptions)
            ->and($enrolledTermConstraintOptions('mostRecentEnrolledTerm'))->toBe($expectedOptions);
    });

    it('filters by the name of the first matched term, ignoring case', function () use ($studentsMatchingEnrolledTerms, $createStudents): void {
        ['firstFallSecondSpring' => $firstFallSecondSpring, 'firstSpringSecondFall' => $firstSpringSecondFall] = $createStudents();

        expect($studentsMatchingEnrolledTerms('firstEnrolledTerm', ['fa-20']))
            ->toBe([$firstFallSecondSpring->getKey()])
            ->and($studentsMatchingEnrolledTerms('firstEnrolledTerm', ['SP-21']))
            ->toBe([$firstSpringSecondFall->getKey()])
            ->and($studentsMatchingEnrolledTerms('firstEnrolledTerm', ['su-19', 'fa-21']))
            ->toBe([]);
    });

    it('filters by the name of the most recent matched term, ignoring case', function () use ($studentsMatchingEnrolledTerms, $createStudents): void {
        ['firstFallSecondSpring' => $firstFallSecondSpring, 'firstSpringSecondFall' => $firstSpringSecondFall] = $createStudents();

        expect($studentsMatchingEnrolledTerms('mostRecentEnrolledTerm', ['sp-21']))
            ->toBe([$firstFallSecondSpring->getKey()])
            ->and($studentsMatchingEnrolledTerms('mostRecentEnrolledTerm', ['FA-21']))
            ->toBe([$firstSpringSecondFall->getKey()]);
    });

    it('excludes students whose enrolled term has one of the names, leaving out students without a matched term', function () use ($studentsMatchingEnrolledTerms, $createStudents): void {
        ['firstFallSecondSpring' => $firstFallSecondSpring, 'firstSpringSecondFall' => $firstSpringSecondFall] = $createStudents();

        expect($studentsMatchingEnrolledTerms('firstEnrolledTerm', ['fa-20'], isInverse: true))
            ->toBe([$firstSpringSecondFall->getKey()])
            ->and($studentsMatchingEnrolledTerms('mostRecentEnrolledTerm', ['sp-21'], isInverse: true))
            ->toBe([$firstSpringSecondFall->getKey()]);
    });

    it('ignores a selection that is not a list of term names, as live report filters come from the URL', function () use ($createStudents): void {
        ['firstFallSecondSpring' => $firstFallSecondSpring, 'firstSpringSecondFall' => $firstSpringSecondFall, 'withoutMatchedTerms' => $withoutMatchedTerms, 'withoutEnrollments' => $withoutEnrollments] = $createStudents();

        $students = app(TranslateGroupFilters::class)->executeRawFilters(GroupModel::Student, [
            'queryBuilder' => [
                'rules' => [
                    'aBcD' => [
                        'type' => 'firstEnrolledTerm',
                        'data' => ['operator' => 'is', 'settings' => ['values' => 'fa-20']],
                    ],
                ],
            ],
        ]);

        expect($students->pluck('sisid')->all())->toEqualCanonicalizing([
            $firstFallSecondSpring->getKey(),
            $firstSpringSecondFall->getKey(),
            $withoutMatchedTerms->getKey(),
            $withoutEnrollments->getKey(),
        ]);
    });

    it('resolves the members of a saved group', function () use ($createStudents): void {
        asSuperAdmin();

        ['firstFallSecondSpring' => $firstFallSecondSpring] = $createStudents();

        $group = Group::factory()->student()->dynamic()->create([
            'filters' => [
                'queryBuilder' => [
                    'rules' => [
                        'aBcD' => [
                            'type' => 'firstEnrolledTerm',
                            'data' => ['operator' => 'is', 'settings' => ['values' => ['fa-20']]],
                        ],
                        'eFgH' => [
                            'type' => 'mostRecentEnrolledTerm',
                            'data' => ['operator' => 'is', 'settings' => ['values' => ['sp-21']]],
                        ],
                    ],
                ],
            ],
        ]);

        expect(app(TranslateGroupFilters::class)->execute($group)->pluck('sisid')->all())
            ->toBe([$firstFallSecondSpring->getKey()]);
    });

    it('filters by the semester name of the first and most recent enrollments when the feature is inactive', function () use ($studentsMatchingEnrolledTerms): void {
        MatchedEnrolledTermsFeature::deactivate();

        $student = Student::factory()->create();
        Enrollment::factory()->for($student, 'student')->create(['semester_code' => '4201', 'semester_name' => 'Fall 2020', 'start_date' => '2020-09-01']);
        Enrollment::factory()->for($student, 'student')->create(['semester_code' => '4202', 'semester_name' => 'Spring 2021', 'start_date' => '2021-01-15']);

        Student::factory()->create();

        expect($studentsMatchingEnrolledTerms('firstEnrollmentTerm.semester_name', ['fall 2020']))
            ->toBe([$student->getKey()])
            ->and($studentsMatchingEnrolledTerms('mostRecentEnrollmentTerm.semester_name', ['spring 2021']))
            ->toBe([$student->getKey()])
            ->and($studentsMatchingEnrolledTerms('firstEnrollmentTerm.semester_name', ['spring 2021']))
            ->toBe([]);
    });
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
