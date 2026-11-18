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

use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\Scopes\JoinMatchedTerm;
use AdvisingApp\StudentDataModel\Models\Term;
use Illuminate\Support\Str;

$createEnrollment = fn (array $attributes): Enrollment => Enrollment::factory()->create([
    'id' => (string) Str::uuid(),
    'sis_term_id' => null,
    'semester_name' => null,
    ...$attributes,
]);

$createEnrollments = function () use ($createEnrollment): void {
    $sisTerm = Term::factory()->create(['name' => 'FA-20']);
    Term::factory()->withoutSisTermId()->create(['name' => 'fa-20']);
    Term::factory()->withoutSisTermId()->create(['name' => 'Manual Term']);
    $deletedTerm = Term::factory()->create(['name' => 'SP-21', 'deleted_at' => now()]);

    $createEnrollment(['sis_term_id' => $sisTerm->sis_term_id, 'semester_name' => 'Manual Term']);
    $createEnrollment(['semester_name' => 'FA-20']);
    $createEnrollment(['semester_name' => 'MANUAL TERM']);
    $createEnrollment(['sis_term_id' => 'unknown', 'semester_name' => 'manual term']);
    $createEnrollment(['sis_term_id' => $deletedTerm->sis_term_id, 'semester_name' => 'SP-21']);
    $createEnrollment(['semester_name' => 'Unmatched']);
    $createEnrollment([]);
};

/**
 * @return array<string, ?string>
 */
$matchedTermsFromView = fn (): array => Enrollment::query()
    ->with('term')
    ->get()
    ->mapWithKeys(fn (Enrollment $enrollment): array => [$enrollment->getKey() => $enrollment->term?->getKey()])
    ->all();

/**
 * @return array<string, ?string>
 */
$joinedTerms = fn (JoinMatchedTerm $scope): array => Enrollment::query()
    ->tap($scope)
    ->select(['enrollments.id as enrollment_id', 'terms.id as term_id'])
    ->pluck('term_id', 'enrollment_id')
    ->all();

it('joins enrollments to their matched terms, the same as the `enrollment_terms` view', function () use ($createEnrollments, $matchedTermsFromView, $joinedTerms): void {
    $createEnrollments();

    expect($joinedTerms(new JoinMatchedTerm()))
        ->toEqualCanonicalizing(array_filter($matchedTermsFromView()))
        ->toHaveCount(4);
});

it('keeps enrollments without a matched term when including unmatched ones', function () use ($createEnrollments, $matchedTermsFromView, $joinedTerms): void {
    $createEnrollments();

    expect($joinedTerms(new JoinMatchedTerm(includeUnmatched: true)))
        ->toEqualCanonicalizing($matchedTermsFromView())
        ->toHaveCount(7);
});
