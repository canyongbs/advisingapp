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

use AdvisingApp\StudentDataModel\Enums\SisSystem;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\Pages\ViewStudent;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\RelationManagers\TermAttributesRelationManager;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentTermAttribute;
use AdvisingApp\StudentDataModel\Models\Term;
use AdvisingApp\StudentDataModel\Settings\StudentInformationSystemSettings;
use App\Features\TermAttributesFeature;
use App\Models\User;
use Filament\Tables\Filters\SelectFilter;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

beforeEach(function () {
    asSuperAdmin();

    $sisSettings = app(StudentInformationSystemSettings::class);
    $sisSettings->is_enabled = true;
    $sisSettings->sis_system = SisSystem::ThesisElements;
    $sisSettings->save();
});

$createTermAttribute = function (Student $student, Term $term): StudentTermAttribute {
    StudentTermAttribute::factory()->for($student)->create(['sis_term_id' => $term->sis_term_id]);

    return $student->termAttributes()->where('sis_term_id', $term->sis_term_id)->sole();
};

it('shows the attributes for the latest term by default', function () use ($createTermAttribute) {
    $student = Student::factory()->create();

    $olderTermAttribute = $createTermAttribute($student, Term::factory()->create(['start_date' => '2020-01-01']));
    $latestTermAttribute = $createTermAttribute($student, Term::factory()->create(['start_date' => '2021-08-23']));

    livewire(TermAttributesRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertCanSeeTableRecords([$latestTermAttribute])
        ->assertCanNotSeeTableRecords([$olderTermAttribute]);
});

it('shows the attributes for the selected term', function () use ($createTermAttribute) {
    $student = Student::factory()->create();

    $olderTerm = Term::factory()->create(['start_date' => '2020-01-01']);

    $olderTermAttribute = $createTermAttribute($student, $olderTerm);
    $latestTermAttribute = $createTermAttribute($student, Term::factory()->create(['start_date' => '2021-08-23']));

    livewire(TermAttributesRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->filterTable('sis_term_id', $olderTerm->sis_term_id)
        ->assertCanSeeTableRecords([$olderTermAttribute])
        ->assertCanNotSeeTableRecords([$latestTermAttribute]);
});

it('only offers the terms the student has attributes for, latest first', function () use ($createTermAttribute) {
    $student = Student::factory()->create();

    $undatedTerm = Term::factory()->create(['sis_term_id' => '302', 'name' => 'Summer TBD', 'start_date' => null]);
    $olderTerm = Term::factory()->create(['sis_term_id' => '268', 'name' => 'FA-20', 'start_date' => '2020-08-24']);
    $latestTerm = Term::factory()->create(['sis_term_id' => '300', 'name' => 'FA-20', 'start_date' => '2020-09-14']);

    $createTermAttribute($student, $undatedTerm);
    $createTermAttribute($student, $olderTerm);
    $createTermAttribute($student, $latestTerm);

    $otherStudentTerm = Term::factory()->create(['start_date' => '2022-01-10']);
    $createTermAttribute(Student::factory()->create(), $otherStudentTerm);

    Term::factory()->create(['start_date' => '2023-01-10']);

    $filter = livewire(TermAttributesRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->instance()
        ->getTable()
        ->getFilter('sis_term_id');

    assert($filter instanceof SelectFilter);

    $options = $filter->getOptions();

    expect(array_map(strval(...), array_keys($options)))->toBe(['300', '268', '302'])
        ->and(array_values($options))->toBe(['FA-20 (Sep 2020)', 'FA-20 (Aug 2020)', 'Summer TBD']);
});

it('shows no term attributes when the student has none', function () {
    StudentTermAttribute::factory()->create();

    livewire(TermAttributesRelationManager::class, [
        'ownerRecord' => Student::factory()->create(),
        'pageClass' => ViewStudent::class,
    ])
        ->assertCountTableRecords(0);
});

describe('visibility', function () {
    it('is visible for tenants using Thesis Elements', function () {
        expect(TermAttributesRelationManager::canViewForRecord(Student::factory()->create(), ViewStudent::class))->toBeTrue();
    });

    it('is hidden for tenants not using Thesis Elements', function (bool $isEnabled, ?SisSystem $sisSystem) {
        $sisSettings = app(StudentInformationSystemSettings::class);
        $sisSettings->is_enabled = $isEnabled;
        $sisSettings->sis_system = $sisSystem;
        $sisSettings->save();

        expect(TermAttributesRelationManager::canViewForRecord(Student::factory()->create(), ViewStudent::class))->toBeFalse();
    })->with([
        'Ellucian Ethos' => [true, SisSystem::EllucianEthos],
        'SIS disabled' => [false, SisSystem::ThesisElements],
    ]);

    it('is hidden while `TermAttributesFeature` is inactive', function () {
        TermAttributesFeature::deactivate();

        expect(TermAttributesRelationManager::canViewForRecord(Student::factory()->create(), ViewStudent::class))->toBeFalse();
    });
});

describe('authorization', function () {
    it('is hidden without the `enrollment.view-any` permission', function () {
        actingAs(User::factory()->licensed(Student::getLicenseType())->create());

        expect(TermAttributesRelationManager::canViewForRecord(Student::factory()->create(), ViewStudent::class))->toBeFalse();
    });

    it('is visible with the `enrollment.view-any` permission', function () {
        $user = User::factory()->licensed(Student::getLicenseType())->create();
        $user->givePermissionTo('enrollment.view-any');

        actingAs($user);

        expect(TermAttributesRelationManager::canViewForRecord(Student::factory()->create(), ViewStudent::class))->toBeTrue();
    });
});
