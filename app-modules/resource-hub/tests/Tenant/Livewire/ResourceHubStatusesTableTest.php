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
use AdvisingApp\ResourceHub\Filament\Resources\ResourceHubStatuses\ResourceHubStatusResource;
use AdvisingApp\ResourceHub\Livewire\ResourceHubStatusesTable;
use AdvisingApp\ResourceHub\Models\ResourceHubArticle;
use AdvisingApp\ResourceHub\Models\ResourceHubStatus;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Actions\DeleteBulkAction;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

$viewer = function (): User {
    $user = User::factory()->licensed(LicenseType::cases())->create();
    $user->givePermissionTo('settings.view-any');

    actingAs($user);

    return $user;
};

it('lists records', function () use ($viewer): void {
    $viewer();

    $records = ResourceHubStatus::factory()->count(3)->create();

    Livewire::test(ResourceHubStatusesTable::class)
        ->assertCanSeeTableRecords($records);
});

it('can search records by name', function () use ($viewer): void {
    $viewer();

    $match = ResourceHubStatus::factory()->create(['name' => 'Findable Name']);
    $other = ResourceHubStatus::factory()->create(['name' => 'Something Else']);

    Livewire::test(ResourceHubStatusesTable::class)
        ->searchTable('Findable')
        ->assertCanSeeTableRecords([$match])
        ->assertCanNotSeeTableRecords([$other]);
});

it('links the actions to the resource pages', function () use ($viewer): void {
    $user = $viewer();
    $user->givePermissionTo(['settings.create', 'settings.*.view', 'settings.*.update']);

    $record = ResourceHubStatus::factory()->create();

    Livewire::test(ResourceHubStatusesTable::class)
        ->assertTableActionHasUrl('create', ResourceHubStatusResource::getUrl('create'))
        ->assertTableActionHasUrl('view', ResourceHubStatusResource::getUrl('view', ['record' => $record]), record: $record)
        ->assertTableActionHasUrl('edit', ResourceHubStatusResource::getUrl('edit', ['record' => $record]), record: $record);
});

it('forbids mounting without the `settings.view-any` permission', function (): void {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    Livewire::test(ResourceHubStatusesTable::class)
        ->assertForbidden();
});

it('forbids mounting when the resource hub feature is disabled', function () use ($viewer): void {
    $viewer();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->resourceHub = false;
    $settings->save();

    Livewire::test(ResourceHubStatusesTable::class)
        ->assertForbidden();
});

it('forbids mounting without a recruitment or retention license', function (): void {
    $user = User::factory()->create();
    $user->givePermissionTo('settings.view-any');

    actingAs($user);

    Livewire::test(ResourceHubStatusesTable::class)
        ->assertForbidden();
});

describe('authorization', function () use ($viewer): void {
    it('only allows the create action with the `settings.create` permission', function () use ($viewer): void {
        $user = $viewer();

        Livewire::test(ResourceHubStatusesTable::class)
            ->assertTableActionHidden('create');

        $user->givePermissionTo('settings.create');

        Livewire::test(ResourceHubStatusesTable::class)
            ->assertTableActionVisible('create');
    });

    it('only allows the view action with the `settings.*.view` permission', function () use ($viewer): void {
        $user = $viewer();

        $record = ResourceHubStatus::factory()->create();

        Livewire::test(ResourceHubStatusesTable::class)
            ->assertTableActionHidden('view', $record);

        $user->givePermissionTo('settings.*.view');

        Livewire::test(ResourceHubStatusesTable::class)
            ->assertTableActionVisible('view', $record);
    });

    it('only allows the edit action with the `settings.*.update` permission', function () use ($viewer): void {
        $user = $viewer();

        $record = ResourceHubStatus::factory()->create();

        Livewire::test(ResourceHubStatusesTable::class)
            ->assertTableActionHidden('edit', $record);

        $user->givePermissionTo('settings.*.update');

        Livewire::test(ResourceHubStatusesTable::class)
            ->assertTableActionVisible('edit', $record);
    });

    it('only shows the delete bulk action with the `settings.*.delete` permission', function () use ($viewer): void {
        $user = $viewer();

        Livewire::test(ResourceHubStatusesTable::class)
            ->assertTableBulkActionHidden(DeleteBulkAction::class);

        $user->givePermissionTo('settings.*.delete');

        Livewire::test(ResourceHubStatusesTable::class)
            ->assertTableBulkActionVisible(DeleteBulkAction::class);
    });

    it('deletes records through the bulk action with the `settings.*.delete` permission', function () use ($viewer): void {
        $user = $viewer();
        $user->givePermissionTo('settings.*.delete');

        $records = ResourceHubStatus::factory()->count(2)->create();

        Livewire::test(ResourceHubStatusesTable::class)
            ->callTableBulkAction(DeleteBulkAction::class, $records);

        expect(ResourceHubStatus::count())->toBe(0);
    });

    it('does not link rows to a page without the `settings.*.view` or `settings.*.update` permission', function () use ($viewer): void {
        $user = $viewer();

        $record = ResourceHubStatus::factory()->create();

        Livewire::test(ResourceHubStatusesTable::class)
            ->assertCanSeeTableRecords([$record])
            ->assertDontSeeHtml(ResourceHubStatusResource::getUrl('view', ['record' => $record]))
            ->assertDontSeeHtml(ResourceHubStatusResource::getUrl('edit', ['record' => $record]));

        $user->givePermissionTo('settings.*.view');

        Livewire::test(ResourceHubStatusesTable::class)
            ->assertSeeHtml(ResourceHubStatusResource::getUrl('view', ['record' => $record]));
    });

    it('does not delete records that still have articles through the bulk action', function () use ($viewer): void {
        $user = $viewer();
        $user->givePermissionTo('settings.*.delete');

        $record = ResourceHubStatus::factory()->create();
        ResourceHubArticle::factory()->create(['status_id' => $record->getKey()]);

        Livewire::test(ResourceHubStatusesTable::class)
            ->callTableBulkAction(DeleteBulkAction::class, [$record]);

        expect(ResourceHubStatus::find($record->getKey()))->not->toBeNull();
    });
});
