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
use AdvisingApp\Interaction\Enums\InteractableType;
use AdvisingApp\Interaction\Enums\InteractionManagementTab;
use AdvisingApp\Interaction\Livewire\InteractionDriversTable;
use AdvisingApp\Interaction\Livewire\InteractionInitiativesTable;
use AdvisingApp\Interaction\Livewire\InteractionOutcomesTable;
use AdvisingApp\Interaction\Livewire\InteractionRelationsTable;
use AdvisingApp\Interaction\Livewire\InteractionStatusesTable;
use AdvisingApp\Interaction\Livewire\InteractionTypesTable;
use AdvisingApp\Interaction\Models\InteractionDriver;
use AdvisingApp\Interaction\Models\InteractionInitiative;
use AdvisingApp\Interaction\Models\InteractionOutcome;
use AdvisingApp\Interaction\Models\InteractionRelation;
use AdvisingApp\Interaction\Models\InteractionStatus;
use AdvisingApp\Interaction\Models\InteractionType;
use AdvisingApp\Interaction\Settings\InteractionManagementSettings;
use App\Models\User;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Testing\TestAction;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertSoftDeleted;
use function Tests\asSuperAdmin;

$getComponent = fn (InteractionManagementTab $tab): string => match ($tab) {
    InteractionManagementTab::Initiatives => InteractionInitiativesTable::class,
    InteractionManagementTab::Drivers => InteractionDriversTable::class,
    InteractionManagementTab::Outcomes => InteractionOutcomesTable::class,
    InteractionManagementTab::Relations => InteractionRelationsTable::class,
    InteractionManagementTab::Statuses => InteractionStatusesTable::class,
    InteractionManagementTab::Types => InteractionTypesTable::class,
};

$createRecords = fn (InteractionManagementTab $tab, array $attributes = [], int $count = 1): Collection => match ($tab) {
    InteractionManagementTab::Initiatives => InteractionInitiative::factory()->count($count)->create($attributes),
    InteractionManagementTab::Drivers => InteractionDriver::factory()->count($count)->create($attributes),
    InteractionManagementTab::Outcomes => InteractionOutcome::factory()->count($count)->create($attributes),
    InteractionManagementTab::Relations => InteractionRelation::factory()->count($count)->create($attributes),
    InteractionManagementTab::Statuses => InteractionStatus::factory()->count($count)->create($attributes),
    InteractionManagementTab::Types => InteractionType::factory()->count($count)->create($attributes),
};

beforeEach(function (): void {
    $settings = app(InteractionManagementSettings::class);

    foreach (InteractionManagementTab::cases() as $tab) {
        $settings->{$tab->getSettingName('enabled')} = true;
        $settings->{$tab->getSettingName('required')} = true;
    }

    $settings->save();
});

it('is forbidden without the settings view permission', function (InteractionManagementTab $tab) use ($getComponent): void {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    Livewire::test($getComponent($tab))
        ->assertForbidden();

    $user->givePermissionTo('settings.view-any');

    Livewire::test($getComponent($tab))
        ->assertSuccessful();
})->with(InteractionManagementTab::cases());

it('lists the records and filters them by interactable type', function (InteractionManagementTab $tab) use ($getComponent, $createRecords): void {
    asSuperAdmin();

    $studentRecords = $createRecords($tab, ['interactable_type' => InteractableType::Student], 3);
    $prospectRecords = $createRecords($tab, ['interactable_type' => InteractableType::Prospect], 3);

    Livewire::test($getComponent($tab))
        ->assertCanSeeTableRecords($studentRecords->merge($prospectRecords))
        ->filterTable('interactable_type', InteractableType::Student->value)
        ->assertCanSeeTableRecords($studentRecords)
        ->assertCanNotSeeTableRecords($prospectRecords)
        ->filterTable('interactable_type', InteractableType::Prospect->value)
        ->assertCanSeeTableRecords($prospectRecords)
        ->assertCanNotSeeTableRecords($studentRecords);
})->with(InteractionManagementTab::cases());

