<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\CareTeam\Models\CareTeam;
use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\Scopes\RetentionCrmRestrictionScope;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Enums\RetentionCrmRestriction;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('Student model has applied global scope', function () {
    Student::bootHasGlobalScopes();

    expect(Student::hasGlobalScope(RetentionCrmRestrictionScope::class))->toBeTrue();
});

test('scope does not filter when user has no restriction', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create([
        'retention_crm_restriction' => null,
    ]);

    actingAs($user);

    Student::factory()->count(5)->create();

    expect(Student::query()->count())->toBe(5);
});

test('faculty member restriction only shows students enrolled in their classes', function () {
    $facultyEmail = 'faculty@example.com';

    $user = User::factory()->licensed(LicenseType::cases())->create([
        'email' => $facultyEmail,
        'retention_crm_restriction' => RetentionCrmRestriction::FacultyMember,
    ]);

    actingAs($user);

    // Students with enrollments taught by this faculty member
    $enrolledStudents = Student::factory()->count(3)->create();

    foreach ($enrolledStudents as $student) {
        Enrollment::factory()
            ->for($student, 'student')
            ->create([
                'faculty_email' => $facultyEmail,
            ]);
    }

    // Students with enrollments taught by a different faculty member
    $otherStudents = Student::factory()->count(2)->create();

    foreach ($otherStudents as $student) {
        Enrollment::factory()
            ->for($student, 'student')
            ->create([
                'faculty_email' => 'other.faculty@example.com',
            ]);
    }

    // Students with no enrollments
    Student::factory()->count(2)->create();

    $visibleStudents = Student::query()->get();

    expect($visibleStudents)->toHaveCount(3);
    expect($visibleStudents->pluck('sisid')->toArray())
        ->toEqualCanonicalizing($enrolledStudents->pluck('sisid')->toArray());
});

test('faculty member restriction is case insensitive for email matching', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create([
        'email' => 'Faculty@Example.COM',
        'retention_crm_restriction' => RetentionCrmRestriction::FacultyMember,
    ]);

    actingAs($user);

    $student = Student::factory()->create();
    Enrollment::factory()
        ->for($student, 'student')
        ->create([
            'faculty_email' => 'faculty@example.com',
        ]);

    expect(Student::query()->count())->toBe(1);
});

test('care team member restriction only shows students in their care team', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create([
        'retention_crm_restriction' => RetentionCrmRestriction::CareTeamMember,
    ]);

    actingAs($user);

    // Students in this user's care team
    $careTeamStudents = Student::factory()->count(3)->create();

    foreach ($careTeamStudents as $student) {
        CareTeam::factory()
            ->for($student, 'educatable')
            ->for($user)
            ->createQuietly();
    }

    // Students in another user's care team
    $otherUser = User::factory()->licensed(LicenseType::cases())->create();
    $otherCareTeamStudents = Student::factory()->count(2)->create();

    foreach ($otherCareTeamStudents as $student) {
        CareTeam::factory()
            ->for($student, 'educatable')
            ->for($otherUser)
            ->createQuietly();
    }

    // Students not in any care team
    Student::factory()->count(2)->create();

    $visibleStudents = Student::query()->get();

    expect($visibleStudents)->toHaveCount(3);
    expect($visibleStudents->pluck('sisid')->toArray())
        ->toEqualCanonicalizing($careTeamStudents->pluck('sisid')->toArray());
});

test('scope does not apply when user is not authenticated', function () {
    Student::factory()->count(5)->create();

    expect(Student::query()->count())->toBe(5);
});
