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
use AdvisingApp\Concern\Enums\SystemConcernStatusClassification;
use AdvisingApp\Concern\Models\Concern;
use AdvisingApp\Concern\Models\ConcernStatus;
use AdvisingApp\Notification\Models\Subscription;
use AdvisingApp\Prospect\Filament\Resources\Prospects\Pages\ListProspects;
use AdvisingApp\Prospect\Filament\Resources\Prospects\ProspectResource;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectSource;
use AdvisingApp\Prospect\Models\ProspectStatus;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

// TODO: Write ListProspects page test
//test('The correct details are displayed on the ListProspects page', function () {});

// TODO: Sorting and Searching tests

// Permission Tests

test('ListProspects is gated with proper access control', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    actingAs($user)
        ->get(
            ProspectResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('prospect.view-any');

    actingAs($user)
        ->get(
            ProspectResource::getUrl('index')
        )->assertSuccessful();
});

test('ListProspects can bulk update characteristics', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    $user->givePermissionTo('prospect.view-any');

    actingAs($user);

    $prospects = Prospect::factory()->count(3)->create();

    $component = livewire(ListProspects::class);

    $component->assertCanSeeTableRecords($prospects)
        ->assertCountTableRecords($prospects->count())
        ->assertTableBulkActionExists('bulk_update');

    $source = ProspectSource::factory()->create();

    $status = ProspectStatus::factory()->create();

    $description = 'abc123';
    $hsgrad = '2000';

    $component
        ->callTableBulkAction('bulk_update', $prospects, [
            'field' => 'description',
            'description' => $description,
        ])
        ->assertHasNoTableBulkActionErrors()
        ->callTableBulkAction('bulk_update', $prospects, [
            'field' => 'hsgrad',
            'hsgrad' => $hsgrad,
        ])
        ->assertHasNoTableBulkActionErrors()
        ->callTableBulkAction('bulk_update', $prospects, [
            'field' => 'source_id',
            'source_id' => $source->id,
        ])
        ->assertHasNoTableBulkActionErrors()
        ->callTableBulkAction('bulk_update', $prospects, [
            'field' => 'status_id',
            'status_id' => $status->id,
        ])
        ->assertHasNoTableBulkActionErrors();

    expect($prospects)
        ->each(
            fn ($prospect) => $prospect
                ->refresh()
                ->description->toBe($description)
                ->hsgrad->toBe($hsgrad)
                ->source_id->toBe($source->id)
                ->status_id->toBe($status->id)
        );
});

it('can filter prospects by `subscribed` prospects', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('prospect.view-any');
    $user->givePermissionTo('subscription.view-any');
    $user->givePermissionTo('subscription.*.view');

    actingAs($user);

    $subscribedProspects = Prospect::factory()
        ->count(3)
        ->has(
            Subscription::factory()->state(['user_id' => $user->getKey()]),
            'subscriptions'
        )
        ->create();

    $notSubscribedProspects = Prospect::factory()->count(3)->create();

    livewire(ListProspects::class)
        ->set('tableRecordsPerPage', 10)
        ->assertCanSeeTableRecords($notSubscribedProspects->merge($subscribedProspects))
        ->filterTable('subscribed')
        ->assertCanSeeTableRecords($subscribedProspects)
        ->assertCanNotSeeTableRecords($notSubscribedProspects);
});

