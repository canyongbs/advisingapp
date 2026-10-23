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

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Form\Filament\Resources\Forms\Pages\ManageForm;
use AdvisingApp\Form\Filament\Resources\Forms\RelationManagers\SubmissionsRelationManager;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormSubmission;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use App\Settings\LicenseSettings;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Tests\asSuperAdmin;

$manageFormSubmissionsTestUser = function (): User {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->onlineForms = true;
    $settings->save();

    return User::factory()->licensed(LicenseType::cases())->create();
};

$testSubmissions = fn (Form $form): Testable => Livewire::test(SubmissionsRelationManager::class, [
    'ownerRecord' => $form,
    'pageClass' => ManageForm::class,
]);

test('archive action is visible when submission is not archived', function () use ($testSubmissions): void {
    asSuperAdmin();

    $form = Form::factory()->create();
    $submission = FormSubmission::factory()->create(['form_id' => $form->id]);

    $testSubmissions($form)
        ->assertTableActionVisible('archive', $submission);
});

test('archive action successfully archives a submission', function () use ($testSubmissions): void {
    asSuperAdmin();

    $form = Form::factory()->create();
    $submission = FormSubmission::factory()->create(['form_id' => $form->id]);

    expect($submission->isArchived())->toBeFalse();

    $testSubmissions($form)
        ->callTableAction('archive', $submission)
        ->assertNotified();

    assertDatabaseHas(FormSubmission::class, [
        'id' => $submission->id,
        'archived_at' => $submission->fresh()->archived_at,
    ]);

    expect($submission->fresh()->isArchived())->toBeTrue();
});

test('bulk archive action successfully archives multiple submissions', function () use ($testSubmissions): void {
    asSuperAdmin();

    $form = Form::factory()->create();
    $submissions = FormSubmission::factory()->count(3)->create(['form_id' => $form->id]);

    $submissions->each(function (FormSubmission $submission): void {
        expect($submission->isArchived())->toBeFalse();
    });

    $testSubmissions($form)
        ->callTableBulkAction('archive', $submissions)
        ->assertNotified();

    $submissions->each(function (FormSubmission $submission): void {
        expect($submission->fresh()->isArchived())->toBeTrue();
    });
});

test('archived submissions are hidden by default', function () use ($testSubmissions): void {
    asSuperAdmin();

    $form = Form::factory()->create();
    $activeSubmission = FormSubmission::factory()->create(['form_id' => $form->id]);
    $archivedSubmission = FormSubmission::factory()->create(['form_id' => $form->id, 'archived_at' => now()]);

    $testSubmissions($form)
        ->assertCanSeeTableRecords([$activeSubmission])
        ->assertCanNotSeeTableRecords([$archivedSubmission]);
});

test('archived submissions are visible when the withoutArchived filter is removed', function () use ($testSubmissions): void {
    asSuperAdmin();

    $form = Form::factory()->create();
    $activeSubmission = FormSubmission::factory()->create(['form_id' => $form->id]);
    $archivedSubmission = FormSubmission::factory()->create(['form_id' => $form->id, 'archived_at' => now()]);

    $testSubmissions($form)
        ->removeTableFilter('withoutArchived')
        ->assertCanSeeTableRecords([$activeSubmission, $archivedSubmission]);
});

test('archiving submissions refreshes the page so the tab badge updates', function (bool $isBulk) use ($testSubmissions): void {
    asSuperAdmin();

    $form = Form::factory()->create();
    $submission = FormSubmission::factory()->create(['form_id' => $form->id]);

    $component = $testSubmissions($form);

    $isBulk
        ? $component->callTableBulkAction('archive', [$submission])
        : $component->callTableAction('archive', $submission);

    $component->assertDispatched('refresh-page');
})->with([
    'single' => false,
    'bulk' => true,
]);