it('gates the delete bulk action by the delete permission', function (InteractionManagementTab $tab) use ($getComponent): void {
    $user = User::factory()->licensed(LicenseType::cases())->create();
    $user->givePermissionTo('settings.view-any');

    actingAs($user);

    Livewire::test($getComponent($tab))
        ->assertTableBulkActionHidden(DeleteBulkAction::class);

    $user->givePermissionTo('settings.*.delete');

    Livewire::test($getComponent($tab))
        ->assertTableBulkActionVisible(DeleteBulkAction::class);
})->with(InteractionManagementTab::cases());

it('deletes the selected records with the delete bulk action', function (InteractionManagementTab $tab) use ($getComponent, $createRecords): void {
    $user = User::factory()->licensed(LicenseType::cases())->create();
    $user->givePermissionTo(['settings.view-any', 'settings.*.delete']);

    actingAs($user);

    $records = $createRecords($tab, [], 2);

    Livewire::test($getComponent($tab))
        ->callTableBulkAction(DeleteBulkAction::class, $records);

    foreach ($records as $record) {
        assertSoftDeleted($record);
    }
})->with(InteractionManagementTab::cases());

describe('view-only user', function () use ($getComponent, $createRecords): void {
    it('cannot create or edit records', function (InteractionManagementTab $tab) use ($getComponent, $createRecords): void {
        $user = User::factory()->licensed(LicenseType::cases())->create();
        $user->givePermissionTo('settings.view-any');

        actingAs($user);

        $record = $createRecords($tab)->first();

        Livewire::test($getComponent($tab))
            ->assertActionHidden(TestAction::make('create')->table())
            ->assertActionHidden(TestAction::make('edit')->table($record));
    })->with(InteractionManagementTab::cases());

    it('cannot change the settings', function (InteractionManagementTab $tab) use ($getComponent): void {
        $user = User::factory()->licensed(LicenseType::cases())->create();
        $user->givePermissionTo('settings.view-any');

        actingAs($user);

        Livewire::test($getComponent($tab))
            ->set("data.{$tab->getSettingName('enabled')}", false)
            ->assertForbidden();

        expect(app(InteractionManagementSettings::class)->{$tab->getSettingName('enabled')})->toBeTrue();
    })->with(InteractionManagementTab::cases());
});

describe('user who can create and update', function () use ($getComponent, $createRecords): void {
    it('links the create and edit actions to the resource pages', function (InteractionManagementTab $tab) use ($getComponent, $createRecords): void {
        $user = User::factory()->licensed(LicenseType::cases())->create();
        $user->givePermissionTo(['settings.view-any', 'settings.create', 'settings.*.update']);

        actingAs($user);

        $record = $createRecords($tab)->first();
        $resource = $tab->getResource();

        Livewire::test($getComponent($tab))
            ->assertActionVisible(TestAction::make('create')->table())
            ->assertActionHasUrl(TestAction::make('create')->table(), $resource::getUrl('create'))
            ->assertActionVisible(TestAction::make('edit')->table($record))
            ->assertActionHasUrl(TestAction::make('edit')->table($record), $resource::getUrl('edit', ['record' => $record]));
    })->with(InteractionManagementTab::cases());

    it('can change the settings', function (InteractionManagementTab $tab) use ($getComponent): void {
        $user = User::factory()->licensed(LicenseType::cases())->create();
        $user->givePermissionTo(['settings.view-any', 'settings.*.update']);

        actingAs($user);

        Livewire::test($getComponent($tab))
            ->assertSchemaStateSet([
                $tab->getSettingName('enabled') => true,
                $tab->getSettingName('required') => true,
            ], 'form')
            ->set("data.{$tab->getSettingName('required')}", false);

        $settings = app(InteractionManagementSettings::class);

        expect($settings->{$tab->getSettingName('required')})->toBeFalse()
            ->and($settings->{$tab->getSettingName('enabled')})->toBeTrue();
    })->with(InteractionManagementTab::cases());
});

it('shows a message instead of the table when the feature is disabled', function (InteractionManagementTab $tab) use ($getComponent, $createRecords): void {
    asSuperAdmin();

    $record = $createRecords($tab)->first();

    $settings = app(InteractionManagementSettings::class);
    $settings->{$tab->getSettingName('enabled')} = false;
    $settings->save();

    Livewire::test($getComponent($tab))
        ->assertSee("{$tab->getLabel()} are currently hidden from all interactions")
        ->assertDontSee($record->name);
})->with(InteractionManagementTab::cases());
