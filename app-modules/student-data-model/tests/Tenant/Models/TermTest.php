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
use AdvisingApp\StudentDataModel\Models\StudentTermAttribute;
use AdvisingApp\StudentDataModel\Models\Term;
use Illuminate\Database\UniqueConstraintViolationException;

it('links student term attributes by `sis_term_id`', function () {
    $term = Term::factory()->create();
    $termAttributes = StudentTermAttribute::factory()->count(2)->create(['sis_term_id' => $term->sis_term_id]);
    $otherTermAttribute = StudentTermAttribute::factory()->create();

    expect($term->studentTermAttributes->pluck('sisid')->all())
        ->toEqualCanonicalizing($termAttributes->pluck('sisid')->all())
        ->not->toContain($otherTermAttribute->sisid);
});

it('links enrollments by `sis_term_id`', function () {
    $term = Term::factory()->create();
    $enrollments = Enrollment::factory()->count(2)->create(['sis_term_id' => $term->sis_term_id]);
    $otherEnrollment = Enrollment::factory()->create(['sis_term_id' => Term::factory()->create()->sis_term_id]);

    expect($term->enrollments->pluck('sisid')->all())
        ->toEqualCanonicalizing($enrollments->pluck('sisid')->all())
        ->not->toContain($otherEnrollment->sisid);
});

it('allows different terms to share the same name', function () {
    Term::factory()->count(2)->create(['name' => 'FA-20']);

    expect(Term::query()->where('name', 'FA-20')->count())->toBe(2);
});

it('does not allow two terms with the same `sis_term_id`', function () {
    $term = Term::factory()->create();

    Term::factory()->create(['sis_term_id' => $term->sis_term_id]);
})->throws(UniqueConstraintViolationException::class);

it('includes the start month in the display name', function () {
    $term = Term::factory()->create([
        'name' => 'FA-20',
        'start_date' => '2020-09-14',
    ]);

    expect($term->getDisplayName())->toBe('FA-20 (Sep 2020)');
});

it('uses only the name as the display name when the term has no start date', function () {
    $term = Term::factory()->create([
        'name' => 'Summer TBD',
        'start_date' => null,
    ]);

    expect($term->getDisplayName())->toBe('Summer TBD');
});
