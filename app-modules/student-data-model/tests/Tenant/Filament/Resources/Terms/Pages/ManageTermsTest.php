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

use AdvisingApp\StudentDataModel\Filament\Resources\Terms\Pages\ManageTerms;
use AdvisingApp\StudentDataModel\Filament\Resources\Terms\TermResource;
use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\Term;
use App\Features\TermsConsolidationFeature;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertModelMissing;
use function Tests\asSuperAdmin;

describe('manually maintained terms', function (): void {
    beforeEach(function (): void {
        asSuperAdmin();
    });

    it('lists the semesters by their order, excluding deleted ones', function (): void {
        $firstTerm = Term::factory()->withoutSisTermId()->create(['order' => 1]);
        $thirdTerm = Term::factory()->withoutSisTermId()->create(['order' => 3]);
        $secondTerm = Term::factory()->withoutSisTermId()->create(['order' => 2]);
        $deletedTerm = Term::factory()->withoutSisTermId()->create(['order' => 4, 'deleted_at' => now()]);

        Livewire::test(ManageTerms::class)
            ->assertCanSeeTableRecords([$thirdTerm, $secondTerm, $firstTerm], inOrder: true)
            ->assertCanNotSeeTableRecords([$deletedTerm]);
    });

    it('can create a semester', function (): void {
        Livewire::test(ManageTerms::class)
            ->callAction(CreateAction::class, ['name' => 'Fall 2024'])
            ->assertHasNoActionErrors();

        assertDatabaseHas(Term::class, ['name' => 'Fall 2024', 'sis_term_id' => null]);
    });

    it('can create a semester with the same name as another semester', function (): void {
        Term::factory()->withoutSisTermId()->create(['name' => 'Fall 2024']);

        Livewire::test(ManageTerms::class)
            ->callAction(CreateAction::class, ['name' => 'Fall 2024'])
            ->assertHasNoActionErrors();

        expect(Term::query()->where('name', 'Fall 2024')->count())->toBe(2);
    });

    it('suggests the semester names of enrollments that have no semester', function (): void {
        Term::factory()->withoutSisTermId()->create(['name' => 'Fall 2024']);

        Enrollment::factory()->create(['semester_name' => 'FALL 2024']);
        Enrollment::factory()->create(['semester_name' => 'Spring 2025']);
        Enrollment::factory()->create(['semester_name' => 'Summer 2025', 'deleted_at' => now()]);

        Livewire::test(ManageTerms::class)
            ->mountAction(CreateAction::class)
            ->assertSchemaComponentExists('name', checkComponentUsing: fn (TextInput $input): bool => $input->getDatalistOptions() === ['Spring 2025']);
    });

    it('can update a semester', function (): void {
        $term = Term::factory()->withoutSisTermId()->create(['name' => 'Fall 2024']);

        Livewire::test(ManageTerms::class)
            ->callTableAction(EditAction::class, $term, ['name' => 'Fall 2024 Cohort'])
            ->assertHasNoTableActionErrors();

        expect($term->refresh()->name)->toBe('Fall 2024 Cohort');
    });

    it('can delete a semester', function (): void {
        $term = Term::factory()->withoutSisTermId()->create();

        Livewire::test(ManageTerms::class)
            ->callTableAction(DeleteAction::class, $term);

        assertModelMissing($term);
    });

    it('shows a semester as mapped when an enrollment has its name, ignoring case', function (): void {
        $term = Term::factory()->withoutSisTermId()->create(['name' => 'Fall 2024']);

        Enrollment::factory()->create(['semester_name' => 'FALL 2024']);

        Livewire::test(ManageTerms::class)
            ->assertTableColumnStateSet('is_mapped', true, record: $term);
    });

    it('shows a semester as unmapped when only deleted enrollments have its name', function (): void {
        $term = Term::factory()->withoutSisTermId()->create(['name' => 'Fall 2024']);

        Enrollment::factory()->create(['semester_name' => 'Fall 2024', 'deleted_at' => now()]);

        Livewire::test(ManageTerms::class)
            ->assertTableColumnStateSet('is_mapped', false, record: $term);
    });
});

describe('terms synced from the SIS', function (): void {
    beforeEach(function (): void {
        asSuperAdmin();
    });

    it('lists the semesters by start date, most recent first, excluding deleted ones', function (): void {
        $olderTerm = Term::factory()->create(['start_date' => '2020-09-01']);
        $recentTerm = Term::factory()->create(['start_date' => '2021-09-01']);
        $deletedTerm = Term::factory()->create(['start_date' => '2022-09-01', 'deleted_at' => now()]);

        Livewire::test(ManageTerms::class)
            ->assertCanSeeTableRecords([$recentTerm, $olderTerm], inOrder: true)
            ->assertCanNotSeeTableRecords([$deletedTerm]);
    });

    it('does not allow semesters to be added, updated or deleted', function (): void {
        Term::factory()->create();

        Livewire::test(ManageTerms::class)
            ->assertActionHidden(CreateAction::class)
            ->assertTableActionDoesNotExist(EditAction::class)
            ->assertTableActionDoesNotExist(DeleteAction::class);
    });
});

describe('authorization', function (): void {
    it('denies access to the page without the `settings.view-any` permission', function (): void {
        actingAs(User::factory()->create())
            ->get(TermResource::getUrl('index'))
            ->assertForbidden();
    });

    it('allows access to the page with the `settings.view-any` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');

        actingAs($user)
            ->get(TermResource::getUrl('index'))
            ->assertSuccessful();
    });

    it('denies access to the page while the `TermsConsolidationFeature` is inactive', function (): void {
        TermsConsolidationFeature::deactivate();

        asSuperAdmin()
            ->get(TermResource::getUrl('index'))
            ->assertForbidden();
    });

    it('hides the `New` action without the `settings.create` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');

        actingAs($user);

        Livewire::test(ManageTerms::class)
            ->assertActionHidden(CreateAction::class);

        $user->givePermissionTo('settings.create');

        Livewire::test(ManageTerms::class)
            ->assertActionVisible(CreateAction::class);
    });
});