describe('submission links', function (): void {
    it('applies filters from the URL', function (): void {
        asSuperAdmin();

        $form = Form::factory()->create();
        $archivedSubmission = FormSubmission::factory()->create(['form_id' => $form->id, 'archived_at' => now()]);

        Livewire::withQueryParams(['filters' => ['withoutArchived' => ['isActive' => false]]])
            ->test(SubmissionsRelationManager::class, ['ownerRecord' => $form, 'pageClass' => ManageForm::class])
            ->assertCanSeeTableRecords([$archivedSubmission]);
    });

    it('applies search from the URL', function (string $authorClass): void {
        asSuperAdmin();

        $form = Form::factory()->create();
        $author = $authorClass::factory()->create();
        $submission = FormSubmission::factory()->for($author, 'author')->create(['form_id' => $form->id]);
        $otherSubmission = FormSubmission::factory()->create(['form_id' => $form->id]);

        Livewire::withQueryParams(['search' => strtoupper($author->primaryEmailAddress->address)])
            ->test(SubmissionsRelationManager::class, ['ownerRecord' => $form, 'pageClass' => ManageForm::class])
            ->assertCanSeeTableRecords([$submission])
            ->assertCanNotSeeTableRecords([$otherSubmission]);
    })->with(['student' => Student::class, 'prospect' => Prospect::class]);

    it('applies sorting from the URL', function (string $direction): void {
        asSuperAdmin();

        $form = Form::factory()->create();
        $olderSubmission = FormSubmission::factory()->create(['form_id' => $form->id, 'submitted_at' => now()->subDay()]);
        $newerSubmission = FormSubmission::factory()->create(['form_id' => $form->id, 'submitted_at' => now()]);

        Livewire::withQueryParams(['sort' => 'submitted_at:' . $direction])
            ->test(SubmissionsRelationManager::class, ['ownerRecord' => $form, 'pageClass' => ManageForm::class])
            ->assertCanSeeTableRecords(
                $direction === 'asc' ? [$olderSubmission, $newerSubmission] : [$newerSubmission, $olderSubmission],
                inOrder: true,
            );
    })->with(['ascending' => 'asc', 'descending' => 'desc']);

    $linkTo = fn (FormSubmission $submission, array $query = []): array => [
        'actionComponent' => 'submissions',
        'tableAction' => 'view',
        'tableActionRecord' => $submission->getKey(),
        ...$query,
    ];

    it('opens the linked submission and removes the link once it is open', function () use ($linkTo): void {
        asSuperAdmin();

        $form = Form::factory()->create();
        $submission = FormSubmission::factory()->create(['form_id' => $form->id, 'submitted_at' => now()]);

        Livewire::withQueryParams($linkTo($submission))
            ->test(SubmissionsRelationManager::class, ['ownerRecord' => $form, 'pageClass' => ManageForm::class])
            ->call('mountDefaultActions')
            ->assertSet('mountedActions.0.name', 'view')
            ->assertSet('mountedActions.0.context.recordKey', $submission->getKey())
            ->assertSet('defaultActionComponent', null)
            ->assertSet('defaultTableAction', null)
            ->assertSet('defaultTableActionRecord', null);
    });

    it('ignores links that target another component', function (?string $actionComponent) use ($linkTo): void {
        asSuperAdmin();

        $form = Form::factory()->create();
        $submission = FormSubmission::factory()->create(['form_id' => $form->id, 'submitted_at' => now()]);

        Livewire::withQueryParams($linkTo($submission, ['actionComponent' => $actionComponent]))
            ->test(SubmissionsRelationManager::class, ['ownerRecord' => $form, 'pageClass' => ManageForm::class])
            ->call('mountDefaultActions')
            ->assertSet('mountedActions', []);
    })->with([
        'unqualified' => null,
        'page' => 'page',
    ]);

    it('does not open a submission from another form', function () use ($linkTo): void {
        asSuperAdmin();

        $form = Form::factory()->create();
        $foreignSubmission = FormSubmission::factory()->create(['form_id' => Form::factory()->create()->id, 'submitted_at' => now()]);

        Livewire::withQueryParams($linkTo($foreignSubmission))
            ->test(SubmissionsRelationManager::class, ['ownerRecord' => $form, 'pageClass' => ManageForm::class])
            ->call('mountDefaultActions')
            ->assertSet('mountedActions', []);
    });
});

describe('authorization', function () use ($testSubmissions, $manageFormSubmissionsTestUser): void {
    it('denies access to the submissions without the `form.view-any` permission', function () use ($testSubmissions, $manageFormSubmissionsTestUser): void {
        actingAs($manageFormSubmissionsTestUser());

        $testSubmissions(Form::factory()->create())->assertForbidden();
    });

    it('does not archive submissions without the `form.*.update` permission', function (bool $isBulk) use ($testSubmissions, $manageFormSubmissionsTestUser): void {
        $user = $manageFormSubmissionsTestUser();
        $user->givePermissionTo('form.view-any', 'form.*.view');
        actingAs($user);

        $form = Form::factory()->create();
        $submission = FormSubmission::factory()->create(['form_id' => $form->id]);

        $component = $testSubmissions($form);

        if ($isBulk) {
            $component->selectTableRecords([$submission->getKey()]);
        }

        $component
            ->call('mountAction', 'archive', [], $isBulk ? ['table' => true, 'bulk' => true] : ['table' => true, 'recordKey' => $submission->getKey()])
            ->call('callMountedAction');

        expect($submission->fresh()->isArchived())->toBeFalse();
    })->with([
        'single' => false,
        'bulk' => true,
    ]);

    it('archives submissions with the `form.*.update` permission', function () use ($testSubmissions, $manageFormSubmissionsTestUser): void {
        $user = $manageFormSubmissionsTestUser();
        $user->givePermissionTo('form.view-any', 'form.*.view', 'form.*.update');
        actingAs($user);

        $form = Form::factory()->create();
        $submission = FormSubmission::factory()->create(['form_id' => $form->id]);

        $testSubmissions($form)->callTableAction('archive', $submission);

        expect($submission->fresh()->isArchived())->toBeTrue();
    });
});
