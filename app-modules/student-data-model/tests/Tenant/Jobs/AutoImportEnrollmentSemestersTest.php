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

use AdvisingApp\StudentDataModel\Enums\EnrollmentSemesterAutoImportDefaultOrder;
use AdvisingApp\StudentDataModel\Events\SisSyncCompleted;
use AdvisingApp\StudentDataModel\Jobs\AutoImportEnrollmentSemesters;
use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\EnrollmentSemester;
use AdvisingApp\StudentDataModel\Settings\StudentInformationSystemSettings;
use Illuminate\Support\Facades\Bus;

it('is dispatched when a SIS sync completes', function () {
    Bus::fake([AutoImportEnrollmentSemesters::class]);

    SisSyncCompleted::dispatch();

    Bus::assertDispatched(AutoImportEnrollmentSemesters::class);
});

it('does not run when auto import is disabled', function () {
    $settings = app(StudentInformationSystemSettings::class);
    $settings->is_enrollment_semester_auto_import_enabled = false;
    $settings->save();

    Enrollment::factory()->create(['semester_name' => 'Fall 2024']);

    (new AutoImportEnrollmentSemesters())->handle();

    expect(EnrollmentSemester::count())->toBe(0);
});

it('does nothing when there are no unsynced semesters', function () {
    $settings = app(StudentInformationSystemSettings::class);
    $settings->is_enrollment_semester_auto_import_enabled = true;
    $settings->save();

    EnrollmentSemester::create(['name' => 'Fall 2024']);
    Enrollment::factory()->create(['semester_name' => 'Fall 2024']);

    (new AutoImportEnrollmentSemesters())->handle();

    expect(EnrollmentSemester::count())->toBe(1);
});

it('imports unsynced semesters with order first placing them at the top', function () {
    $settings = app(StudentInformationSystemSettings::class);
    $settings->is_enrollment_semester_auto_import_enabled = true;
    $settings->enrollment_semester_auto_import_default_order = EnrollmentSemesterAutoImportDefaultOrder::First;
    $settings->save();

    EnrollmentSemester::create(['name' => 'Spring 2024', 'order' => 1]);
    EnrollmentSemester::create(['name' => 'Fall 2024', 'order' => 2]);

    Enrollment::factory()->create(['semester_name' => 'Spring 2025']);
    Enrollment::factory()->create(['semester_name' => 'Fall 2025']);

    (new AutoImportEnrollmentSemesters())->handle();

    expect(EnrollmentSemester::count())->toBe(4);

    $spring2025 = EnrollmentSemester::where('name', 'Spring 2025')->first();
    $fall2025 = EnrollmentSemester::where('name', 'Fall 2025')->first();
    $fall2024 = EnrollmentSemester::where('name', 'Fall 2024')->first();
    $spring2024 = EnrollmentSemester::where('name', 'Spring 2024')->first();

    expect($spring2025->order)->toBeGreaterThan($fall2024->order);
    expect($fall2025->order)->toBeGreaterThan($fall2024->order);
    expect($fall2024->order)->toBe(2);
    expect($spring2024->order)->toBe(1);
});

it('imports unsynced semesters with order last placing them at the bottom', function () {
    $settings = app(StudentInformationSystemSettings::class);
    $settings->is_enrollment_semester_auto_import_enabled = true;
    $settings->enrollment_semester_auto_import_default_order = EnrollmentSemesterAutoImportDefaultOrder::Last;
    $settings->save();

    EnrollmentSemester::create(['name' => 'Spring 2024', 'order' => 1]);
    EnrollmentSemester::create(['name' => 'Fall 2024', 'order' => 2]);

    Enrollment::factory()->create(['semester_name' => 'Spring 2025']);
    Enrollment::factory()->create(['semester_name' => 'Fall 2025']);

    (new AutoImportEnrollmentSemesters())->handle();

    expect(EnrollmentSemester::count())->toBe(4);

    $spring2025 = EnrollmentSemester::where('name', 'Spring 2025')->first();
    $fall2025 = EnrollmentSemester::where('name', 'Fall 2025')->first();
    $fall2024 = EnrollmentSemester::where('name', 'Fall 2024')->first();
    $spring2024 = EnrollmentSemester::where('name', 'Spring 2024')->first();

    expect($fall2024->order)->toBeGreaterThan($spring2025->order);
    expect($fall2024->order)->toBeGreaterThan($fall2025->order);
    expect($spring2024->order)->toBeGreaterThan($spring2025->order);
    expect($spring2024->order)->toBeGreaterThan($fall2025->order);
});

it('preserves relative order of existing semesters when placing new ones last', function () {
    $settings = app(StudentInformationSystemSettings::class);
    $settings->is_enrollment_semester_auto_import_enabled = true;
    $settings->enrollment_semester_auto_import_default_order = EnrollmentSemesterAutoImportDefaultOrder::Last;
    $settings->save();

    EnrollmentSemester::create(['name' => 'Spring 2024', 'order' => 1]);
    EnrollmentSemester::create(['name' => 'Summer 2024', 'order' => 2]);
    EnrollmentSemester::create(['name' => 'Fall 2024', 'order' => 3]);

    Enrollment::factory()->create(['semester_name' => 'Spring 2025']);

    (new AutoImportEnrollmentSemesters())->handle();

    $fall2024 = EnrollmentSemester::where('name', 'Fall 2024')->first();
    $summer2024 = EnrollmentSemester::where('name', 'Summer 2024')->first();
    $spring2024 = EnrollmentSemester::where('name', 'Spring 2024')->first();
    $spring2025 = EnrollmentSemester::where('name', 'Spring 2025')->first();

    expect($fall2024->order)->toBeGreaterThan($summer2024->order);
    expect($summer2024->order)->toBeGreaterThan($spring2024->order);
    expect($spring2024->order)->toBeGreaterThan($spring2025->order);
});

it('skips enrollments with null semester name', function () {
    $settings = app(StudentInformationSystemSettings::class);
    $settings->is_enrollment_semester_auto_import_enabled = true;
    $settings->save();

    Enrollment::factory()->create(['semester_name' => null]);
    Enrollment::factory()->create(['semester_name' => 'Fall 2024']);

    (new AutoImportEnrollmentSemesters())->handle();

    expect(EnrollmentSemester::count())->toBe(1);
    expect(EnrollmentSemester::first()->name)->toBe('Fall 2024');
});

it('does not duplicate already synced semesters', function () {
    $settings = app(StudentInformationSystemSettings::class);
    $settings->is_enrollment_semester_auto_import_enabled = true;
    $settings->save();

    EnrollmentSemester::create(['name' => 'Fall 2024']);
    Enrollment::factory()->create(['semester_name' => 'Fall 2024']);
    Enrollment::factory()->create(['semester_name' => 'Spring 2025']);

    (new AutoImportEnrollmentSemesters())->handle();

    expect(EnrollmentSemester::count())->toBe(2);
    expect(EnrollmentSemester::where('name', 'Fall 2024')->count())->toBe(1);
});
