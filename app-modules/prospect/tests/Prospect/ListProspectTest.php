<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Prospect\Models\Prospect;

use function Pest\Livewire\livewire;

use Assist\Prospect\Models\ProspectSource;
use Assist\Prospect\Models\ProspectStatus;
use Assist\Prospect\Filament\Resources\ProspectResource;

// TODO: Write ListProspects page test
//test('The correct details are displayed on the ListProspects page', function () {});

// TODO: Sorting and Searching tests

// Permission Tests

test('ListProspects is gated with proper access control', function () {
    $user = User::factory()->create();

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
    $user = User::factory()->create();

    $user->givePermissionTo('prospect.view-any');

    actingAs($user);

    $prospects = Prospect::factory()->count(3)->create();

    $component = livewire(ProspectResource\Pages\ListProspects::class);

    $component->assertCanSeeTableRecords($prospects)
        ->assertCountTableRecords($prospects->count())
        ->assertTableBulkActionExists('bulk_update');

    $source = ProspectSource::factory()->create();

    $status = ProspectStatus::factory()->create();

    $description = 'abc123';
    $hsgrad = '2000';

    $component
        ->callTableBulkAction('bulk_update', $prospects, [
            'field' => 'assigned_to_id',
            'assigned_to_id' => $user->id,
        ])
        ->assertHasNoTableBulkActionErrors()
        ->callTableBulkAction('bulk_update', $prospects, [
            'field' => 'description',
            'description' => $description,
        ])
        ->assertHasNoTableBulkActionErrors()
        ->callTableBulkAction('bulk_update', $prospects, [
            'field' => 'email_bounce',
            'email_bounce' => true,
        ])
        ->assertHasNoTableBulkActionErrors()
        ->callTableBulkAction('bulk_update', $prospects, [
            'field' => 'hsgrad',
            'hsgrad' => $hsgrad,
        ])
        ->assertHasNoTableBulkActionErrors()
        ->callTableBulkAction('bulk_update', $prospects, [
            'field' => 'sms_opt_out',
            'sms_opt_out' => true,
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
                ->assigned_to_id->toBe($user->id)
                ->description->toBe($description)
                ->email_bounce->toBeTrue()
                ->hsgrad->toBe($hsgrad)
                ->sms_opt_out->toBeTrue()
                ->source_id->toBe($source->id)
                ->status_id->toBe($status->id)
        );
});
