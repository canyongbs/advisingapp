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

use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\StudentDataModel\Actions\ResolveEducatableFromEmail;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\StudentResource;
use AdvisingApp\StudentDataModel\Models\Scopes\WithoutArchivedStudents;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Features\StudentArchivingFeature;
use App\Filament\Forms\Components\EducatableSelect;
use Filament\Forms\Components\Select;
use Illuminate\Support\Carbon;

use function Tests\asSuperAdmin;

describe('archiving', function () {
    it('archives a student', function () {
        asSuperAdmin();

        $student = Student::factory()->create();

        expect($student->archived_at)->toBeNull()
            ->and($student->isArchived())->toBeFalse();

        expect($student->archive())->toBeTrue();

        $student->refresh();

        expect($student->archived_at)->toBeInstanceOf(Carbon::class)
            ->and($student->isArchived())->toBeTrue();
    });

    it('unarchives a student', function () {
        asSuperAdmin();

        $student = Student::factory()->create();
        $student->archive();

        expect($student->refresh()->isArchived())->toBeTrue();

        expect($student->unarchive())->toBeTrue();

        expect($student->refresh()->archived_at)->toBeNull()
            ->and($student->isArchived())->toBeFalse();
    });

    it('does not soft delete a student when it is archived', function () {
        asSuperAdmin();

        $student = Student::factory()->create();

        $student->archive();

        expect($student->refresh()->trashed())->toBeFalse();
    });

    it('includes archived students by default', function () {
        asSuperAdmin();

        $archived = Student::factory()->create();
        $archived->archive();

        expect(Student::query()->pluck('sisid'))->toContain($archived->getKey());
    });

    it('excludes archived students from `withoutArchived`', function () {
        asSuperAdmin();

        $active = Student::factory()->create();
        $archived = Student::factory()->create();
        $archived->archive();

        $sisids = Student::query()->withoutArchived()->pluck('sisid');

        expect($sisids)->toContain($active->getKey())
            ->and($sisids)->not->toContain($archived->getKey());
    });
});

describe('WithoutArchivedStudents scope', function () {
    it('excludes archived students while the feature is active', function () {
        asSuperAdmin();

        $active = Student::factory()->create();
        $archived = Student::factory()->create();
        $archived->archive();

        $sisids = Student::query()->tap(new WithoutArchivedStudents())->pluck('sisid');

        expect($sisids)->toContain($active->getKey())
            ->and($sisids)->not->toContain($archived->getKey());
    });

    it('leaves the query untouched while the feature is inactive', function () {
        asSuperAdmin();

        StudentArchivingFeature::deactivate();

        $archived = Student::factory()->create();
        $archived->archive();

        expect(Student::query()->tap(new WithoutArchivedStudents())->pluck('sisid'))
            ->toContain($archived->getKey());
    });
});

describe('discovery surfaces', function () {
    it('excludes archived students from global search', function () {
        asSuperAdmin();

        $active = Student::factory()->create(['full_name' => 'Searchable Alpha']);
        $archived = Student::factory()->create(['full_name' => 'Searchable Beta']);
        $archived->archive();

        $sisids = StudentResource::getGlobalSearchEloquentQuery()->pluck('sisid');

        expect($sisids)->toContain($active->getKey())
            ->and($sisids)->not->toContain($archived->getKey());
    });

    it('excludes archived students from the educatable select options', function () {
        asSuperAdmin();

        $active = Student::factory()->create();
        $archived = Student::factory()->create();
        $archived->archive();

        $type = EducatableSelect::getStudentType();
        $options = ($type->getOptionsUsing)(Select::make('educatable_id')->preload());

        // Numeric-string SIS IDs come back as integer array keys.
        $sisids = array_map(strval(...), array_keys($options));

        expect($sisids)->toContain($active->getKey())
            ->and($sisids)->not->toContain($archived->getKey());
    });

    // Filament resolves the label of the selected value through the same options query, so a
    // record already pointing at an archived student must keep showing that student's name.
    it('still resolves an archived student that is already selected', function () {
        asSuperAdmin();

        $archived = Student::factory()->create(['full_name' => 'Already Selected']);
        $archived->archive();

        $interaction = Interaction::factory()->for($archived, 'interactable')->create();

        $type = EducatableSelect::getStudentType('interactable_id', $interaction);

        expect(($type->getOptionLabelUsing)(Select::make('interactable_id'), $archived->getKey()))
            ->toBe('Already Selected')
            ->and((EducatableSelect::getStudentType()->getOptionLabelUsing)(Select::make('interactable_id'), $archived->getKey()))
            ->toBeNull();
    });
});

describe('sign in', function () {
    it('does not resolve an archived student from their email address', function () {
        asSuperAdmin();

        $student = Student::factory()->create();
        $email = $student->primaryEmailAddress->address;

        expect(app(ResolveEducatableFromEmail::class)($email))->not->toBeNull();

        $student->archive();

        expect(app(ResolveEducatableFromEmail::class)($email))->toBeNull();
    });
});
