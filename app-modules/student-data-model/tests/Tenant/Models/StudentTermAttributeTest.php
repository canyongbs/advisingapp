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

use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentTermAttribute;
use AdvisingApp\StudentDataModel\Models\Term;
use Illuminate\Database\UniqueConstraintViolationException;

use function Tests\asSuperAdmin;

it('belongs to a student by `sisid`', function () {
    asSuperAdmin();

    $student = Student::factory()->create();
    $termAttribute = StudentTermAttribute::factory()->for($student)->create();

    expect($termAttribute->student->getKey())->toBe($student->getKey());
});

it('belongs to a term by `sis_term_id`', function () {
    $term = Term::factory()->create();
    $termAttribute = StudentTermAttribute::factory()->create(['sis_term_id' => $term->sis_term_id]);

    expect($termAttribute->term->sis_term_id)->toBe($term->sis_term_id);
});

it('does not allow two records for the same student and term', function () {
    $termAttribute = StudentTermAttribute::factory()->create();

    StudentTermAttribute::factory()->create([
        'sisid' => $termAttribute->sisid,
        'sis_term_id' => $termAttribute->sis_term_id,
    ]);
})->throws(UniqueConstraintViolationException::class);