it('can filter prospect by concerns', function () {
    asSuperAdmin();

    $activeStatusConcern = ConcernStatus::factory()
        ->state([
            'name' => 'Active',
            'classification' => SystemConcernStatusClassification::Active,
        ])
        ->create();

    $inprogressStatusConcern = ConcernStatus::factory()
        ->state([
            'name' => 'InProgress',
            'classification' => SystemConcernStatusClassification::Active,
        ])
        ->create();

    $prospectWithStatusActive = Prospect::factory()->create();

    $prospectWithStatusInprogress = Prospect::factory()->create();

    $activeConcerns = Concern::factory()
        ->count(3)
        ->for($prospectWithStatusActive, 'concern')
        ->state([
            'status_id' => $activeStatusConcern->getKey(),
        ])
        ->create();

    $inProgressConcerns = Concern::factory()
        ->count(2)
        ->for($prospectWithStatusInprogress, 'concern')
        ->state([
            'status_id' => $inprogressStatusConcern->getKey(),
        ])
        ->create();

    $prospectsWithoutConcerns = Prospect::factory()->count(5)->create();

    livewire(ListProspects::class)
        ->set('tableRecordsPerPage', 10)
        ->assertCanSeeTableRecords($prospectsWithoutConcerns->merge([$prospectWithStatusActive, $prospectWithStatusInprogress]))
        ->filterTable('concerns', [$activeStatusConcern, $inprogressStatusConcern])
        ->assertCanSeeTableRecords([$prospectWithStatusActive, $prospectWithStatusInprogress])
        ->assertCanNotSeeTableRecords($prospectsWithoutConcerns)
        ->resetTableFilters()
        ->filterTable('concerns', [$activeStatusConcern])
        ->assertCanSeeTableRecords([$prospectWithStatusActive])
        ->assertCanNotSeeTableRecords($prospectsWithoutConcerns->merge([$prospectWithStatusInprogress]))
        ->removeTableFilter('concerns')
        ->assertCanSeeTableRecords($prospectsWithoutConcerns->merge([$prospectWithStatusActive, $prospectWithStatusInprogress]));
});

it('renders the bulk create concern action based on proper access', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    $user->givePermissionTo('prospect.view-any');
    $user->givePermissionTo('prospect.*.view');

    actingAs($user);

    livewire(ListProspects::class)
        ->assertOk()
        ->assertTableBulkActionHidden('createConcern');

    $user->givePermissionTo('concern.create');
    $user->givePermissionTo('prospect.*.update');

    $user->refresh();

    livewire(ListProspects::class)
        ->assertOk()
        ->assertTableBulkActionVisible('createConcern');
});

it('shows bulk assign tags action for authorized user', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    $user->givePermissionTo('prospect.view-any');
    $user->givePermissionTo('prospect.create');

    actingAs($user);

    $prospects = Prospect::factory()->count(5)->create();

    livewire(ListProspects::class)
        ->assertCanSeeTableRecords($prospects)
        ->assertTableBulkActionHidden('bulkProspectTags');

    $user->givePermissionTo('prospect.*.update');

    livewire(ListProspects::class)
        ->assertCanSeeTableRecords($prospects)
        ->assertTableBulkActionVisible('bulkProspectTags');
});

it('renders the bulk create interaction action based on proper access', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    $user->givePermissionTo('prospect.view-any');
    $user->givePermissionTo('prospect.*.view');

    actingAs($user);

    livewire(ListProspects::class)
        ->assertOk()
        ->assertTableBulkActionHidden('createInteraction');

    $user->givePermissionTo('prospect.*.update');

    livewire(ListProspects::class)
        ->assertOk()
        ->assertTableBulkActionVisible('createInteraction');
});

it('shows bulk subscription action for authorized user', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    $user->givePermissionTo('prospect.view-any');
    $user->givePermissionTo('prospect.create');

    actingAs($user);

    livewire(ListProspects::class)
        ->assertOk()
        ->assertTableBulkActionHidden('bulkSubscription');

    $user->givePermissionTo('prospect.*.update');

    $prospects = Prospect::factory()->count(5)->create();

    livewire(ListProspects::class)
        ->assertCanSeeTableRecords($prospects)
        ->assertTableBulkActionVisible('bulkSubscription')
        ->assertSuccessful();
});

it('renders the bulk create case action based on proper access', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    $user->givePermissionTo('prospect.view-any');
    $user->givePermissionTo('prospect.*.view');

    actingAs($user);

    livewire(ListProspects::class)
        ->assertOk()
        ->assertTableBulkActionHidden('createCase');

    $user->givePermissionTo('prospect.*.update');

    livewire(ListProspects::class)
        ->assertOk()
        ->assertTableBulkActionVisible('createCase');
});
